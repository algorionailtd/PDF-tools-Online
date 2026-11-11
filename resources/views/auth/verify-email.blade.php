<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Verify Email - PDFTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Inter, Arial, sans-serif
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 flex items-center justify-center px-4">
    <div class="w-full max-w-xs bg-white border border-slate-200 rounded-xl shadow-sm p-4">
        <div class="text-center mb-5">
            <div class="inline-flex bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-xs font-bold mb-2">  PDFTools  </div>
            <h1 class="text-xl font-bold">Verify Email</h1>
            <p class="text-slate-500 text-xs mt-1"> Check your inbox to continue </p>
        </div>

        @if(session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-xs">
            {{__('Registration successful. A verification email has been sent to your email address. Please click the link in that email to continue.')}}
        </div>
        @else
        <div class="mb-4 p-3 rounded-lg bg-slate-50 text-slate-600 text-xs leading-5">
            {{__('Please verify your email address by clicking the link we emailed to you. If you did not receive the email, we will gladly send you another.')}}
        </div>
        @endif

        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send', absolute: false) }}">
                @csrf
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-xs font-bold transition"> Resend Verification Email </button>
            </form>

            <form method="POST" action="{{ route('logout', absolute: false) }}">
                @csrf
                <button type="submit" class="w-full border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 py-2.5 rounded-lg text-xs font-semibold transition"> Log Out </button>
            </form>
        </div>
    </div>
</body>
</html>