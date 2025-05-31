<?php

use App\Models\User;
use App\Models\Country;
use App\Models\Language;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new class extends Component {

    use Toast, WithFileUploads;

    // Component parameter
    public User $user;

    public string $name = '';
    public string $email = '';
    public ?int $country_id = null;
    public $photo;
    public array $my_languages = [];
    public ?string $bio = null;

    protected function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'country_id' => 'sometimes|nullable|integer',
            'photo' => 'nullable|image|max:1024',
            'my_languages' => 'required',
            'bio' => 'sometimes'
        ];
    }

    // Fill the combobox with countries
    public function with(): array
    {
        return [
            'countries' => Country::all(),
            'languages' => Language::all()
        ];
    }

    // This is a Livewire magic method that gets called automatically whenever a public
    // property is updated (i.e., changed on the frontend).
    // Example: If you have this field:
    // <x-input wire:model="email" />
    // And the user types into it, Livewire triggers:
    // updated('email')
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Fill automatically all the component's properties
    // that match with the target model fields.
    public function mount(): void
    {
        $this->fill($this->user);

        // This assigns the selected language IDs to your my_languages array
        // so <x-choices-offline> can show them as pre-selected.
        $this->my_languages = $this->user->languages()->pluck('id')->toArray();
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Update
        $this->user->update($data);

        // Sync selection
        // Take the array in $this->my_languages and
        // replace the user's current language associations with it.
        // If: $this->my_languages = [2, 4, 6];
        // Then laravel does:
        // Delete any existing rows in language_user for this user
        // Then inserts the new one
        $this->user->languages()->sync($this->my_languages);

        // Upload file and save the avatar 'url' on User model
        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        // You can toast and redirect to any route
        $this->success('User updated with success.', redirectTo: route('users.index'));
    }
}; ?>

<div>
    <x-header title="Update {{ $user->name }}" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from user" size="text-lg" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-36 rounded-lg" />
                </x-file>
                <x-input label="Name" wire:model="name" />
                <x-input label="Email" wire:model="email" />
                <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="---" />

                {{-- Multiple section --}}
                <x-choices-offline
                    label="My languages"
                    wire:model="my_languages"
                    :options="$languages"
                    seachable
                />
            </div>
        </div>

        {{--  Details section --}}
        <hr class="my-5 border-base-300" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the user" size="text-lg" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-editor wire:model="bio" label="Biography" hint="The great biography" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" :link="route('users.index')" />

            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
