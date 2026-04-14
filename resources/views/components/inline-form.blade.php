@props([
'title' => 'Data',
'showForm' => false,
'formMode' => 'create',
'addLabel' => '+ Add',
])

@role('admin')
<div class="mb-4">
    <div class="flex items-center justify-between mb-3">
        <flux:heading>{{ $title }}</flux:heading>
        <flux:button variant="primary" size="sm" wire:click="toggleForm">
            {{ $showForm && $formMode === 'create' ? 'Close' : $addLabel }}
        </flux:button>
    </div>

    @if ($showForm)
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 mb-4">

        <flux:heading size="sm" class="mb-4">
            {{ $formMode === 'create' ? 'Add New ' . $title : 'Edit ' . $title }}
        </flux:heading>

        <div class="flex flex-col gap-4 md:flex-row md:items-end">

            {{-- Fields dikirim dari luar via $slot --}}
            {{ $slot }}

            {{-- Tombol aksi selalu sama --}}
            <div class="flex gap-2 pb-0.5">
                <flux:button variant="primary" size="sm" wire:click="save" wire:loading.attr="disabled"
                    wire:target="save">
                    <span wire:loading.remove wire:target="save">
                        {{ $formMode === 'create' ? 'Save' : 'Update' }}
                    </span>
                    <span wire:loading wire:target="save">Saving...</span>
                </flux:button>

                <flux:button variant="ghost" size="sm" wire:click="cancelForm">
                    Cancel
                </flux:button>
            </div>
        </div>
    </div>
    @endif
</div>
@endrole