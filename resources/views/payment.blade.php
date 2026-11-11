
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Premium Payment | PDFTools</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#e5322d',
                        brandDark: '#c9211c'
                    }
                }
            }
        };
    </script>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .hero-bg {
            background:
                linear-gradient(#e5322d08 1px, transparent 1px),
                linear-gradient(90deg, #e5322d08 1px, transparent 1px);
            background-size: 36px 36px;
        }

        .card-shadow {
            box-shadow: 0 12px 35px rgba(15, 23, 42, .06);
        }

        .payment-option:has(input:checked) {
            border-color: #e5322d;
            background: #fff5f5;
            box-shadow: 0 0 0 1px #e5322d;
        }

        .bank-option:has(input:checked) {
            border-color: #e5322d;
            background: #fff5f5;
        }

        .loader {
            width: 18px;
            height: 18px;
            border: 2px solid #ffffff70;
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media print {
            body * { visibility: hidden; }

            #paymentSlip, #paymentSlip * {
                visibility: visible;
            }

            #paymentSlip {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                max-width: 390px;
                margin: 0;
                box-shadow: none;
            }

            .no-print { display: none !important; }
        }
    </style>
</head>

<body class="min-h-screen overflow-x-hidden bg-slate-50 text-slate-800 antialiased">

@php
    $premiumActive = $premiumActive ?? false;
    $banks = $banks ?? [];
    $accountHolderName = $accountHolderName ?? '';
@endphp

<!-- HEADER -->
<header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6">

        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2">
            <span class="rounded-lg bg-brand px-2.5 py-1.5 font-black text-white">PDF</span>
            <span class="text-xl font-black text-slate-900">Tools</span>
        </a>

        <nav class="hidden items-center gap-6 text-sm font-semibold md:flex">
            <a href="{{ route('home') }}" class="hover:text-brand">Home</a>
            <a href="{{ route('home') }}#tools" class="hover:text-brand">All Tools</a>
            <a href="{{ route('home') }}#about" class="hover:text-brand">About</a>
        </nav>

        <a href="{{ route('home') }}#tools"
           class="rounded-lg bg-brand px-3 py-2 text-xs font-bold text-white hover:bg-brandDark sm:px-5 sm:text-sm">
            Explore Tools
        </a>
    </div>
</header>

<!-- HERO -->
<section class="hero-bg border-b border-red-100 bg-red-50/50">
    <div class="mx-auto max-w-5xl px-4 py-9 text-center sm:py-12">

        <span class="inline-flex items-center gap-2 rounded-full border border-red-100 bg-white px-4 py-1.5 text-xs font-bold text-brand">
            <i class="fa-solid fa-shield-halved"></i>
            SECURE PREMIUM PAYMENT
        </span>

        <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900 sm:text-5xl">
            @if ($premiumActive)
                Premium <span class="text-brand">Active</span>
            @else
                Upgrade to <span class="text-brand">Premium</span>
            @endif
        </h1>

        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600 sm:text-base">
            @if ($premiumActive)
                Your premium subscription is active.
                @if (!empty($premiumExpiresAt))
                    It expires on {{ $premiumExpiresAt->format('d M Y') }}.
                @endif
            @else
                Get unlimited access to premium PDF tools for only $2 per month.
            @endif
        </p>
    </div>
</section>

<!-- MAIN -->
<main class="mx-auto w-full max-w-5xl px-4 py-7 sm:px-6 sm:py-10">

    <div class="grid grid-cols-1 gap-6 {{ $premiumActive ? 'max-w-2xl mx-auto' : 'lg:grid-cols-2' }}">

        <!-- PREMIUM PLAN -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white card-shadow">
            <div class="p-5 sm:p-7">

                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand">Premium Plan</p>
                        <h2 class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">
                            Unlimited PDF Access
                        </h2>
                    </div>

                    <div class="shrink-0 text-right">
                        <strong class="text-3xl font-black text-slate-900">$2</strong>
                        <p class="text-xs text-slate-500">per month</p>
                    </div>
                </div>

                @php
                    $features = [
                        'Unlimited PDF processing',
                        'Access to all PDF tools',
                        'No daily processing limit',
                        'Fast and secure processing',
                        'Mobile-friendly workspace',
                    ];
                @endphp

                <div class="mt-6 space-y-4">
                    @foreach ($features as $feature)
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-50 text-xs font-bold text-green-600">
                                <i class="fa-solid fa-check"></i>
                            </span>
                            <span class="text-sm text-slate-700">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>

                @if ($premiumActive)
                    <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4">
                        <p class="font-bold text-green-800">
                            <i class="fa-solid fa-circle-check mr-1"></i>
                            Premium is active
                        </p>
                        @if (!empty($premiumExpiresAt))
                            <p class="mt-1 text-sm text-green-700">
                                Active until {{ $premiumExpiresAt->format('d M Y') }}.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        @unless ($premiumActive)

        <!-- PAYMENT FORM -->
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white card-shadow">

            <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                <p class="text-xs font-bold uppercase tracking-wider text-brand">Secure Payment</p>
                <h2 class="mt-1 text-xl font-black text-slate-900">Submit Payment</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Submit your transaction details for administrator verification.
                </p>
            </div>

            <div class="p-5 sm:p-6">

                <div id="messageBox"
                     role="alert"
                     aria-live="polite"
                     class="mb-5 hidden rounded-xl border px-4 py-3 text-sm font-semibold">
                </div>

                <!-- PAYMENT METHOD -->
                <fieldset>
                    <legend class="mb-3 text-sm font-bold">Choose Payment Method</legend>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                        <label class="payment-option flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:bg-red-50/50">
                            <input type="radio" name="payment_method" value="jazzcash"
                                   class="h-4 w-4 shrink-0 accent-red-600" checked>

                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                                <i class="fa-solid fa-mobile-screen-button text-lg"></i>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-sm font-bold">JazzCash</span>
                                <span class="mt-1 block text-xs text-slate-500">Mobile payment</span>
                            </span>
                        </label>

                        <label class="payment-option flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:bg-red-50/50">
                            <input type="radio" name="payment_method" value="credit_card"
                                   class="h-4 w-4 shrink-0 accent-red-600">

                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                <i class="fa-regular fa-credit-card text-lg"></i>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-sm font-bold">Credit Card</span>
                                <span class="mt-1 block text-xs text-slate-500">Card payment</span>
                            </span>
                        </label>

                        <label class="payment-option flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:bg-red-50/50">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                   class="h-4 w-4 shrink-0 accent-red-600">

                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-building-columns text-lg"></i>
                            </span>

                            <span class="min-w-0">
                                <span class="block text-sm font-bold">Other Bank</span>
                                <span class="mt-1 block text-xs text-slate-500">Bank transfer</span>
                            </span>
                        </label>

                    </div>
                </fieldset>

                <!-- BANK SEARCH -->
                <div id="bankOptions" class="mt-5 hidden">
                    <label for="bankSearch" class="mb-2 block text-sm font-bold">
                        Search and select your bank
                    </label>

                    <div class="relative mb-3">
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="bankSearch" type="search" autocomplete="off"
                               placeholder="Type bank name..."
                               class="w-full rounded-xl border border-slate-200 py-3 pl-10 pr-4 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-red-100">
                    </div>

                    <p id="bankSearchEmpty" class="mb-3 hidden rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">
                        No matching bank found. Try another name.
                    </p>

                    <div id="bankList" class="max-h-56 space-y-2 overflow-y-auto pr-1">

                        @foreach ($banks as $bank)
                            @php
                                $bankName = is_string($bank) ? $bank : ($bank->name ?? '');
                                $bankAccount = is_string($bank) ? '' : ($bank->account_number ?? '');
                            @endphp

                            @if ($bankName)
                                <label data-bank-option
                                       data-bank-name="{{ strtolower($bankName) }}"
                                       class="bank-option flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-red-200 hover:bg-red-50/40">

                                    <input type="radio" name="bank_name" value="{{ $bankName }}"
                                           class="h-4 w-4 shrink-0 accent-red-600">

                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span class="block break-words text-sm font-bold text-slate-800">
                                            {{ $bankName }}
                                        </span>

                                        <span class="mt-1 block break-all text-xs text-slate-500">
                                            {{ $bankAccount ?: 'Bank transfer' }}
                                        </span>
                                    </span>
                                </label>
                            @endif
                        @endforeach

                    </div>
                </div>

                <!-- CARD ISSUING BANK -->
                <div id="cardBankOptions" class="mt-5 hidden">
                    <label for="cardBankSearch" class="mb-2 block text-sm font-bold">
                        Search card issuing bank
                    </label>

                    <div class="relative mb-3">
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="cardBankSearch" type="search" autocomplete="off"
                               placeholder="Type bank name..."
                               class="w-full rounded-xl border border-slate-200 py-3 pl-10 pr-4 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-red-100">
                    </div>

                    <p id="cardBankSearchEmpty" class="mb-3 hidden rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">
                        No matching bank found.
                    </p>

                    <div id="cardBankList" class="grid max-h-56 grid-cols-1 gap-2 overflow-y-auto pr-1 sm:grid-cols-2">

                        @foreach ($banks as $bank)
                            @php
                                $bankName = is_string($bank) ? $bank : ($bank->name ?? '');
                            @endphp

                            @if ($bankName)
                                <label data-card-bank-option
                                       data-bank-name="{{ strtolower($bankName) }}"
                                       class="bank-option flex min-w-0 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 p-3 hover:bg-red-50/40">

                                    <input type="radio" name="card_bank_name" value="{{ $bankName }}"
                                           class="h-4 w-4 shrink-0 accent-red-600">

                                    <i class="fa-regular fa-credit-card shrink-0 text-slate-500"></i>
                                    <span class="break-words text-xs font-semibold text-slate-700">
                                        {{ $bankName }}
                                    </span>
                                </label>
                            @endif
                        @endforeach

                    </div>
                </div>

                <!-- ACCOUNT HOLDER -->
                <div class="mt-5">
                    <label for="account_holder_name" class="mb-2 block text-sm font-bold">
                        Account Holder Name
                    </label>

                    <input id="account_holder_name" type="text"
                           value="{{ $accountHolderName }}"
                           autocomplete="name" maxlength="150"
                           placeholder="Enter full name"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-red-100">
                </div>

                <!-- ACCOUNT NUMBER -->
                <div id="bankAccountNumberBox" class="mt-5 hidden">
                    <label for="bank_account_number" class="mb-2 block text-sm font-bold">
                        Bank Account Number / IBAN
                    </label>

                    <input id="bank_account_number" type="text" inputmode="text"
                           maxlength="34" autocomplete="off"
                           placeholder="Enter account number or IBAN"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-red-100">
                </div>

                <!-- TRANSACTION -->
                <div class="mt-5">
                    <label for="transaction_id" class="mb-2 block text-sm font-bold">
                        Transaction ID / Reference
                    </label>

                    <input id="transaction_id" type="text" maxlength="150"
                           autocomplete="off" placeholder="Enter transaction reference ID"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-red-100">
                </div>

                <!-- SUBMIT -->
                <button id="paymentBtn" type="button"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-brand py-3.5 text-sm font-bold text-white transition hover:bg-brandDark disabled:cursor-not-allowed disabled:opacity-60">

                    <span id="paymentBtnText">Submit Payment Request</span>
                    <span id="paymentLoader" class="loader hidden"></span>
                </button>

                <p class="mt-3 text-center text-xs leading-5 text-slate-400">
                    <i class="fa-solid fa-lock mr-1"></i>
                    Your payment request is sent securely for verification.
                </p>

            </div>
        </section>

        @endunless
    </div>

    <!-- PAYMENT RECEIPT -->
    <section id="paymentSlip" class="mx-auto mt-8 hidden w-full max-w-sm overflow-hidden rounded-xl border border-slate-200 bg-white card-shadow">
        <div class="bg-slate-950 px-5 py-4 text-white">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="rounded bg-brand px-2 py-1 text-xs font-black">PDF</span>
                    <span class="text-lg font-black">Tools</span>
                </div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-300"> Payment Receipt  </span>
            </div>
            <p class="mt-2 text-xs text-slate-400">Premium subscription payment</p>
        </div>

        <div class="p-5">
            <div class="flex justify-between gap-3">
                <div>
                    <p class="text-xs text-slate-400">Receipt Number</p>
                    <p id="slipReceiptNo" class="mt-1 break-all text-sm font-bold">—</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400">Date</p>
                    <p id="slipDate" class="mt-1 text-sm font-semibold">—</p>
                </div>
            </div>

            <div class="my-4 border-t border-dashed border-slate-200"></div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Customer</span>
                    <strong id="slipCustomer" class="max-w-[60%] break-words text-right">—</strong>
                </div>

                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Payment Method</span>
                    <strong id="slipMethod" class="text-right">—</strong>
                </div>

                <div id="slipBankRow" class="hidden justify-between gap-3">
                    <span class="text-slate-500">Bank</span>
                    <strong id="slipBank" class="max-w-[60%] break-words text-right">—</strong>
                </div>

                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Transaction ID</span>
                    <strong id="slipTransaction" class="max-w-[60%] break-all text-right">—</strong>
                </div>

                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Plan</span>
                    <strong>Premium - 1 Month</strong>
                </div>
            </div>

            <div class="my-4 border-t border-dashed border-slate-200"></div>
            <div class="rounded-xl border border-red-100 bg-red-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase text-brand">Total Amount</p>
                        <p class="mt-1 text-xs text-slate-500">Monthly premium</p>
                    </div>
                    <strong class="text-2xl font-black text-brand">$2.00</strong>
                </div>
            </div>

            <div class="mt-3 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                <i class="fa-solid fa-clock mr-1"></i>
                Submitted. Waiting for admin verification.
            </div>

            <div id="slipDownloadArea" class="no-print mt-4 hidden">
                <a id="slipDownloadButton" href="#"
                   class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-900 py-3 text-sm font-bold text-white hover:bg-slate-800">
                    <i class="fa-solid fa-download"></i>
                    Download Payment Slip
                </a>
            </div>

            <button type="button" onclick="window.print()"
                    class="no-print mt-3 w-full rounded-lg border border-slate-200 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-print mr-1"></i>
                Print Receipt
            </button>

            <p class="mt-4 text-center text-xs font-semibold text-slate-600">  Thank you for choosing PDFTools.</p>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer class="bg-white border-t border-slate-100 mt-10">
        <div
            class="max-w-6xl mx-auto px-4 sm:px-6 py-7 flex flex-col sm:flex-row gap-4 justify-between items-center text-center sm:text-left">
            <b class="text-xl">PDF<span class="text-brand">Tools</span></b>
            <p class="text-sm text-slate-500">© {{date('Y')}} PDFTools. All rights reserved.</p>
            <a href="{{route('home')}}#tools" class="text-brand font-semibold">All Tools →</a>
        </div>
    </footer>

<script>
(() => {
    'use strict';

    const paymentBtn = document.getElementById('paymentBtn');
    const paymentBtnText = document.getElementById('paymentBtnText');
    const paymentLoader = document.getElementById('paymentLoader');
    const messageBox = document.getElementById('messageBox');
    const bankOptions = document.getElementById('bankOptions');
    const cardBankOptions = document.getElementById('cardBankOptions');
    const bankAccountBox = document.getElementById('bankAccountNumberBox');
    const accountName = document.getElementById('account_holder_name');
    const accountNumber = document.getElementById('bank_account_number');
    const transactionInput = document.getElementById('transaction_id');
    function showMessage(message, type = 'error') {
        messageBox.className = 'mb-5 rounded-xl border px-4 py-3 text-sm font-semibold';
        if (type === 'success') { messageBox.classList.add('border-green-200', 'bg-green-50', 'text-green-700');
        } else { messageBox.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
        }

        messageBox.textContent = message;
        messageBox.classList.remove('hidden');
    }

    function getMethod() { return document.querySelector('input[name="payment_method"]:checked')?.value || '';
    }

    function updatePaymentMethod() {
        const method = getMethod();
        bankOptions.classList.toggle('hidden', method !== 'bank_transfer');
        cardBankOptions.classList.toggle('hidden', method !== 'credit_card');
        bankAccountBox.classList.toggle('hidden', method !== 'bank_transfer');
        if (method !== 'bank_transfer') {
            document.querySelectorAll('input[name="bank_name"]').forEach(input => { input.checked = false;
            });
        }

        if (method !== 'credit_card') {
            document.querySelectorAll('input[name="card_bank_name"]').forEach(input => { input.checked = false;
            });
        }
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(input => { input.addEventListener('change', updatePaymentMethod);
    });

    function attachBankSearch(inputId, optionSelector, emptyStateId) {
        const searchInput = document.getElementById(inputId);
        const emptyState = document.getElementById(emptyStateId);
        if (!searchInput) return;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase();
            let visibleCount = 0;
            document.querySelectorAll(optionSelector).forEach(option => {
                const name = (option.dataset.bankName || '').toLocaleLowerCase();
                const matches = name.includes(query);
                option.classList.toggle('hidden', !matches);
                if (matches) visibleCount++;
                const radio = option.querySelector('input[type="radio"]');
                if (!matches && radio) radio.checked = false;
            });

            if (emptyState) {
                emptyState.classList.toggle('hidden', visibleCount > 0);
            }
        });
    }

    attachBankSearch('bankSearch', '[data-bank-option]', 'bankSearchEmpty');
    attachBankSearch('cardBankSearch', '[data-card-bank-option]', 'cardBankSearchEmpty');
    async function submitPayment() {
        messageBox.classList.add('hidden');
        const method = getMethod();
        const customer = accountName.value.trim();
        const transaction = transactionInput.value.trim();
        const bankName = document.querySelector('input[name="bank_name"]:checked')?.value || '';
        const cardBankName = document.querySelector('input[name="card_bank_name"]:checked')?.value || '';
        const bankAccount = accountNumber.value.trim();
        if (!method) {
            showMessage('Please select a payment method.');
            return;
        }

        if (!customer) {
            showMessage('Please enter account holder name.');
            accountName.focus();
            return;
        }

        if (!transaction) {
            showMessage('Please enter your transaction ID.');
            transactionInput.focus();
            return;
        }

        if (method === 'bank_transfer') {
            if (!bankName) {
                showMessage('Please search and select your bank.');
                document.getElementById('bankSearch').focus();
                return;
            }

            if (bankAccount.replace(/\s/g, '').length < 8) {
                showMessage('Please enter a valid account number or IBAN.');
                accountNumber.focus();
                return;
            }
        }

        if (method === 'credit_card' && !cardBankName) {
            showMessage('Please select the bank that issued your card.');
            document.getElementById('cardBankSearch').focus();
            return;
        }

        paymentBtn.disabled = true;
        paymentBtnText.textContent = 'Sending Request...';
        paymentLoader.classList.remove('hidden');

        try {
            const formData = new FormData();

            formData.append('payment_method', method);
            formData.append('account_holder_name', customer);
            formData.append('transaction_id', transaction);

            if (method === 'bank_transfer') {
                formData.append('bank_name', bankName);
                formData.append('bank_account_number', bankAccount);
            }

            if (method === 'credit_card') {
                formData.append('card_bank_name', cardBankName);
            }

            const response = await fetch(@json(route('payment.submit')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            let data = {};

            try {
                data = await response.json();
            } catch (_) {
                data = {};
            }

            if (!response.ok) {
                let errorMessage = data.message || 'Payment request failed.';

                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (Array.isArray(firstError) && firstError.length) {
                        errorMessage = firstError[0];
                    }
                }

                throw new Error(errorMessage);
            }

            showMessage(
                data.message || 'Your payment request was submitted successfully.',
                'success'
            );

            createReceipt(data, method, customer, transaction,
                method === 'credit_card' ? cardBankName :
                (method === 'bank_transfer' ? bankName : ''));

        } catch (error) {
            showMessage(error.message || 'Unable to submit your payment request.');
        } finally {
            paymentBtn.disabled = false;
            paymentBtnText.textContent = 'Submit Payment Request';
            paymentLoader.classList.add('hidden');
        }
    }

    function createReceipt(data, method, customer, transaction, bank) {
        const now = new Date();

        const date = now.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        const receiptNumber = data.receipt_number ||
            data.payment_id ||
            ('PDF-' + now.getFullYear() +
             String(now.getMonth() + 1).padStart(2, '0') +
             String(now.getDate()).padStart(2, '0') + '-' +
             Math.random().toString(36).substring(2, 8).toUpperCase());

        document.getElementById('slipReceiptNo').textContent = receiptNumber;
        document.getElementById('slipDate').textContent = data.receipt_date || date;
        document.getElementById('slipCustomer').textContent = customer;
        document.getElementById('slipTransaction').textContent = transaction;

        const methodNames = {
            jazzcash: 'JazzCash',
            credit_card: 'Credit Card',
            bank_transfer: 'Bank Transfer'
        };

        document.getElementById('slipMethod').textContent = methodNames[method] || method;

        const bankRow = document.getElementById('slipBankRow');
        bankRow.classList.toggle('hidden', !bank);
        bankRow.classList.toggle('flex', Boolean(bank));

        if (bank) {
            document.getElementById('slipBank').textContent = bank;
        }

        const downloadArea = document.getElementById('slipDownloadArea');
        const downloadButton = document.getElementById('slipDownloadButton');

        downloadArea.classList.add('hidden');

        if (data.receipt_url) {
            downloadButton.href = data.receipt_url;
            downloadButton.classList.remove('hidden');
            downloadArea.classList.remove('hidden');
        }

        const slip = document.getElementById('paymentSlip');
        slip.classList.remove('hidden');

        slip.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    paymentBtn?.addEventListener('click', submitPayment);

    updatePaymentMethod();
})();
</script>

</body>
</html>