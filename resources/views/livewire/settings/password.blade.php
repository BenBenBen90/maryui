<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    {{-- Heade --}}
    <x-header title="Settings" separator />

    {{-- Tab --}}
    <div role="tablist" class="abs tabs-border">
        <a role="tab" class="tab" href="{{ @route('settings.profile') }}">Profile</a>
        <a role="tab" class="tab tab-active">Password</a>
    </div>

    <h1>Password</h1>
</div>
