<x-layouts::app :title="__('Users Detail')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <flux:button variant="ghost" icon="arrow-left" href="{{ url()->previous() }}" wire:navigate />
                <div>
                    <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @role('admin')
                <flux:button variant="ghost" icon="pencil" href="{{ route('users.edit', $user) }}" wire:navigate>
                    Edit
                </flux:button>
                <form action="{{ route('users.destroy', $user) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                    @csrf
                    @method('DELETE')
                    <flux:button variant="danger" icon="trash" type="submit">
                        Hapus
                    </flux:button>
                </form>
                @endrole
            </div>
        </div>

        {{-- Profile Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">

            {{-- Cover --}}
            <div class="h-24 bg-gradient-to-r from-zinc-100 to-zinc-200 dark:from-zinc-800 dark:to-zinc-700"></div>

            {{-- Avatar & Name --}}
            <div class="px-6 pb-6">
                <div class="flex items-end gap-4 -mt-10 mb-4">
                    <div
                        class="size-20 rounded-2xl bg-zinc-200 dark:bg-zinc-700 border-4 border-white dark:border-zinc-900 flex items-center justify-center text-2xl font-bold text-zinc-600 dark:text-zinc-300 shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="pb-1">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Info --}}
        <div
            class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 divide-y divide-zinc-100 dark:divide-zinc-800">

            <div class="px-6 py-4">
                <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Informasi Akun
                </h3>
            </div>

            @php
            $details = [
            ['label' => 'Nama Lengkap', 'value' => $user->name, 'icon' => 'user'],
            ['label' => 'Username', 'value' => $user->username, 'icon' => 'at-symbol'],
            ['label' => 'Email', 'value' => $user->email, 'icon' => 'envelope'],
            ['label' => 'Email Terverifikasi','value' => $user->email_verified_at
            ? $user->email_verified_at->format('d M Y H:i')
            : 'Belum diverifikasi', 'icon' => 'shield-check'],
            ['label' => 'Bergabung', 'value' => $user->created_at->format('d M Y H:i'), 'icon' => 'calendar'],
            ['label' => 'Terakhir Diperbarui','value' => $user->updated_at->diffForHumans(), 'icon' => 'clock'],
            ];
            @endphp

            @foreach ($details as $detail)
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="size-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                    <flux:icon :icon="$detail['icon']" class="size-4 text-zinc-500 dark:text-zinc-400" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $detail['label'] }}</p>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $detail['value'] }}</p>
                </div>
            </div>
            @endforeach

        </div>

    </div>
</x-layouts::app>