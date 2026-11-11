<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password - PDFTools</title>
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
            <div class="inline-flex bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-xs font-bold mb-2"> PDFTools </div>
            <h1 class="text-xl font-bold">Reset Password</h1>
            <p class="text-slate-500 text-xs mt-1">Create your new password</p>
        </div>

        <form method="POST" action="{{ route('password.store', absolute: false) }}" class="space-y-3">
            @csrf
            <input type="hidden" name="token" value="{{$request->route('token')}}">

            <div>
                <label class="block text-xs font-semibold mb-1">Email Address</label>
                <input type="email" name="email" value="{{old('email',$request->email)}}" required autofocus autocomplete="username" class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                @error('email')<p class="text-red-500 text-[11px] mt-1">{{$message}}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1">New Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full px-3 py-2.5 pr-12 text-xs rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    <button type="button" onclick="togglePass('password',this)" class="absolute right-3 top-2 text-blue-600 text-[11px] font-semibold">Show</button>
                </div>
                @error('password')<p class="text-red-500 text-[11px] mt-1">{{$message}}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1">Confirm Password</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"class="w-full px-3 py-2.5 pr-12 text-xs rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    <button type="button" onclick="togglePass('password_confirmation',this)" class="absolute right-3 top-2 text-blue-600 text-[11px] font-semibold">Show</button>
                </div>
                @error('password_confirmation')<p class="text-red-500 text-[11px] mt-1">{{$message}}</p>@enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-xs font-bold transition"> Reset Password  </button>

        </form>

        <div class="text-center mt-4 pt-3 border-t border-slate-100">
            <a href="{{ route('login', absolute: false) }}" class="text-blue-600 text-xs font-bold hover:underline"> ← Back to Sign In </a>
        </div>
    </div>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
            btn.innerText = input.type === "password" ? "Show" : "Hide";
        }
    </script>

</body>

</html>