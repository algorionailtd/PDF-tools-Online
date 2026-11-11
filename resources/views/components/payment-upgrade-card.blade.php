@props(['id' => 'paymentBox'])

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'hidden mt-6 rounded-3xl border border-red-200 bg-white shadow-lg overflow-hidden']) }}>
    <div class="bg-red-50 border-b border-red-100 p-5 sm:p-6">
        <p class="text-xs font-black uppercase tracking-wider text-brand">Upgrade Required</p>
        <h3 class="mt-1 text-xl sm:text-2xl font-black">Free uploads finished</h3>
        <p class="mt-2 text-sm sm:text-base text-slate-600">Pay $2/month to unlock unlimited PDF tools.</p>
    </div>

    <div class="p-5 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="text-2xl">📱</div>
                <p class="mt-2 text-xl font-black">JazzCash</p>
                <p class="text-sm text-slate-500">Mobile wallet</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="text-2xl">💳</div>
                <p class="mt-2 text-xl font-black">Credit Card</p>
                <p class="text-sm text-slate-500">Manual verification</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="text-2xl">🏦</div>
                <p class="mt-2 text-xl font-black">Bank Transfer</p>
                <p class="text-sm text-slate-500">Any bank</p>
            </div>
        </div>

        <a href="{{ route('payment') }}"
            class="mt-5 flex w-full items-center justify-center rounded-xl bg-brand py-4 text-center text-lg font-black text-white transition hover:bg-brandDark">
            Continue to Payment <span class="ml-2" aria-hidden="true">→</span>
        </a>
    </div>
</div>
