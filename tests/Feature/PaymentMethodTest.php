<?php

use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('payment request accepts supported methods', function (string $paymentMethod) {
    Storage::fake('local');
    $user = User::factory()->create();

    $paymentRequest = [
        'payment_method' => $paymentMethod,
        'transaction_id' => 'REF-12345',
        'payment_proof' => UploadedFile::fake()->create('receipt.png', 20, 'image/png'),
    ];

    if ($paymentMethod === 'credit_card') {
        $paymentRequest['bank_name'] = 'hbl';
    }

    $response = $this->actingAs($user)->post(route('payment.submit'), $paymentRequest, [
        'Accept' => 'application/json',
    ]);

    $response->assertOk()->assertJson(['success' => true]);

    $this->assertDatabaseHas('payment_requests', [
        'user_id' => $user->id,
        'payment_method' => $paymentMethod,
        'transaction_id' => 'REF-12345',
    ]);

    $payment = PaymentRequest::where('user_id', $user->id)->firstOrFail();
    Storage::disk('local')->assertExists($payment->proof_path);
})->with(['jazzcash', 'credit_card']);

test('payment request rejects Easypaisa and unknown bank methods', function (string $paymentMethod) {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson(route('payment.submit'), [
        'payment_method' => $paymentMethod,
        'transaction_id' => 'TX-12345',
    ])->assertUnprocessable()->assertJsonValidationErrors('payment_method');
})->with(['easypaisa', 'bop']);

test('payment page includes method-specific account details', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('payment'))
        ->assertOk()
        ->assertSee('jazzCashAccountDetails', false)
        ->assertSee('creditCardAccountDetails', false)
        ->assertSee('creditCardBankList', false)
        ->assertSee('bankAccountForm', false)
        ->assertSee('paymentProofPreview', false)
        ->assertSee('paymentSentButton', false)
        ->assertSee('paymentSubmissionForm', false);
});

test('credit card payment requires and records the issuing bank', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $this->actingAs($user)->postJson(route('payment.submit'), [
        'payment_method' => 'credit_card',
        'transaction_id' => 'CARD-12345',
    ])->assertUnprocessable()->assertJsonValidationErrors('bank_name');

    $response = $this->actingAs($user)->post(route('payment.submit'), [
        'payment_method' => 'credit_card',
        'bank_name' => 'hbl',
        'transaction_id' => 'CARD-12345',
        'payment_proof' => UploadedFile::fake()->create('receipt.png', 20, 'image/png'),
    ], ['Accept' => 'application/json']);

    $response->assertOk()->assertJson(['success' => true]);

    $this->assertDatabaseHas('payment_requests', [
        'user_id' => $user->id,
        'payment_method' => 'credit_card',
        'bank_name' => 'Habib Bank Limited (HBL)',
        'transaction_id' => 'CARD-12345',
    ]);
});

test('bank transfer requires a valid bank selection', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $this->actingAs($user)->postJson(route('payment.submit'), [
        'payment_method' => 'bank_transfer',
        'transaction_id' => 'BANK-12345',
    ])->assertUnprocessable()->assertJsonValidationErrors(['bank_name', 'account_holder_name', 'bank_account_number', 'payment_proof']);

    $this->actingAs($user)->post(route('payment.submit'), [
        'payment_method' => 'bank_transfer',
        'bank_name' => 'hbl',
        'bank_account_number' => '123456789012',
        'transaction_id' => 'BANK-12345',
        'payment_proof' => UploadedFile::fake()->create('receipt.png', 20, 'image/png'),
    ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('account_holder_name');

    $this->actingAs($user)->post(route('payment.submit'), [
        'payment_method' => 'bank_transfer',
        'bank_name' => 'hbl',
        'account_holder_name' => 'Test Account Holder',
        'bank_account_number' => '12345',
        'transaction_id' => 'BANK-12345',
        'payment_proof' => UploadedFile::fake()->create('receipt.png', 20, 'image/png'),
    ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('bank_account_number');

    $response = $this->actingAs($user)->post(route('payment.submit'), [
        'payment_method' => 'bank_transfer',
        'bank_name' => 'hbl',
        'account_holder_name' => 'Test Account Holder',
        'bank_account_number' => '123456789012',
        'transaction_id' => 'BANK-12345',
        'payment_proof' => UploadedFile::fake()->create('receipt.png', 20, 'image/png'),
    ], ['Accept' => 'application/json']);

    $response->assertOk()->assertJson(['success' => true]);

    $this->assertDatabaseHas('payment_requests', [
        'user_id' => $user->id,
        'payment_method' => 'bank_transfer',
        'bank_name' => 'Habib Bank Limited (HBL)',
        'account_holder_name' => 'Test Account Holder',
        'transaction_id' => 'BANK-12345',
    ]);
});

test('only the admin can download a payment proof', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $payment = PaymentRequest::create([
        'user_id' => $user->id,
        'payment_method' => 'jazzcash',
        'transaction_id' => 'PROOF-12345',
        'proof_path' => 'payment-proofs/receipt.pdf',
        'amount' => 2.00,
        'currency' => 'USD',
        'status' => 'pending',
    ]);

    Storage::disk('local')->put($payment->proof_path, 'receipt contents');

    $this->actingAs($user)
        ->get(route('admin.payments.proof', $payment))
        ->assertForbidden();

    $adminEmail = 'payments-admin@example.com';
    $admin = User::factory()->create(['email' => $adminEmail]);
    config(['app.admin_email' => $adminEmail]);

    $this->actingAs($admin)
        ->get(route('admin.payments.proof', $payment))
        ->assertDownload('payment-proof-'.$payment->id.'.pdf');

    $this->get(route('payment.receipt', $payment))
        ->assertDownload('payment-request-'.$payment->id.'.pdf');
});

test('payment owner can download a request receipt but another user cannot', function () {
    $owner = User::factory()->create();
    $payment = PaymentRequest::create([
        'user_id' => $owner->id,
        'payment_method' => 'jazzcash',
        'transaction_id' => 'RECEIPT-12345',
        'amount' => 2.00,
        'currency' => 'USD',
        'status' => 'pending',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('payment.receipt', $payment))
        ->assertForbidden();

    $response = $this->actingAs($owner)
        ->get(route('payment.receipt', $payment));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'attachment; filename="payment-request-'.$payment->id.'.pdf"');

    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');
});
