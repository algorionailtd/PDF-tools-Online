<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PDFTools</title>

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
        box-sizing: border-box
    }

    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif
    }

    .login-card {
        box-shadow: 0 18px 45px rgba(15, 23, 42, .10), 0 5px 15px rgba(15, 23, 42, .05)
    }

    .google-btn,
    .primary-btn {
        transition: all .2s ease
    }

    .google-btn:hover,
    .primary-btn:hover {
        transform: translateY(-1px)
    }

    .google-btn:active,
    .primary-btn:active {
        transform: translateY(0)
    }

    .loader {
        width: 17px;
        height: 17px;
        border: 2px solid rgba(255, 255, 255, .35);
        border-top-color: white;
        border-radius: 50%;
        animation: spin .7s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    .google-icon {
        width: 19px;
        height: 19px
    }
    </style>
</head>

<body class="min-h-screen bg-orange-50/40">

    <!-- HEADER -->
    <header class="border-b border-orange-100 bg-white">
        <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-5 sm:px-6">

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
                        PDF<span class="text-brand">Tools</span>
                    </div>
                    <div class="-mt-1 text-[9px] font-medium text-slate-400">Every PDF tool</div>
                </div>
            </a>

            <a href="{{ url('/') }}" class="text-xs font-semibold text-slate-500 transition hover:text-orange-500">
                Back to Home
            </a>

        </div>
    </header>

    <!-- MAIN -->
    <main class="relative flex min-h-[calc(100vh-56px)] items-center justify-center overflow-hidden px-4 py-8">

        <!-- BACKGROUND -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-24 -top-24 h-64 w-64 rounded-full bg-orange-200/40 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-orange-100/70 blur-3xl"></div>
        </div>

        <!-- LOGIN CARD -->
        <section
            class="login-card relative z-10 w-full max-w-sm rounded-2xl border border-orange-100 bg-white p-5 sm:p-6">

            <!-- ICON -->
            <div class="mb-5 text-center">
                <div
                    class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>

                <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Welcome Back</h1>
                <p class="mt-1 text-xs leading-5 text-slate-500">Sign in to continue to PDFTools</p>
            </div>

            <!-- MESSAGE -->
            <div id="messageBox" class="mb-4 hidden rounded-lg border px-3 py-2.5 text-xs break-words"></div>

            <!-- GOOGLE -->
            <button type="button" id="googleLoginBtn"
                class="google-btn flex w-full items-center justify-center gap-3 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm hover:border-orange-200 hover:bg-orange-50/30">

                <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4"
                        d="M21.35 12.23c0-.79-.07-1.55-.22-2.27H12v4.3h5.22a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.93-4.18 2.93-7.4z" />
                    <path fill="#34A853"
                        d="M12 21.6c2.63 0 4.84-.87 6.46-2.36l-3.14-2.44c-.87.58-1.98.92-3.32.92-2.55 0-4.71-1.72-5.48-4.03H3.27v2.52A9.75 9.75 0 0 0 12 21.6z" />
                    <path fill="#FBBC05"
                        d="M6.52 13.69A5.86 5.86 0 0 1 6.2 12c0-.59.11-1.16.32-1.69V7.79H3.27A9.76 9.76 0 0 0 2.25 12c0 1.57.38 3.05 1.02 4.21l3.25-2.52z" />
                    <path fill="#EA4335"
                        d="M12 6.28c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.37 14.63 2.4 12 2.4a9.75 9.75 0 0 0-8.73 5.39l3.25 2.52C7.29 8 9.45 6.28 12 6.28z" />
                </svg>

                <span id="googleBtnText">Continue with Google</span>
            </button>

            <!-- DIVIDER -->
            <div class="my-4 flex items-center gap-3">
                <div class="h-px flex-1 bg-slate-200"></div>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                    Or continue with email
                </span>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            <!-- EMAIL LOGIN -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- EMAIL -->
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold text-slate-700">
                        Email Address
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                </div>

                <!-- PASSWORD -->
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-700">Password</label>

                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-[11px] font-bold text-orange-500 hover:text-orange-600 hover:underline">
                            Forgot password?
                        </a>
                        @endif
                    </div>

                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        placeholder="Enter your password"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                </div>

                <!-- REMEMBER -->
                <div class="flex items-center gap-2">
                    <input id="remember" name="remember" type="checkbox" value="1"
                        class="h-3.5 w-3.5 rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                    <label for="remember" class="text-xs text-slate-500">Remember me</label>
                </div>

                <!-- SIGN IN -->
                <button type="submit"
                    class="primary-btn flex w-full items-center justify-center rounded-lg bg-orange-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-orange-600">
                    Sign In
                </button>
            </form>

            <!-- REGISTER -->
            <div class="mt-5 text-center">
                <p class="text-xs text-slate-500">
                    Don't have an account?
                    <a href="{{ route('register') }}"
                        class="font-bold text-orange-500 hover:text-orange-600 hover:underline">
                        Create account
                    </a>
                </p>
            </div>

            <!-- SECURITY -->
            <div class="mt-4 flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="10" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <span>Secure authentication</span>
            </div>

        </section>
    </main>

    <!-- FIREBASE GOOGLE LOGIN -->
    <script type="module">
    const firebaseConfig = {
        apiKey: "AIzaSyCzuZPe3xQsMMFoCUjMAP6-i4SAIAo0Jc4",
        authDomain: "pdf-tools-33a07.firebaseapp.com",
        projectId: "pdf-tools-33a07",
        storageBucket: "pdf-tools-33a07.firebasestorage.app",
        messagingSenderId: "197338175040",
        appId: "1:197338175040:web:42f65bde15d220fbfdf1a5",
        measurementId: "G-6WNG5M8T7Q"
    };

    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js";
    import {
        getAuth,
        GoogleAuthProvider,
        signInWithPopup
    }
    from "https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js";

    const googleLoginBtn = document.getElementById("googleLoginBtn");
    const googleBtnText = document.getElementById("googleBtnText");
    const messageBox = document.getElementById("messageBox");

    function showMessage(message, type = "error") {
        messageBox.classList.remove(
            "hidden", "border-red-200", "bg-red-50", "text-red-700",
            "border-green-200", "bg-green-50", "text-green-700"
        );

        if (type === "success") {
            messageBox.classList.add("border-green-200", "bg-green-50", "text-green-700");
        } else {
            messageBox.classList.add("border-red-200", "bg-red-50", "text-red-700");
        }
        messageBox.textContent = message;
    }

    let app, auth;

    try {
        app = initializeApp(firebaseConfig);
        auth = getAuth(app);
        console.log("Firebase initialized successfully");
    } catch (error) {
        console.error("Firebase initialization error:", error);
        showMessage("Firebase initialization failed.", "error");
    }

    const provider = new GoogleAuthProvider();
    provider.setCustomParameters({
        prompt: "select_account"
    });

    googleLoginBtn.addEventListener("click", async function() {

        if (!auth) {
            showMessage("Firebase authentication is not available.", "error");
            return;
        }

        googleLoginBtn.disabled = true;

        googleBtnText.innerHTML = `
<span class="inline-flex items-center gap-2">
<span class="loader"></span>Signing in...
</span>`;

        messageBox.classList.add("hidden");

        try {

            const result = await signInWithPopup(auth, provider);
            const user = result.user;
            const idToken = await user.getIdToken(true);

            const laravelUrl = @json(route('firebase.google.login'));

            const response = await fetch(laravelUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    idToken: idToken
                })
            });

            const rawResponse = await response.text();
            let data = {};

            try {
                data = JSON.parse(rawResponse);
            } catch (e) {
                throw new Error(
                    "Laravel returned a non-JSON response. HTTP status: " + response.status
                );
            }

            if (!response.ok) {
                throw new Error(
                    "Laravel HTTP " + response.status + ": " +
                    (data.message || "Server error")
                );
            }

            if (!data.success) {
                throw new Error(
                    data.message || "Laravel rejected Google login."
                );
            }

            showMessage(
                "Google login successful. Redirecting...",
                "success"
            );

            window.location.href = data.redirect || "/";

        } catch (error) {

            console.error("Google Login Error:", error);

            let message = "Google login failed.";

            if (error?.code) message += " Code: " + error.code;
            if (error?.message) message += " Message: " + error.message;

            showMessage(message, "error");

            googleLoginBtn.disabled = false;
            googleBtnText.textContent = "Continue with Google";
        }
    });
    </script>

</body>

</html>