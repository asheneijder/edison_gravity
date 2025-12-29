<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MFA Setup - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl shadow-2xl p-8 text-white">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-500">Enable 2FA</h2>
        
        <div class="mb-6 text-center">
            <p class="text-sm text-gray-300 mb-4">Scan this QR code with your Google Authenticator app.</p>
            <div class="inline-block bg-white p-2 rounded-lg">
                {!! $QR_Image !!}
            </div>
            <p class="mt-4 text-xs text-gray-400 font-mono select-all">{{ $secret }}</p>
        </div>

        <form action="{{ route('mfa.enable') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="secret" value="{{ $secret }}">
            
            <div>
                <label for="otp" class="block text-sm font-medium mb-1">Enter Code</label>
                <input type="text" name="otp" id="otp" required autofocus
                    class="w-full px-4 py-2 bg-white/20 border border-white/10 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-white placeholder-gray-400"
                    placeholder="123456">
                @if(session('error'))
                    <p class="text-red-400 text-sm mt-1">{{ session('error') }}</p>
                @endif
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Enable & Login
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
