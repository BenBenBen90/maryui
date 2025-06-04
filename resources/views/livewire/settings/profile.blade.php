<?php

use App\Models\User;
use App\Models\Country;
use App\Models\Language;
use Mary\Traits\Toast;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {

    use Toast;

    public string $name = '';
    public string $email = '';

    protected function rules()
    {
        $user = Auth::user(); // ✅ Now $user is defined

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ];
    }

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save(): void
    {
        $user = Auth::user();

        $data = $this->validate();

        // It assigns values from the array to the model only for the
        // fields that are listed in the model’s $fillable property.
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // fill() + save()
        // This allows modifying attributes before saving —
        // like resetting email_verified_at only if the email was changed.
        // This $user->update($data);
        // This is a shortcut for: $user->fill($validated)->save();
        // In short if update() ang ginamit, di ka pwede mag add ng logic.
        $user->save();

        $this->success('Profile updated successfully.', redirectTo: route('settings.profile'));
    }
}; ?>


<div>
    {{-- Header --}}
    <x-header title="Settings" separator />

    {{-- Tab --}}
    <div role="tablist" class="abs tabs-border mb-5">
        <a role="tab" class="tab tab-active">Profile</a>
        <a role="tab" class="tab" href="{{ @route('settings.password') }}">Password</a>
    </div>

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-3">
                <x-header title="Name & Email" subtitle="Update your name and email address" size="text-lg" />
            </div>
            <div class="col-span-2 grid gap-3">
                <x-input label="Name" wire:model="name" />
                <x-input label="Email" wire:model="email" />
            </div>
        </div>
        <x-slot:actions>
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
