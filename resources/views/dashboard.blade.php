<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="grid gap-4 md:grid-cols-3">
            <div
                class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">PowerGrid</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-950 dark:text-white">Active</p>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">Asset CSS dan JS sudah dimuat dari
                    halaman dashboard.</p>
            </div>
            <div
                class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Datasource</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-950 dark:text-white">Users</p>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">Tabel di bawah membaca data langsung dari
                    model `User`.</p>
            </div>
            <div
                class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Status</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-950 dark:text-white">Ready</p>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">Search, sorting, filter text, dan
                    pagination sudah siap diuji.</p>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
            <livewire:users-table />
        </div>
        @role('admin')
        <div
            class="overflow-hidden rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
            <livewire:rolesTable />
        </div>
        <div
            class="overflow-hidden rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
            <livewire:permissionsTable />
        </div>
        @endrole
    </div>
</x-layouts::app>