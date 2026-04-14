<x-layouts::app :title="__('Users Edit')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('users.update', $user) }}" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            <!-- Name -->
            <flux:input name="name" :label="__('Name')" :value="old('name', $user->name)" type="text" required autofocus
                autocomplete="name" :placeholder="__('Full name')" />

            <!-- Username -->
            <flux:input name="username" :label="__('Username')" :value="old('username', $user->username)" type="text"
                required autofocus autocomplete="username" placeholder="Username" />

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email address')" :value="old('email', $user->email)" type="email"
                required autocomplete="email" placeholder="email@example.com" />

            <div class="flex items-center justify-center gap-2">
                <flux:button type="submit" variant="primary">
                    {{ __('Save Changes') }}
                </flux:button>
                <flux:button variant="filled" type="button" :href="url()->previous()" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </form>

    </div>
</x-layouts::app>