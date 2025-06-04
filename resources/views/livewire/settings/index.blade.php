<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-settings.tab
    :profile="view('livewire.settings.profile')"
    :password="view('livewire.settings.password')"
/>


