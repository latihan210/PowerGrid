<x-inline-form title="Permissions" :showForm="$showForm" :formMode="$formMode" addLabel="+ Add Permission">
    {{-- Field khusus Permission --}}
    <div class="flex-1">
        <flux:input wire:model="name" label="Permission Name" placeholder="Example: create-user, delete-post" />
        @error('name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="w-full md:w-36">
        <flux:select wire:model="guard_name" label="Guard">
            <flux:select.option value="web">web</flux:select.option>
            <flux:select.option value="api">api</flux:select.option>
        </flux:select>
    </div>
</x-inline-form>