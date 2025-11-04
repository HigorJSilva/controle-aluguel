<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $passwordRules = ['required', 'string', 'confirmed', Rules\Password::defaults()];

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => $passwordRules,
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('messages.create_account_title')" :description="__('messages.create_account_description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <x-mary-input :label="__('messages.input_name_label')" wire:model="name" :placeholder="__('messages.input_name_placeholder')" type="text" required autofocus
            autocomplete="name" />

        <x-mary-input :label="__('messages.input_email_label')" wire:model="email" :placeholder="__('messages.input_email_placeholder')" type="email" required
            autocomplete="email" />

        <x-mary-password :label="__('messages.input_password_label')" wire:model="password" :placeholder="__('messages.input_password_placeholder')" required right
            autocomplete="new-password" />

        <x-mary-password :label="__('messages.input_password_confirmation_label')" wire:model="password_confirmation" :placeholder="__('messages.input_password_confirmation_placeholder')" required right
            autocomplete="new-password" />

        <x-mary-button type="submit" :label="__('messages.create_account_button')" class="btn-accent" />
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-base-content">
        {{ __('messages.already_have_account') }}
        <x-mary-button :label="__('messages.log_in_button')" :link="route('login')" class="btn-link link-accent link-hover pl-0" />
    </div>
</div>
