<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Requests - PDFTools Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    }
    </script>
    <style>
    body {
        font-family: Inter,
            ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    }

    html {
        scrollbar-width: none
    }

    .grid-bg {
        background-color: #fff7f7;
        background-image: linear-gradient(rgba(229, 50, 45, 0.045) 1px, transparent 1px), linear-gradient(90deg, rgba(229, 50, 45, 0.045) 1px, transparent 1px);
        background-size: 32px 32px;
    }

    #paymentConfirmDialog:not(.hidden) {
        display: flex;
    }

    .payment-confirm-card {
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
    }

    @supports (height: 100dvh) {
        .payment-confirm-card {
            max-height: calc(100dvh - 2rem);
        }
    }

    /* Responsive safeguards for phones, tablets and desktop screens */
    html, body {
        width: 100%;
        max-width: 100%;
        overflow-x: clip;
    }

    img, svg, video, canvas {
        max-width: 100%;
    }

    .payment-confirm-card {
        overscroll-behavior: contain;
    }

    @media (max-width: 639px) {
        header .max-w-7xl {
            gap: .5rem;
        }

        main {
            min-width: 0;
        }

        article {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        article .inline-flex {
            max-width: 100%;
            flex-wrap: wrap;
            overflow-wrap: anywhere;
        }

        #paymentConfirmDialog {
            align-items: flex-start;
            overflow-y: auto;
        }

        .payment-confirm-card {
            margin: auto 0;
            max-height: calc(100vh - 1.5rem);
            padding: 1rem;
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        article .grid {
            min-width: 0;
        }
    }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center font-black text-sm shadow-sm"> PDF </div>
                <div class="leading-tight">
                    <div class="text-lg font-black tracking-tight"> PDF<span class="text-brand">Tools</span> </div>
                    <div class="text-[10px] uppercase tracking-[0.16em] text-slate-400 font-bold"> Administration </div>
                </div>
            </a>
            <div class="flex items-center gap-3"> <span class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-green-500 text-white text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-red-500"> </span> Admin Panel </span>
                   <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-brand text-sm font-bold hover:text-slate-900 text-white transition"> <span class="hidden sm:inline"> Back to Website</span> <span class="sm:hidden"> Back</span> </a>
            </div>
        </div>
    </header>
    <section class="grid-bg border-b border-red-100">
        <div class="max-w-7xl mx-auto px-3 sm:px-5 md:px-6 lg:px-8 py-8 sm:py-10 lg:py-12">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 sm:gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-red-100 text-brand text-xs font-black shadow-sm"> <span>⚙️</span> Administration
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight mt-4"> Payment Requests</h1>
                    <p class="mt-3 max-w-2xl text-slate-600 text-sm sm:text-base"> Review, approve or reject premium payment requests submitted by PDFToolsusers. </p>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl px-5 py-4 shadow-sm min-w-[150px]">
                    <p class="text-xs uppercase tracking-wider font-bold text-slate-400"> Total Requests </p>
                    <p class="text-3xl font-black mt-1"> {{ $payments->total() }} </p>
                </div>
            </div>
        </div>
    </section>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @if(session('success'))
         <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 flex items-start gap-3">
            <div class="w-8 h-8 shrink-0 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-black"> ✓ </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-green-800"> Success</p>
                <p class="text-sm text-green-700 mt-0.5 break-words"> {{ session('success') }} </p>
            </div>
        </div> @endif
        @if(session('error')) 
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 flex items-start gap-3">
            <div class="w-8 h-8 shrink-0 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-black"> ! </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-red-800"> Error</p>
                <p class="text-sm text-red-700 mt-0.5 break-words"> {{
                  session('error') }} </p>
            </div>
         </div> @endif <div class="space-y-5">
            @forelse($payments as $payment) 
            @php
                $paymentMethodLabels = [
                    'jazzcash' => 'JazzCash',
                    'credit_card' => 'Credit Card',
                    'bank_transfer' => 'Bank Transfer',
                ];
                $paymentMethodLabel = $paymentMethodLabels[$payment->payment_method] ?? ucfirst($payment->payment_method);
                if ($payment->bank_name !== null) {
                    $paymentMethodLabel .= ' - '.$payment->bank_name;
                }
            @endphp
            <article class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-3 sm:p-5 md:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 sm:gap-x-4 gap-y-4 sm:gap-y-5 md:grid-cols-3 xl:grid-cols-[minmax(0,1fr)_9rem_minmax(0,1.25fr)_6rem_7rem_12rem] xl:items-center xl:gap-5">
                        <div class="col-span-1 sm:col-span-2 min-w-0 md:col-span-3 xl:col-span-1">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-red-50 text-brand flex items-center justify-center font-black shrink-0"> {{ strtoupper(substr($payment->user->name ?? 'U', 0, 1)) }} </div>
                                <div class="min-w-0">
                                    <p class="text-xs uppercase tracking-wider font-bold text-slate-400"> User </p>
                                    <p class="font-black text-slate-900 truncate"> {{ $payment->user->name }} </p>
                                    <p class="text-xs text-slate-500 truncate"> {{ $payment->user->email }} </p>
                                </div>
                            </div>
                        </div>

                         <!-- METHOD -->
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wider font-bold text-slate-400"> Method </p>
                            <div class="inline-flex items-center gap-2 mt-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold"> <span>💳</span> {{ $paymentMethodLabel }} </div>
                            @if($payment->account_holder_name)
                            <p class="mt-2 text-xs text-slate-500">Account holder: {{ $payment->account_holder_name }}</p>
                            @endif
                        </div>

                         <!-- TRANSACTION -->
                        <div class="col-span-1 sm:col-span-2 min-w-0 md:col-span-2 xl:col-span-1">
                            <p class="text-xs uppercase tracking-wider font-bold text-slate-400"> Transaction ID </p>
                            <p class="font-bold text-sm mt-2 break-all"> {{ $payment->transaction_id }} </p>
                            @if($payment->proof_path)
                            <a href="{{ route('admin.payments.proof', $payment) }}" class="mt-2 inline-flex text-sm font-bold text-brand underline">Download payment proof</a>
                            <a href="{{ route('payment.receipt', $payment) }}" class="mt-2 inline-flex text-sm font-bold text-brand underline">Download payment slip</a>
                            @endif
                        </div> 

                        <!-- AMOUNT -->
                        <div class="min-w-0">
                            <p class="text-xs uppercase  tracking-wider font-bold text-slate-400"> Amount </p>
                            <p class="text-xl font-black mt-1"> ${{ number_format($payment->amount, 2) }} </p>
                        </div>

                        <!--STATUS -->
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wider font-bold text-slate-400"> Status </p> @if($payment->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-100 text-amber-700 text-xs font-black">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"> </span> Pending </span>
                            @elseif($payment->status === 'approved')
                             <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 rounded-full bg-green-50 border border-green-100 text-green-700 text-xs font-black">
                                 <span class="w-1.5 h-1.5 rounded-full bg-green-500"> </span> Approved </span>
                                  @else <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 rounded-full bg-red-50 border border-red-100 text-red-700 text-xs font-black"> 
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"> </span> Rejected </span> @endif
                        </div>

                        <!--ACTIONS -->
                        <div class="col-span-1 sm:col-span-2 min-w-0 md:col-span-3 xl:col-span-1">
                             @if($payment->status  === 'pending') 

                            <div class="flex flex-col gap-2">
                                <!-- APPROVE -->
                                <form action="{{ route('admin.payments.approve', $payment) }}" method="POST"
                                    data-payment-confirm="approve"
                                    data-payment-user="{{ $payment->user->name ?? 'User' }}"
                                    data-payment-method="{{ $paymentMethodLabel }}"
                                    data-payment-transaction="{{ $payment->transaction_id }}"
                                    data-payment-amount="${{ number_format($payment->amount, 2) }}">
                                    @csrf
                                  <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 active:bg-green-800
                                 text-white px-4 py-2.5 rounded-xl text-sm font-black transition  shadow-sm"> ✓ Approve Payment </button> 
                                </form> 

                                <!-- REJECT -->
                                <form action="{{ route('admin.payments.reject', $payment) }}" method="POST"
                                    data-payment-confirm="reject"
                                    data-payment-user="{{ $payment->user->name ?? 'User' }}"
                                    data-payment-method="{{ $paymentMethodLabel }}"
                                    data-payment-transaction="{{ $payment->transaction_id }}"
                                    data-payment-amount="${{ number_format($payment->amount, 2) }}">
                                     @csrf
                                      <input type="text" name="admin_note" placeholder="Reason (optional)" 
                                      class="w-full border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-300 focus:ring-2 focus:ring-red-100 outline-none rounded-xl px-3 py-2.5 text-xs mb-2 transition"> 
                                      <button type="submit"class="w-full inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white px-4 py-2.5 rounded-xl text-sm font-black transition shadow-sm"> ✕ Reject Payment </button> </form>
                             </div> 
                            @else 
                            <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-center">
                                <p class="text-xs font-bold text-slate-400"> ✓ Processed </p>
                            </div>
                             @endif 
                            </div>
                    </div>
                     <!-- ADMIN NOTE -->
                       @if($payment->admin_note)
                        <div class="mt-5 pt-5 border-t border-slate-100">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                            <p class="text-xs uppercase tracking-wider font-black text-slate-400"> Admin Note </p>
                            <p class="text-sm text-slate-600 mt-1">

                                 {{ $payment->admin_note }} </p>
                        </div>
                    </div>
                     @endif
                </div>
            </article>
             @empty
            <!-- EMPTY -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 md:p-12 lg:p-16 text-center shadow-sm">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-red-50 flex items-center justify-center text-3xl"> 💳 </div>
                <h2 class="text-xl sm:text-2xl font-black mt-5"> No Payment Requests </h2>
                <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto"> New premium payment requests submitted by users will appear here. </p>
            </div>
            @endforelse
        </div>
         @if($payments->hasPages()) 
         <div class="mt-8 bg-white border border-slate-200 rounded-2xl px-4 py-3 overflow-x-auto">

             {{$payments->links() }}
 </div>
  @endif
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
    <div id="paymentConfirmDialog" class="hidden fixed inset-0 z-[100] items-center justify-center bg-slate-950/55 p-3 backdrop-blur-sm sm:p-4" role="alertdialog" aria-modal="true" aria-hidden="true"
        aria-labelledby="paymentConfirmTitle" aria-describedby="paymentConfirmDescription">
        <div class="payment-confirm-card w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl sm:p-5">
            <div id="paymentConfirmIcon" class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-xl text-emerald-700"> ✓ </div>
            <p id="paymentConfirmEyebrow" class="mt-3 text-xs font-black uppercase tracking-wider text-emerald-700"> Payment approval</p>
            <h2 id="paymentConfirmTitle" class="mt-1 text-lg font-black text-slate-900 sm:text-xl">Approve this payment?
            </h2>
            <p id="paymentConfirmDescription" class="mt-1 text-sm leading-5 text-slate-600"> Premium access will be activated for this user. </p>
            <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-slate-500">User</dt>
                    <dd id="paymentConfirmUser" class="mt-1 break-words font-bold text-slate-900"></dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Amount</dt>
                    <dd id="paymentConfirmAmount" class="mt-1 font-black text-slate-900"></dd>
                </div>
                <div class="col-span-2 min-w-0">
                    <dt class="text-xs font-semibold text-slate-500">Method · Transaction</dt>
                    <dd id="paymentConfirmReference" class="mt-1 break-all font-semibold text-slate-700"></dd>
                </div>
            </dl>
            <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end sm:gap-3">
                 <button id="paymentConfirmCancel" type="button" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">  Cancel </button> 
                <button id="paymentConfirmSubmit" type="button" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white hover:bg-emerald-700"> Approve Payment </button> </div>
        </div>
    </div>

    <script>
    const paymentConfirmDialog = document.getElementById('paymentConfirmDialog');
    const paymentConfirmCancel = document.getElementById('paymentConfirmCancel');
    const paymentConfirmSubmit = document.getElementById('paymentConfirmSubmit');
    let pendingPaymentForm = null;
    let confirmedPaymentForm = null;
    let previousPaymentFocus = null;

    function closePaymentConfirmation() {
        paymentConfirmDialog.classList.add('hidden');
        paymentConfirmDialog.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        pendingPaymentForm = null;
        if (previousPaymentFocus instanceof HTMLElement) previousPaymentFocus.focus();
    }

    document.querySelectorAll('form[data-payment-confirm]').forEach(form => {
        form.addEventListener('submit', event => {
            if (confirmedPaymentForm === form) {
                confirmedPaymentForm = null;
                return;
            }

            event.preventDefault();
            pendingPaymentForm = form;
            previousPaymentFocus = document.activeElement;
            const isApproval = form.dataset.paymentConfirm === 'approve';

            document.getElementById('paymentConfirmIcon').className = isApproval ?
                'flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-2xl text-emerald-700' :
                'flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-2xl text-red-700';
            document.getElementById('paymentConfirmIcon').textContent = isApproval ? '✓' : '✕';
            document.getElementById('paymentConfirmEyebrow').className = isApproval ?
                'mt-4 text-xs font-black uppercase tracking-wider text-emerald-700' :
                'mt-4 text-xs font-black uppercase tracking-wider text-red-700';
            document.getElementById('paymentConfirmEyebrow').textContent = isApproval ?
                'Payment approval' : 'Payment rejection';
            document.getElementById('paymentConfirmTitle').textContent = isApproval ?
                'Approve this payment?' : 'Reject this payment?';
            document.getElementById('paymentConfirmDescription').textContent = isApproval ?
                'Premium access will be activated for this user.' :
                'This payment request will be marked as rejected.';
            document.getElementById('paymentConfirmUser').textContent = form.dataset.paymentUser;
            document.getElementById('paymentConfirmAmount').textContent = form.dataset.paymentAmount;
            document.getElementById('paymentConfirmReference').textContent =
                form.dataset.paymentMethod + ' · ' + form.dataset.paymentTransaction;
            paymentConfirmSubmit.className = isApproval ?
                'rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white hover:bg-emerald-700' :
                'rounded-xl bg-red-600 px-5 py-3 text-sm font-black text-white hover:bg-red-700';
            paymentConfirmSubmit.textContent = isApproval ? 'Approve Payment' : 'Reject Payment';

            paymentConfirmDialog.classList.remove('hidden');
            paymentConfirmDialog.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            paymentConfirmCancel.focus();
        });
    });

    paymentConfirmCancel.addEventListener('click', closePaymentConfirmation);
    paymentConfirmDialog.addEventListener('click', event => {
        if (event.target === paymentConfirmDialog) closePaymentConfirmation();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && pendingPaymentForm) closePaymentConfirmation();
    });
    paymentConfirmSubmit.addEventListener('click', () => {
        if (!pendingPaymentForm) return;
        const form = pendingPaymentForm;
        pendingPaymentForm = null;
        confirmedPaymentForm = form;
        closePaymentConfirmation();
        form.requestSubmit();
    });
    </script>

</body>

</html>