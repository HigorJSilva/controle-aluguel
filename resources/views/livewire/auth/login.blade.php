<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Enums\SocialiteProviders;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

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

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('messages.log_in_title')"
                   :description="__('messages.log_in_description')"/>

    <x-auth-session-status class="text-center" :status="session('status')"/>

    <x-auth-session-status class="text-center" :status="session('error')" type="error"/>

    <form wire:submit="login" class="flex flex-col gap-6">
        <x-mary-input :label="__('messages.input_email_label')" wire:model="email" placeholder="__('messages.input_email_placeholder')" type="email"
                      required
                      autofocus autocomplete="email"/>

        <div class="relative">
            <x-mary-password wire:model="password" :placeholder="__('messages.input_password_label')" :label="__('messages.input_password_placeholder')" required
                             right/>
            @if (Route::has('password.request'))
                <div class="absolute end-0 top-0 text-sm">
                    <x-mary-button :label="__('messages.forgot_password')" :link="route('password.request')"
                                   class="btn-link link-accent link-hover pr-0"/>
                </div>
            @endif
        </div>

        <x-mary-checkbox :label="__('messages.remember_me')" wire:model="remember"/>

        <x-mary-button type="submit" :label="__('messages.log_in_button')" class="btn-accent"/>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-base-content">
            <div class="flex justify-center items-center gap-1">
                {{ __('messages.dont_have_account') }}
                <x-mary-button :label="__('messages.sign_up')" :link="route('register')"
                               class="btn-link link-accent link-hover pl-0"/>
            </div>
            <div class="divider">{{__('messages.or')}}</div>
            <a href="{{ route('oauth.redirect', ['provider' => SocialiteProviders::GOOGLE]) }}"
               class="btn w-full bg-white hover:opacity-90 text-black border-[#e5e5e5]">
                <svg aria-label="Google logo" width="16" height="16" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 512 512">
                    <g>
                        <path d="m0 0H512V512H0" fill="#fff"></path>
                        <path fill="#34a853" d="M153 292c30 82 118 95 171 60h62v48A192 192 0 0190 341"></path>
                        <path fill="#4285f4" d="m386 400a140 175 0 0053-179H260v74h102q-7 37-38 57"></path>
                        <path fill="#fbbc02" d="m90 341a208 200 0 010-171l63 49q-12 37 0 73"></path>
                        <path fill="#ea4335" d="m153 219c22-69 116-109 179-50l55-54c-78-75-230-72-297 55"></path>
                    </g>
                </svg>
                {{ __('messages.log_in_google') }}
            </a>
        </div>
    @endif
</div>
