<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Monthly Premium Price
    |--------------------------------------------------------------------------
    */

    private const MONTHLY_PRICE = 2.00;

    private const BANKS = [
        'allied' => 'Allied Bank Limited',
        'askari' => 'Askari Bank Limited',
        'alfalah' => 'Bank Alfalah Limited',
        'al_habib' => 'Bank AL Habib Limited',
        'bank_islami' => 'BankIslami Pakistan Limited',
        'bank_khyber' => 'The Bank of Khyber',
        'bank_punjab' => 'The Bank of Punjab',
        'dubai_islamic' => 'Dubai Islamic Bank Pakistan Limited',
        'faysal' => 'Faysal Bank Limited',
        'first_women' => 'First Women Bank Limited',
        'hbl' => 'Habib Bank Limited (HBL)',
        'habib_metro' => 'Habib Metropolitan Bank Limited',
        'js' => 'JS Bank Limited',
        'mcb' => 'MCB Bank Limited',
        'meezan' => 'Meezan Bank Limited',
        'nbp' => 'National Bank of Pakistan',
        'sindh' => 'Sindh Bank Limited',
        'soneri' => 'Soneri Bank Limited',
        'standard_chartered' => 'Standard Chartered Bank Pakistan',
        'ubl' => 'United Bank Limited (UBL)',
        'ztbl' => 'Zarai Taraqiati Bank Limited',
        'other' => 'Other Bank',
    ];

    /*
    |--------------------------------------------------------------------------
    | Payment Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        $premiumExpiresAt = $user->premium_expires_at;

        $premiumActive =
            $user->premium_expires_at !== null &&
            $user->premium_expires_at->isFuture();

        $pendingPayment = PaymentRequest::where(
            'user_id',
            $user->id
        )
            ->where(
                'status',
                'pending'
            )
            ->latest()
            ->first();

        $banks = self::BANKS;

        $accountHolderName = $user->name;

        return view(
            'payment',
            compact(
                'accountHolderName',
                'banks',
                'pendingPayment',
                'premiumActive',
                'premiumExpiresAt'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Submit Payment Request
    |--------------------------------------------------------------------------
    */

    public function submit(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'payment_method' => [
                'required',
                'in:jazzcash,credit_card,bank_transfer',
            ],

            'bank_name' => [
                'nullable',
                'required_if:payment_method,bank_transfer,credit_card',
                Rule::in(array_keys(self::BANKS)),
            ],

            'account_holder_name' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'string',
                'max:120',
            ],

            'bank_account_number' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'string',
                'max:34',

                function (
                    string $attribute,
                    mixed $value,
                    Closure $fail
                ): void {
                    if (
                        !is_string($value) ||
                        !self::isValidPakistanBankAccountNumber($value)
                    ) {
                        $fail(
                            'Enter a valid account number or Pakistani IBAN.'
                        );
                    }
                },
            ],

            'transaction_id' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Current User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Active Premium Check
        |--------------------------------------------------------------------------
        */

        if (
            $user->premium_expires_at !== null &&
            $user->premium_expires_at->isFuture()
        ) {
            return response()->json([
                'success' => true,

                'message' =>
                    'Premium access is already active until ' .
                    $user->premium_expires_at->format('d M Y') .
                    '.',

                'redirect' => route('home'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Existing Pending Payment
        |--------------------------------------------------------------------------
        */

        $alreadyPending = PaymentRequest::where(
            'user_id',
            $user->id
        )
            ->where(
                'status',
                'pending'
            )
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'success' => false,

                'message' =>
                    'You already have a pending payment request. ' .
                    'Please wait for admin verification.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Duplicate Transaction ID
        |--------------------------------------------------------------------------
        */

        $duplicateTransaction = PaymentRequest::where(
            'transaction_id',
            $validated['transaction_id']
        )->exists();

        if ($duplicateTransaction) {
            return response()->json([
                'success' => false,

                'message' =>
                    'This transaction ID has already been submitted.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Payment Request
        |--------------------------------------------------------------------------
        |
        | No payment_proof
        | No proof_path
        |
        */

        $payment = PaymentRequest::create([

            'user_id' =>
                $user->id,

            'payment_method' =>
                $validated['payment_method'],

            'bank_name' =>
                isset($validated['bank_name'])
                    ? self::BANKS[$validated['bank_name']]
                    : null,

            'account_holder_name' =>
                $validated['account_holder_name'] ?? null,

            'transaction_id' =>
                $validated['transaction_id'],

            'amount' =>
                self::MONTHLY_PRICE,

            'currency' =>
                'USD',

            'status' =>
                'pending',

            'admin_note' =>
                null,

            'approved_at' =>
                null,

            'rejected_at' =>
                null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate The SAME PDF Slip
        |--------------------------------------------------------------------------
        */

        $pdfContent = $this->generateReceiptPdf($payment);

        /*
        |--------------------------------------------------------------------------
        | Send SAME PDF Slip To Admin
        |--------------------------------------------------------------------------
        */

        $adminEmail = trim(
            (string) config('app.admin_email')
        );

        if ($adminEmail !== '') {
            try {
                Mail::raw(
                    $this->getAdminEmailMessage($payment),
                    function ($message) use (
                        $adminEmail,
                        $payment,
                        $pdfContent
                    ) {
                        $message
                            ->to($adminEmail)
                            ->subject(
                                'New Premium Payment Request #' .
                                $payment->id
                            )
                            ->attachData(
                                $pdfContent,
                                'payment-request-' .
                                $payment->id .
                                '.pdf',
                                [
                                    'mime' =>
                                        'application/pdf',
                                ]
                            );
                    }
                );
            } catch (\Throwable $e) {
                /*
                |--------------------------------------------------------------------------
                | Payment Is Already Saved
                |--------------------------------------------------------------------------
                |
                | If email fails, payment request remains saved.
                | User can still download the same receipt.
                |
                */

                report($e);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'Payment request submitted successfully. ' .
                'Admin verification is pending.',

            'payment_id' =>
                $payment->id,

            'receipt_url' =>
                route(
                    'payment.receipt',
                    $payment
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Download Payment Receipt
    |--------------------------------------------------------------------------
    |
    | This uses the EXACT SAME PDF generator that is sent to admin.
    |
    */

    public function downloadReceipt(
        PaymentRequest $payment
    ): Response {
        $user = Auth::user();

        $adminEmail = strtolower(
            trim(
                (string) config('app.admin_email')
            )
        );

        $isOwner =
            (int) $payment->user_id ===
            (int) Auth::id();

        $isAdmin =
            $adminEmail !== '' &&
            strtolower(
                trim(
                    (string) $user?->email
                )
            ) === $adminEmail;

        abort_unless(
            $isOwner || $isAdmin,
            403
        );

        $pdfContent = $this->generateReceiptPdf(
            $payment
        );

        return response(
            $pdfContent,
            200,
            [
                'Content-Type' =>
                    'application/pdf',

                'Content-Disposition' =>
                    'attachment; filename="payment-request-' .
                    $payment->id .
                    '.pdf"',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Same Payment Slip PDF
    |--------------------------------------------------------------------------
    */

    private function generateReceiptPdf(
        PaymentRequest $payment
    ): string {
        $payment->loadMissing('user');

        $paymentMethods = [
            'jazzcash' =>
                'JazzCash',

            'credit_card' =>
                'Credit Card',

            'bank_transfer' =>
                'Bank Transfer',
        ];

        $pdf = new \TCPDF(
            'P',
            'mm',
            'A4',
            true,
            'UTF-8',
            false
        );

        $pdf->SetCreator(
            (string) config('app.name')
        );

        $pdf->SetAuthor(
            (string) config('app.name')
        );

        $pdf->SetTitle(
            'Payment Request Receipt'
        );

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(
            18,
            18,
            18
        );

        $pdf->AddPage();

        /*
        |--------------------------------------------------------------------------
        | Heading
        |--------------------------------------------------------------------------
        */

        $pdf->SetFont(
            'helvetica',
            'B',
            18
        );

        $pdf->Cell(
            0,
            12,
            'Payment Request Receipt',
            0,
            1
        );

        $pdf->SetFont(
            'helvetica',
            '',
            11
        );

        $pdf->Ln(3);

        /*
        |--------------------------------------------------------------------------
        | Receipt Rows
        |--------------------------------------------------------------------------
        */

        $receiptRows = [

            [
                'Request number',
                '#' . $payment->id
            ],

            [
                'Customer',
                $payment->user->name
            ],

            [
                'Email',
                $payment->user->email
            ],

            [
                'Amount',
                $payment->currency .
                ' ' .
                number_format(
                    (float) $payment->amount,
                    2
                )
            ],

            [
                'Payment method',
                $paymentMethods[
                    $payment->payment_method
                ] ??
                ucfirst(
                    $payment->payment_method
                )
            ],
        ];

        if ($payment->bank_name !== null) {
            $receiptRows[] = [
                'Selected bank',
                $payment->bank_name
            ];
        }

        $receiptRows[] = [
            'Transaction ID',
            $payment->transaction_id
        ];

        $receiptRows[] = [
            'Submitted',
            $payment->created_at?->format(
                'd M Y, h:i A'
            ) ?? ''
        ];

        $receiptRows[] = [
            'Status',
            ucfirst(
                $payment->status
            ) .
            ' - awaiting admin verification'
        ];

        /*
        |--------------------------------------------------------------------------
        | Print Rows
        |--------------------------------------------------------------------------
        */

        foreach (
            $receiptRows
            as [$label, $value]
        ) {
            $pdf->SetFont(
                'helvetica',
                'B',
                10
            );

            $pdf->Cell(
                48,
                9,
                $label,
                1,
                0
            );

            $pdf->SetFont(
                'helvetica',
                '',
                10
            );

            $pdf->Cell(
                0,
                9,
                (string) $value,
                1,
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Note
        |--------------------------------------------------------------------------
        */

        $pdf->Ln(6);

        $pdf->SetFont(
            'helvetica',
            'I',
            9
        );

        $pdf->MultiCell(
            0,
            7,
            'This document confirms submission of a payment request only. It is not a bank-issued receipt or confirmation that funds have cleared.',
            0,
            'L'
        );

        /*
        |--------------------------------------------------------------------------
        | Return PDF As String
        |--------------------------------------------------------------------------
        */

        return $pdf->Output(
            '',
            'S'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Email Message
    |--------------------------------------------------------------------------
    */

    private function getAdminEmailMessage(
        PaymentRequest $payment
    ): string {
        $payment->loadMissing('user');

        return
            "New Premium Payment Request\n\n" .

            "Request Number: #" .
            $payment->id .
            "\n" .

            "Customer: " .
            $payment->user->name .
            "\n" .

            "Email: " .
            $payment->user->email .
            "\n" .

            "Amount: " .
            $payment->currency .
            ' ' .
            number_format(
                (float) $payment->amount,
                2
            ) .
            "\n" .

            "Payment Method: " .
            ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $payment->payment_method
                )
            ) .
            "\n" .

            (
                $payment->bank_name
                    ? "Bank: " .
                      $payment->bank_name .
                      "\n"
                    : ''
            ) .

            "Transaction ID: " .
            $payment->transaction_id .
            "\n\n" .

            "The same payment slip is attached to this email.\n" .

            "Please verify the payment before approving the premium subscription.";
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Pakistan Bank Account Number / IBAN
    |--------------------------------------------------------------------------
    */

    private static function isValidPakistanBankAccountNumber(
        string $accountNumber
    ): bool {
        $accountNumber = strtoupper(
            (string) preg_replace(
                '/[\s-]+/',
                '',
                trim($accountNumber)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Normal Account Number
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^\d{10,24}$/',
                $accountNumber
            ) === 1
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Pakistani IBAN
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^PK\d{2}[A-Z]{4}\d{16}$/',
                $accountNumber
            ) !== 1
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | IBAN MOD-97 Validation
        |--------------------------------------------------------------------------
        */

        $rearrangedIban =
            substr($accountNumber, 4) .
            substr($accountNumber, 0, 4);

        $remainder = 0;

        foreach (
            str_split($rearrangedIban)
            as $character
        ) {
            $digits = ctype_alpha($character)
                ? (string) (
                    ord($character) - 55
                )
                : $character;

            foreach (
                str_split($digits)
                as $digit
            ) {
                $remainder =
                    (
                        $remainder * 10 +
                        (int) $digit
                    ) % 97;
            }
        }

        return $remainder === 1;
    }
}
