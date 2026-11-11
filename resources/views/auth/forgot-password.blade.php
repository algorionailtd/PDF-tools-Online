
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forgot Password - PDFTools</title>
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
            <div class="inline-flex bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-xs font-bold mb-2">  PDFTools </div>
            <h1 class="text-xl font-bold">Forgot Password?</h1>
            <p class="text-slate-500 text-xs mt-1">  Enter your email to reset your password </p>
        </div>

        <x-auth-session-status class="mb-3" :status="session('status')" />

        <form method="POST" action="{{ route('password.email', absolute: false) }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold mb-1.5"> Email Address </label>
                <input id="email" type="email" name="email" value="{{old('email')}}" required autofocus class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">

                @error('email')
                <p class="text-red-500 text-[11px] mt-1">{{$message}}</p>
                @enderror
            </div>

            <button type="submit"  class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-xs font-bold transition">  Email Password Reset Link  </button>
        </form>

        <div class="text-center mt-5 pt-4 border-t border-slate-100">
            <a href="{{ route('login', absolute: false) }}" class="text-blue-600 text-xs font-bold hover:underline">  ← Back to Sign In </a>
        </div>
    </div>
</body>
</html>