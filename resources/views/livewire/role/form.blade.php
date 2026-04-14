<x-inline-form title="Roles" :showForm="$showForm" :formMode="$formMode" addLabel="+ Add Role">
    {{-- Field khusus Role --}}
    <div class="flex-1">
        <flux:input wire:model="name" label="Role Name" placeholder="Example: editor, manager" />
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