<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdfUsageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Global PDF Usage
    |--------------------------------------------------------------------------
    |
    | Every normal user gets exactly 4 free file uploads
    | across ALL PDF tools.
    |
    | Example:
    |
    | Merge PDF       = 2 files
    | Compress PDF    = 1 file
    | Split PDF       = 1 file
    | --------------------------------
    | Total            = 4 files
    |
    | After 4 uploads, user needs an active $2/month subscription.
    |
    | Premium users with an active premium_expires_at date
    | have unlimited uploads.
    |
    */

    private const FREE_LIMIT = 4;


    /*
    |--------------------------------------------------------------------------
    | Reserve Free Uploads
    |--------------------------------------------------------------------------
    */

    public function reserve(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate File Count
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'file_count' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $fileCount = (int) $validated['file_count'];


        /*
        |--------------------------------------------------------------------------
        | Authenticated User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user) {

            return response()->json([
                'success' => false,
                'allowed' => false,
                'premium' => false,
                'requires_payment' => false,
                'message' =>
                    'Please login before uploading files.',
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | Active Monthly Premium Check
        |--------------------------------------------------------------------------
        |
        | premium_expires_at must be a datetime field on users table.
        |
        | Example:
        |
        | 2026-10-30 14:30:00
        |
        | If expiry is in the future:
        | unlimited access.
        |
        */

        if (
            $user->premium_expires_at !== null &&
            $user->premium_expires_at->isFuture()
        ) {

            return response()->json([
                'success' => true,
                'allowed' => true,
                'premium' => true,
                'requires_payment' => false,

                'free_uploads_used' => self::FREE_LIMIT,

                'remaining' => null,

                'file_count' => $fileCount,

                'premium_expires_at' =>
                    $user->premium_expires_at->toIso8601String(),

                'message' =>
                    'Premium access active. Unlimited uploads are allowed.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Lock User Row
        |--------------------------------------------------------------------------
        |
        | This prevents two simultaneous requests from bypassing
        | the 4-file global limit.
        |
        */

        $result = DB::transaction(function () use (
            $user,
            $fileCount
        ) {

            /*
            |--------------------------------------------------------------------------
            | Lock User
            |--------------------------------------------------------------------------
            */

            $lockedUser = DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();


            /*
            |--------------------------------------------------------------------------
            | User Not Found
            |--------------------------------------------------------------------------
            */

            if (!$lockedUser) {

                return [
                    'success' => false,
                    'allowed' => false,
                    'premium' => false,
                    'requires_payment' => false,

                    'message' =>
                        'User account could not be found.',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Active Premium Check Again
            |--------------------------------------------------------------------------
            |
            | We check again after locking the row.
            |
            | This is important if another request/payment changed
            | the user record while this request was starting.
            |
            */

            $premiumExpiresAt =
                $lockedUser->premium_expires_at ?? null;


            if ($premiumExpiresAt !== null) {

                try {

                    $expiry = \Carbon\Carbon::parse(
                        $premiumExpiresAt
                    );

                    if ($expiry->isFuture()) {

                        return [
                            'success' => true,
                            'allowed' => true,
                            'premium' => true,
                            'requires_payment' => false,

                            'free_uploads_used' =>
                                self::FREE_LIMIT,

                            'remaining' => null,

                            'file_count' => $fileCount,

                            'premium_expires_at' =>
                                $expiry->toIso8601String(),

                            'message' =>
                                'Premium access active. Unlimited uploads are allowed.',
                        ];
                    }

                } catch (\Throwable $e) {

                    Log::warning(
                        'Invalid premium_expires_at value.',
                        [
                            'user_id' => $user->id,
                            'premium_expires_at' =>
                                $premiumExpiresAt,
                        ]
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Current Global Usage
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | We are using your existing database column:
            |
            | pdf_free_uploads_used
            |
            */

            $used = (int) (
                $lockedUser->pdf_free_uploads_used ?? 0
            );


            /*
            |--------------------------------------------------------------------------
            | Protect Against Invalid Values
            |--------------------------------------------------------------------------
            */

            if ($used < 0) {
                $used = 0;
            }

            if ($used > self::FREE_LIMIT) {
                $used = self::FREE_LIMIT;
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate Remaining
            |--------------------------------------------------------------------------
            */

            $freeLimit = self::FREE_LIMIT;

            $remaining = max(
                0,
                $freeLimit - $used
            );


            /*
            |--------------------------------------------------------------------------
            | Check Requested File Count
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Used = 3
            | Remaining = 1
            |
            | User requests 1:
            | ALLOWED
            |
            | User requests 2:
            | PAYMENT REQUIRED
            |
            */

            if ($fileCount > $remaining) {

                return [
                    'success' => false,
                    'allowed' => false,
                    'premium' => false,
                    'requires_payment' => true,

                    'free_uploads_used' => $used,

                    'remaining' => $remaining,

                    'file_count' => $fileCount,

                    'free_limit' => self::FREE_LIMIT,

                    'redirect' => route('payment'),

                    'message' =>
                        'You have used your 4 free uploads. ' .
                        'Please subscribe for $2/month to unlock unlimited PDF tools.',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Consume Free Uploads
            |--------------------------------------------------------------------------
            */

            $newUsed = $used + $fileCount;


            /*
            |--------------------------------------------------------------------------
            | Extra Security Check
            |--------------------------------------------------------------------------
            */

            if ($newUsed > $freeLimit) {

                return [
                    'success' => false,
                    'allowed' => false,
                    'premium' => false,
                    'requires_payment' => true,

                    'free_uploads_used' => $used,

                    'remaining' => $remaining,

                    'file_count' => $fileCount,

                    'free_limit' => self::FREE_LIMIT,

                    'redirect' => route('payment'),

                    'message' =>
                        'Your free upload limit has been reached. ' .
                        'Please subscribe for $2/month.',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Update Usage
            |--------------------------------------------------------------------------
            */

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'pdf_free_uploads_used' => $newUsed,
                    'updated_at' => now(),
                ]);


            /*
            |--------------------------------------------------------------------------
            | New Remaining
            |--------------------------------------------------------------------------
            */

            $newRemaining = max(
                0,
                $freeLimit - $newUsed
            );


            /*
            |--------------------------------------------------------------------------
            | Return Success
            |--------------------------------------------------------------------------
            */

            return [
                'success' => true,

                'allowed' => true,

                'premium' => false,

                'requires_payment' => false,

                'free_uploads_used' => $newUsed,

                'remaining' => $newRemaining,

                'file_count' => $fileCount,

                'free_limit' => self::FREE_LIMIT,

                'message' =>
                    'Upload allowed. ' .
                    $newRemaining .
                    ' free upload(s) remaining.',
            ];
        });


        /*
        |--------------------------------------------------------------------------
        | HTTP Status
        |--------------------------------------------------------------------------
        |
        | 200 = allowed
        | 402 = payment required
        |
        */

        $status = !empty($result['allowed'])
            ? 200
            : (
                !empty($result['requires_payment'])
                    ? 402
                    : 400
            );


        return response()->json(
            $result,
            $status
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Current Usage Status
    |--------------------------------------------------------------------------
    |
    | Optional endpoint.
    |
    | Frontend can call:
    |
    | GET /pdf/usage/status
    |
    */

    public function status()
    {
        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user) {

            return response()->json([
                'success' => false,
                'authenticated' => false,
                'allowed' => false,
                'premium' => false,
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | Active Premium
        |--------------------------------------------------------------------------
        */

        if (
            $user->premium_expires_at !== null &&
            $user->premium_expires_at->isFuture()
        ) {

            return response()->json([
                'success' => true,

                'authenticated' => true,

                'allowed' => true,

                'premium' => true,

                'requires_payment' => false,

                'free_uploads_used' =>
                    self::FREE_LIMIT,

                'remaining' => null,

                'free_limit' =>
                    self::FREE_LIMIT,

                'premium_expires_at' =>
                    $user->premium_expires_at->toIso8601String(),

                'message' =>
                    'Premium access active. Unlimited uploads are allowed.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Normal User Usage
        |--------------------------------------------------------------------------
        */

        $used = (int) (
            $user->pdf_free_uploads_used ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | Protect Invalid Values
        |--------------------------------------------------------------------------
        */

        $used = max(
            0,
            min(self::FREE_LIMIT, $used)
        );


        /*
        |--------------------------------------------------------------------------
        | Remaining
        |--------------------------------------------------------------------------
        */

        $remaining = max(
            0,
            self::FREE_LIMIT - $used
        );


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'authenticated' => true,

            'allowed' => $remaining > 0,

            'premium' => false,

            'requires_payment' => $remaining <= 0,

            'free_uploads_used' => $used,

            'remaining' => $remaining,

            'free_limit' => self::FREE_LIMIT,

            'premium_expires_at' => null,

            'message' =>
                $remaining > 0
                    ? "You have {$remaining} free upload(s) remaining."
                    : 'Your 4 free uploads have been used. Please subscribe for $2/month.',
        ]);
    }
}