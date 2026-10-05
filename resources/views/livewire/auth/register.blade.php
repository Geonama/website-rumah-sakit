<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'pasien';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:40', 'alpha_dash', Rule::unique(User::class)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'pasien'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route($user->isAdmin() ? 'admin.dashboard' : 'pasien.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="relative overflow-hidden rounded-3xl border border-sky-100 bg-white/95 p-8 shadow-2xl shadow-sky-200/60 backdrop-blur dark:border-sky-800/60 dark:bg-zinc-900/95 dark:shadow-sky-950/40">
    <div class="pointer-events-none absolute -top-16 -right-16 h-48 w-48 rounded-full bg-cyan-200/40 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-sky-300/30 blur-3xl"></div>

    <div class="relative flex flex-col gap-6">
        <div class="space-y-2 text-center">
            <p class="text-xs font-semibold tracking-[0.35em] text-sky-600">RSUD WELASASIH • PROV JAWA BARAT</p>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Pendaftaran Akun Layanan Kesehatan</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-300">Buat akun Admin atau Pasien dan nikmati pengalaman digital rumah sakit profesional.</p>
        </div>

        <x-auth-session-status class="rounded-xl bg-emerald-50 px-4 py-2 text-center text-sm text-emerald-700" :status="session('status')" />

        <form wire:submit="register" class="grid gap-4">
            <flux:input wire:model="name" id="name" label="Nama Lengkap" type="text" name="name" required autofocus autocomplete="name" placeholder="Nama lengkap" />

            <flux:input wire:model="username" id="username" label="Username" type="text" name="username" required autocomplete="username" placeholder="Contoh: pasien_sehat" />

            <flux:input wire:model="email" id="email" label="Email" type="email" name="email" required autocomplete="email" placeholder="email@contoh.com" />

            <flux:select wire:model="role" label="Daftar sebagai" name="role">
                <flux:select.option value="pasien">Pasien</flux:select.option>
                <flux:select.option value="admin">Admin</flux:select.option>
            </flux:select>

            <flux:input
                wire:model="password"
                id="password"
                label="Password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
            />

            <flux:input
                wire:model="password_confirmation"
                id="password_confirmation"
                label="Konfirmasi Password"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi password"
            />

            <flux:button type="submit" variant="primary" class="mt-2 w-full rounded-xl bg-gradient-to-r from-sky-600 to-cyan-500 text-base font-semibold text-white">
                Daftar & Masuk
            </flux:button>
        </form>

        <p class="text-center text-sm text-zinc-600 dark:text-zinc-300">
            Sudah punya akun?
            <x-text-link href="{{ route('login') }}">Masuk di sini</x-text-link>
        </p>
    </div>
</div>
