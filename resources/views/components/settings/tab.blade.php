{{-- This makes $profile and $password available inside your component. --}}
@props(['profile' => '', 'password' => ''])

<div>
    <x-header title="Settings" separator />

    <x-tabs
        wire:model="selectedTab"
        active-class="bg-primary rounded !text-white"
        label-class="font-semibold"
        label-div-class="bg-primary/5 rounded w-fit p-2"
    >
        <x-tab name="profile-tab" label="Profile" icon="o-user">
            {{-- Use {!! ... !!} to render sub-views properly. --}}
            {{--  {{ $profile }} escapes the output, which means the inner HTML won't render. --}}
            {!! $profile !!}
        </x-tab>
        <x-tab name="password-tab" label="Password" icon="o-key">
            {!! $password !!}
        </x-tab>
    </x-tabs>
</div>
