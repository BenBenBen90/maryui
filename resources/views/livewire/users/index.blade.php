<?php

use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {

    use Toast, WithPagination;

    public string $search = '';

    public bool $drawer = false;

    // Create a public property
    public int $country_id = 0;

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Count the filtered components
    public function countFilterWithVal(): int
    {
        // Original code
        /*
        $withValue = 0;
        if (!empty($this->search)) $withValue = $withValue + 1;
        if (!empty($this->country_id)) $withValue = $withValue + 1;

        return $withValue;
        */

        // Optimized
        return collect([
            $this->search,
            $this->country_id,
        ])->filter()->count();
        // The filter() method removes "falsy" values:
        // null, false, 0, & empty arrays.
    }

    // Reset pagination when any component property changes
    // A Livewire lifecycle hook
    // With $property = 'search'
    public function updated($property): void
    {
        if (! is_array($property) && $property != "") {
            // A method from the WithPagination trait
            // Resets pagination to page 1
            $this->resetPage();
        }
    }

    // Delete action
    public function delete(User $user): void
    {
        $user->delete();
        $this->warning('Record deleted', "User $user->name has been deleted.", position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'avatar', 'label' => '', 'class' => 'w-1'],
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
            ['key' => 'country_name', 'label' => 'Country', 'class' => 'hidden lg:table-cell'],
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => false],
        ];
    }

    /**
     * For demo purpose, this is a static collection.
     *
     * On real projects you do it with Eloquent collections.
     * Please, refer to maryUI docs to see the eloquent examples.
     */
    public function users(): LengthAwarePaginator //Collection
    {
        /*
            this:

            ->withAggregate('country', 'name')

            is equivalent to:

            ->addSelect([
                'country_name' => Country::select('name')
                    ->whereColumn('countries.id', 'users.country_id')
                    ->limit(1)
            ])

            Why Is the Column Called country_name?

            Laravel builds the column name like this:
            <relationship>_<column>

            So:
            withAggregate('country', 'name')

            Results in:
            country_name
        */
        return User::query()
            ->withAggregate('country', 'name')
            ->with(['country'])
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->when($this->country_id, fn(Builder $q) => $q->where('country_id', $this->country_id))
            // In PHP, the ... operator (called the "splat" operator) is used to:
            // Unpack an array into individual arguments when calling a function or method.
            ->orderBy(...array_values($this->sortBy))
            //->get()
            ->paginate(5);
    }

    // Part of Livewire Volt components (introduced in Livewire v3).
    // It’s a special method used to pass data to the view.
    // In Volt, the with() method returns an array of variables that are injected into
    // the Blade view automatically.
    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers(),
            'countries' => Country::all(),
            'countFilterWithVal' => $this->countFilterWithVal(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Users" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" :badge="$countFilterWithVal ?: null" badge-classes="badge-primary" />
            <x-button label="Create" icon="o-plus" :link="route('users.create')" spinner class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>
            @scope('cell_avatar', $user)
                <x-avatar image="{{ $user->avatar ?? '/empty-user.jpg' }}" class="!w-10" />
            @endscope
            @scope('actions', $user)
            <div class="flex flex-row">
                <x-button tooltip="Update" icon="o-pencil-square" :link="route('users.edit', $user)" spinner class="btn-ghost btn-sm text-primary" />
                <x-button tooltip="Delete" icon="o-trash" wire:click="delete({{ $user['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="Country" wire:model.live="country_id" :options="$countries" icon="o-flag" placeholder-value="0" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
