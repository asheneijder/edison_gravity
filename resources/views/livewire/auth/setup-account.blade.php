<style>
    .setup-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1rem;
    }
    .setup-card {
        background: var(--card-bg);
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        width: 100%;
        max-width: 480px;
    }
    .setup-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .setup-header h2 {
        font-size: 1.875rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 0.5rem 0;
    }
    .setup-header p {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin: 0;
    }
    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }
    .form-input {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--border-color);
        background-color: var(--input-bg);
        border-radius: 6px;
        font-size: 0.875rem;
        color: var(--text-main);
        box-sizing: border-box; /* Crucial for width: 100% */
        transition: border-color 0.15s ease-in-out;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    .error-msg {
        color: var(--danger);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    .section-divider {
        border-top: 1px solid var(--border-color);
        margin: 2rem 0;
        padding-top: 1.5rem;
    }
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-main);
        margin: 0 0 0.5rem 0;
    }
    .section-desc {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin: 0 0 1.5rem 0;
    }
    .qr-container {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
    }
    .qr-box {
        padding: 1rem;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }
    .btn-primary {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 0.75rem 1rem;
        border: transparent;
        border-radius: 6px;
        color: white;
        background-color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    .btn-primary:hover {
        background-color: var(--primary-hover);
    }
</style>

<div class="setup-container">
    <div class="setup-card">
        <div class="setup-header">
            <h2>Setup Your Account</h2>
            <p>Please set a strong password and configure MFA to continue.</p>
        </div>
        
        @if (session()->has('message'))
            <div class="alert-success">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="submit">
            <!-- Password Section -->
            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <input wire:model.live="password" id="password" type="password" required class="form-input" placeholder="Min 20 chars, letters, numbers & symbols">
                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input wire:model="password_confirmation" id="password_confirmation" type="password" required class="form-input" placeholder="Confirm your password">
            </div>

            <!-- MFA Section -->
            <div class="section-divider">
                <h3 class="section-title">Multi-Factor Authentication</h3>
                <p class="section-desc">Scan the QR code below with your authenticator app (e.g. Google Authenticator).</p>
                
                <div class="qr-container">
                    @if($qr_code_url)
                        <div class="qr-box">
                             {!! $qr_code_url !!}
                        </div>
                    @else
                        <div class="text-sm text-gray-500">Loading QR...</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="mfa_code" class="form-label">Enter One-Time Pin</label>
                    <input wire:model="mfa_code" id="mfa_code" type="text" required class="form-input" placeholder="123456" maxlength="6" style="text-align: center; letter-spacing: 0.1em; font-size: 1.1em;">
                    @error('mfa_code') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="btn-primary">
                Complete Setup
            </button>
        </form>
    </div>
</div>
