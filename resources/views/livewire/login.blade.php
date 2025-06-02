<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
// Specifies that the component should use the components.layouts.empty
#[Layout('components.layouts.empty')]

// Sets the page title to "Login"
#[Title('Login')]
class extends Component {

    public string $email = '';
    public string $password = '';

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function mount()
    {
        // If already logged in, redirect to home
        if (auth()->user()) {
            return redirect('/');
        }
    }

    public function login()
    {
        $credentials = $this->validate();

        if (auth()->attempt($credentials)) {
            // After a user logs in (or registers),
            // Laravel regenerates the session ID to prevent session fixation attacks.
            // A session fixation attack is where a malicious user tricks
            // a victim into using a known session ID — and then hijacks it.
            // By regenerating the session, Laravel ensures:
            // The old session ID is invalidated
            // A new secure session is created for the user
            request()->session()->regenerate();

            return redirect()->intended('/');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }
}; ?>

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 mt-10">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <x-app-brand />
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <x-form class="space-y-6" wire:submit="login">
            <x-input placeholder="E-mail" wire:model="email" icon="o-envelope" />
            <x-input placeholder="Password" wire:model="password" type="password" icon="o-key" />

            <x-slot:actions>
                <x-button label="Create an account" class="btn-ghost" link="/register" />
                <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login" />
            </x-slot:actions>
        </x-form>
    </div>
</div>





