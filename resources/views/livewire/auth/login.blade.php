<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string')]
    public string $login = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $loginField = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$loginField => $this->login, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Username/email atau password tidak sesuai.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $targetRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 'pasien.dashboard';

        $this->redirectIntended(default: route($targetRoute, absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login).'|'.request()->ip());
    }
}; ?>

<div class="relative overflow-hidden rounded-3xl border border-sky-100 bg-white/95 p-8 shadow-2xl shadow-sky-200/60 backdrop-blur dark:border-sky-800/60 dark:bg-zinc-900/95 dark:shadow-sky-950/40">
    <div class="pointer-events-none absolute -top-16 -right-16 h-48 w-48 rounded-full bg-cyan-200/40 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-sky-300/30 blur-3xl"></div>

    <div class="relative flex flex-col gap-6">
        <div class="space-y-2 text-center">
            <p class="text-xs font-semibold tracking-[0.35em] text-sky-600">RSUD WELASASIH • PROV JAWA BARAT</p>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Portal Layanan Rumah Sakit Bintang 5</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-300">Masuk sebagai Admin atau Pasien untuk mengelola layanan kesehatan profesional.</p>
        </div>

        <x-auth-session-status class="rounded-xl bg-emerald-50 px-4 py-2 text-center text-sm text-emerald-700" :status="session('status')" />

        <form wire:submit="login" class="flex flex-col gap-5">
            <flux:input wire:model="login" label="Username atau Email" type="text" name="login" required autofocus autocomplete="username" placeholder="Contoh: Admin atau email@domain.com" />

            <flux:input
                wire:model="password"
                label="Password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
            />

            <div class="flex items-center justify-between text-sm">
                <flux:checkbox wire:model="remember" label="Ingat saya" />
                @if (Route::has('password.request'))
                    <x-text-link href="{{ route('password.request') }}">Lupa password?</x-text-link>
                @endif
            </div>

            <flux:button variant="primary" type="submit" class="w-full rounded-xl bg-gradient-to-r from-sky-600 to-cyan-500 text-base font-semibold text-white">
                Masuk ke Dashboard
            </flux:button>
        </form>

        <p class="text-center text-sm text-zinc-600 dark:text-zinc-300">
            Belum punya akun pasien?
            <x-text-link href="{{ route('register') }}">Daftar sekarang</x-text-link>
        </p>
    </div>
</div>
