<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Monthly Premium Price
    |--------------------------------------------------------------------------
    */

    private const MONTHLY_PRICE = 2.00;

    /*
    |--------------------------------------------------------------------------
    | Check Admin
    |--------------------------------------------------------------------------
    */

    private function checkAdmin(): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'You are not logged in.');
        }

        $adminEmail = strtolower(trim((string) config('app.admin_email')));

        if ($adminEmail === '' || strtolower(trim($user->email)) !== $adminEmail) {
            abort(403, 'Unauthorized.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Requests
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $this->checkAdmin();

        $payments = PaymentRequest::with('user')
            ->latest()
            ->paginate(20);

        return view(
            'admin.payments',
            compact('payments')
        );
    }

    public function downloadProof(PaymentRequest $payment): StreamedResponse
    {
        $this->checkAdmin();

        if (! $payment->proof_path || ! Storage::disk('local')->exists($payment->proof_path)) {
            abort(404);
        }

        $extension = pathinfo($payment->proof_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download(
            $payment->proof_path,
            'payment-proof-'.$payment->id.'.'.$extension
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approve Payment
    |--------------------------------------------------------------------------
    |
    | $2 payment = 1 month premium.
    |
    | If user has no active premium:
    |
    | Now + 1 month
    |
    | If user already has active premium:
    |
    | Existing expiry + 1 month
    |
    */

    public function approve(PaymentRequest $payment)
    {
        $this->checkAdmin();

        try {

            DB::transaction(function () use ($payment) {

                /*
                |--------------------------------------------------------------------------
                | Lock Payment Request
                |--------------------------------------------------------------------------
                |
                | Prevent duplicate approval if two requests happen
                | at the same time.
                |
                */

                $lockedPayment = PaymentRequest::query()
                    ->where('id', $payment->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedPayment) {
                    throw new \RuntimeException(
                        'Payment request not found.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Already Processed Check
                |--------------------------------------------------------------------------
                */

                if ($lockedPayment->status !== 'pending') {
                    throw new \RuntimeException(
                        'This payment request has already been processed.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Check Payment Amount
                |--------------------------------------------------------------------------
                |
                | Your new subscription price is $2/month.
                |
                */

                if (
                    (float) $lockedPayment->amount
                    !== self::MONTHLY_PRICE
                ) {

                    throw new \RuntimeException(
                        'Invalid payment amount. Expected $2.00.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Find User
                |--------------------------------------------------------------------------
                */

                $user = $lockedPayment->user;

                if (! $user) {
                    throw new \RuntimeException(
                        'The user associated with this payment could not be found.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Lock User Row
                |--------------------------------------------------------------------------
                */

                $lockedUser = $user->newQuery()
                    ->where('id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedUser) {
                    throw new \RuntimeException(
                        'User account could not be found.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Current Time
                |--------------------------------------------------------------------------
                */

                $now = Carbon::now();

                /*
                |--------------------------------------------------------------------------
                | Calculate Premium Expiry
                |--------------------------------------------------------------------------
                |
                | Existing active premium:
                |
                | 20 Oct expiry
                | + 1 month
                | = 20 Nov
                |
                | No active premium:
                |
                | Today + 1 month
                |
                */

                if (
                    $lockedUser->premium_expires_at !== null &&
                    $lockedUser->premium_expires_at->isFuture()
                ) {

                    $newExpiry = $lockedUser
                        ->premium_expires_at
                        ->copy()
                        ->addMonth();

                } else {

                    $newExpiry = $now
                        ->copy()
                        ->addMonth();
                }

                /*
                |--------------------------------------------------------------------------
                | Activate Monthly Premium
                |--------------------------------------------------------------------------
                */

                $lockedUser->premium_expires_at = $newExpiry;

                /*
                |--------------------------------------------------------------------------
                | Optional Old Premium Fields
                |--------------------------------------------------------------------------
                |
                | Your old system used:
                |
                | pdf_premium
                | pdf_premium_at
                |
                | If these columns still exist, we keep them synchronized.
                |
                | This means old parts of your application won't break.
                |
                */

                $lockedUser->pdf_premium = true;

                $lockedUser->pdf_premium_at = $now;

                /*
                |--------------------------------------------------------------------------
                | Save User
                |--------------------------------------------------------------------------
                */

                $lockedUser->save();

                /*
                |--------------------------------------------------------------------------
                | Approve Payment Request
                |--------------------------------------------------------------------------
                */

                $lockedPayment->status = 'approved';

                $lockedPayment->approved_at = $now;

                $lockedPayment->rejected_at = null;

                $lockedPayment->admin_note =
                    'Payment approved by admin. Premium active until '.
                    $newExpiry->format('Y-m-d H:i:s').
                    '.';

                $lockedPayment->save();
            });

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return back()->with(
                'success',
                'Payment approved. User premium access is active for one month.'
            );

        } catch (\RuntimeException $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Log Unexpected Error
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Admin payment approval failed.',
                [
                    'payment_id' => $payment->id,
                    'admin_id' => Auth::id(),
                    'error' => $e->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'Unable to approve this payment. Please try again.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reject Payment
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        PaymentRequest $payment
    ) {

        $this->checkAdmin();

        /*
        |--------------------------------------------------------------------------
        | Validate Admin Note
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'admin_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        try {

            DB::transaction(function () use (
                $request,
                $payment
            ) {

                /*
                |--------------------------------------------------------------------------
                | Lock Payment
                |--------------------------------------------------------------------------
                */

                $lockedPayment = PaymentRequest::query()
                    ->where('id', $payment->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedPayment) {
                    throw new \RuntimeException(
                        'Payment request not found.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Already Processed
                |--------------------------------------------------------------------------
                */

                if ($lockedPayment->status !== 'pending') {

                    throw new \RuntimeException(
                        'This payment request has already been processed.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Reject Payment
                |--------------------------------------------------------------------------
                */

                $lockedPayment->status = 'rejected';

                $lockedPayment->rejected_at = now();

                $lockedPayment->approved_at = null;

                $lockedPayment->admin_note =
                    $request->admin_note
                    ?: 'Payment rejected by admin.';

                $lockedPayment->save();
            });

            return back()->with(
                'success',
                'Payment request rejected successfully.'
            );

        } catch (\RuntimeException $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Log Error
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Admin payment rejection failed.',
                [
                    'payment_id' => $payment->id,
                    'admin_id' => Auth::id(),
                    'error' => $e->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'Unable to reject this payment. Please try again.'
            );
        }
    }
}
