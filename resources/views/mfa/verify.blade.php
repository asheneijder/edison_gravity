<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MFA Verify - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl shadow-2xl p-8 text-white">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-500">Security Check</h2>
        
        <p class="text-sm text-gray-300 mb-6 text-center">Please enter the code from your Authenticator app.</p>

        <form action="{{ route('mfa.verify.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="otp" class="block text-sm font-medium mb-1">One-Time Password</label>
                <input type="text" name="otp" id="otp" required autofocus autocomplete="one-time-code"
                    class="w-full px-4 py-2 bg-white/20 border border-white/10 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-white placeholder-gray-400"
                    placeholder="123456">
                @if(session('error'))
                    <p class="text-red-400 text-sm mt-1">{{ session('error') }}</p>
                @endif
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Verify
            </button>
        </form>

        <form action="{{ route('mfa.logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" 
                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Back to Login
            </button>
        </form>
    </div>
</body>
</html>
