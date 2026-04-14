<x-layouts::app :title="__('Roles Permissions')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <flux:button variant="ghost" icon="arrow-left" href="{{ route('roles.index') }}" wire:navigate />
                <div>
                    <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">
                        {{ $role->name }}
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Guard: {{ $role->guard_name }} ·
                        {{ $role->permissions->count() }} permissions assigned
                    </p>
                </div>
            </div>
        </div>
        <livewire:rolePermissionsTable :roleId="$role->id" />
    </div>
</x-layouts::app>