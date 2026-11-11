<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - PDFTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: '#f97316',
                    brandDark: '#ea580c'
                }
            }
        }
    };
    </script>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system,
            BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .register-card {
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.10), 0 5px 15px rgba(15, 23, 42, 0.05);
    }

    .orange-btn {
        transition: all .2s ease;
    }

    .orange-btn:hover {
        transform: translateY(-1px);
    }

    .orange-btn:active {
        transform: translateY(0);
    }
    </style>
</head>

<body class="min-h-screen bg-orange-50/40">
    <!-- HEADER -->
    <header class="border-b border-orange-100 bg-white">
        <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-5 sm:px-6">
            <!-- LOGO -->
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                </div>
                <div>
                    <div class="text-base font-extrabold tracking-tight text-slate-900">
                        PDF<span class="text-orange-500">Tools</span>
                    </div>
                    <div class="-mt-1 text-[9px] font-medium text-slate-400"> Every PDF tool </div>
                </div>
            </a>
            <!-- HOME -->
            <a href="{{ url('/') }}" class="text-xs font-semibold text-slate-500 transition hover:text-orange-500"> Back
                to Home </a>
        </div>
    </header>
    <!-- MAIN -->
    <main class="relative flex min-h-[calc(100vh-56px)] items-center justify-center overflow-hidden px-4 py-8">

        <!-- BACKGROUND -->

        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-24 -top-24 h-64 w-64 rounded-full bg-orange-200/40 blur-3xl">
            </div>
            <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-orange-100/70 blur-3xl">
            </div>
        </div>
        <!-- REGISTER CARD -->
        <section
            class="register-card relative z-10 w-full max-w-xs rounded-xl border border-orange-100 bg-white p-4 sm:p-5">

            <!-- ICON + TITLE -->
            <div class="mb-3 text-center">
                <div
                    class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-xl bg-orange-100 text-orange-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <path d="M8 13h8" />
                        <path d="M8 17h6" />
                    </svg>
                </div>
                <h1 class="text-lg font-extrabold tracking-tight text-slate-900"> Create Account </h1>
                <p class="mt-0.5 text-[11px] leading-4 text-slate-500">Join PDFTools today </p>
            </div>
            <!-- REGISTER FORM -->

            <form method="POST" action="{{ route('register', absolute: false) }}" class="space-y-3"> @csrf

                <!-- NAME -->
                <div>
                    <label for="name" class="mb-1 block text-[11px] font-bold text-slate-700"> Full Name </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required
                        autofocus placeholder="Enter your full name"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 outline-none placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                    @error('name')
                    <p class="mt-1 text-[11px] text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <!-- EMAIL -->
                <div> <label for="email" class="mb-1 block text-[11px] font-bold text-slate-700"> Email Address </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 outline-none placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                    @error('email')
                    <p class="mt-1 text-[11px] text-red-600"> {{ $message }}
                    </p> @enderror
                </div> <!-- PASSWORD -->
                <div> <label for="password" class="mb-1 block text-[11px] font-bold text-slate-700"> Password </label>
                    <div class="relative"> <input id="password" type="password" name="password"
                            autocomplete="new-password" required placeholder="Create a password"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 pr-12 text-xs text-slate-900 outline-none placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                        <button type="button" onclick="togglePassword('password', 'passwordToggle')" id="passwordToggle"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-orange-500 hover:text-orange-600">
                            Show </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-[11px] text-red-600"> {{ $message }}
                    </p> @enderror
                </div>
                <!-- CONFIRM PASSWORD -->
                <div>
                    <label for="password_confirmation" class="mb-1 block text-[11px] font-bold text-slate-700"> Confirm
                        Password </label>
                    <div class="relative"> <input id="password_confirmation" type="password"
                            name="password_confirmation" autocomplete="new-password" required
                            placeholder="Repeat your password"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 pr-12 text-xs text-slate-900 outline-none placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                        <button type="button" onclick="togglePassword('password_confirmation', 'confirmToggle')"
                            id="confirmToggle"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-orange-500 hover:text-orange-600">
                            Show </button>
                    </div>
                </div>
                <!-- TERMS -->

                <label class="flex items-start gap-2 text-[11px] leading-4 text-slate-500">
                    <input type="checkbox" required
                        class="mt-0.5 h-3.5 w-3.5 rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                    <span> I agree to the<span class="font-semibold text-orange-500"> Terms & Conditions </span> </span>
                </label>

                <!-- CREATE ACCOUNT -->
                <button type="submit"
                    class="orange-btn flex w-full items-center justify-center rounded-lg bg-orange-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-orange-600">
                    Create Account </button>
            </form>
            <!-- LOGIN LINK -->

            <div class="mt-3 border-t border-slate-100 pt-3 text-center">
                <p class="text-xs text-slate-500"> Already have an account?
                    <a href="{{ route('login', absolute: false) }}"
                        class="font-bold text-orange-500 hover:text-orange-600 hover:underline"> Sign In </a>
                </p>
            </div> <!-- SECURITY -->
            <div class="mt-3 flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="10" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg> <span> Secure account creation</span>
            </div>
        </section>
    </main>

    <!-- SHOW / HIDE PASSWORD -->

    <script>
    function togglePassword(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button =
            document.getElementById(buttonId);
        if (input.type === "password") {
            input.type = "text";
            button.textContent = "Hide";
        } else {
            input.type = "password";
            button.textContent = "Show";
        }
    }
    </script>
</body>

</html>