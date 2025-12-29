<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Setup Your Account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please set a strong password and configure MFA to continue.
            </p>
        </div>
        
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="submit" class="mt-8 space-y-6">
            
            <!-- Password Section -->
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input wire:model.live="password" id="password" name="password" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Target: 20+ chars, A-Z, 0-9, symbol">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Confirm your password">
                </div>
            </div>

            <!-- MFA Section -->
            <div class="border-t pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Multi-Factor Authentication</h3>
                <p class="text-sm text-gray-500 mb-4">Scan the QR code below with your authenticator app (e.g. Google Authenticator).</p>
                
                <div class="flex justify-center mb-4">
                    @if($qr_code_url)
                        <div class="p-2 bg-white border rounded">
                             {!! $qr_code_url !!}
                        </div>
                    @else
                        Loading QR...
                    @endif
                </div>

                <div class="mb-4">
                    <label for="mfa_code" class="block text-sm font-medium text-gray-700">Enter One-Time Pin</label>
                    <input wire:model="mfa_code" id="mfa_code" name="mfa_code" type="text" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="123456">
                    @error('mfa_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Complete Setup
                </button>
            </div>
        </form>
    </div>
</div>
