<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

new
// Specifies that the component should use the components.layouts.empty
#[Layout('components.layouts.empty')]

// Sets the page title to "Register"
#[Title('Registration')]
class extends Component {

    public string $name ='';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    public function mount()
    {
        // If already logged in, redirect to home
        if (auth()->user()) {
            return redirect('/');
        }
    }

    public function register()
    {
        $data = $this->validate();

        $data['avatar'] = '/empty-user.jpg';
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        auth()->login($user);

        // After a user logs in (or registers),
        // Laravel regenerates the session ID to prevent session fixation attacks.
        // A session fixation attack is where a malicious user tricks
        // a victim into using a known session ID — and then hijacks it.
        // By regenerating the session, Laravel ensures:
        // The old session ID is invalidated
        // A new secure session is created for the user
        request()->session()->regenerate();

        return redirect('/');
    }
}; ?>

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 mt-10">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <x-app-brand />
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <x-form class="space-y-6" wire:submit="register">
            <x-input placeholder="Name" wire:model="name" icon="o-user" />
            <x-input placeholder="E-mail" wire:model="email" icon="o-envelope" />
            <x-input placeholder="Password" wire:model="password" type="password" icon="o-key" />
            <x-input placeholder="Confirm Password" wire:model="password_confirmation" type="password" icon="o-key" />

            <x-slot:actions>
                <x-button label="Already registered?" class="btn-ghost" link="/login" />
                <x-button label="Register" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="register" />
            </x-slot:actions>
        </x-form>
    </div>
</div>
