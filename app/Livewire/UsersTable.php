<?php

namespace App\Livewire;

use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class UsersTable extends PowerGridComponent
{
    public string $tableName = 'users-table';

    public function setUp(): array
    {
        return [
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $allowedSorts = ['name', 'username', 'email', 'created_at', 'roles_name'];
        $sortField = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'id';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $rolesSubquery = "(SELECT GROUP_CONCAT(name, ', ') FROM roles INNER JOIN model_has_roles ON roles.id = model_has_roles.role_id WHERE model_has_roles.model_id = users.id AND model_has_roles.model_type = 'App\Models\User')";
        $sortSql = ($sortField === 'roles_name') ? $rolesSubquery : "users.{$sortField}";

        return User::query()
            ->select('users.*')
            ->selectRaw("ROW_NUMBER() OVER (ORDER BY {$sortSql} {$sortDirection}) AS no")
            ->selectRaw("{$rolesSubquery} as roles_name")
            ->where('id', '>', 1);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('name')
            ->add('username', fn(User $user) => $user->username ?: '-')
            ->add('roles_name', fn(User $user) => $user->roles_name ?: '-')
            ->add('email')
            ->add('created_at_formatted', fn(User $user) => Carbon::parse($user->created_at)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Username', 'username')
                ->sortable()
                ->searchable(),

            Column::make('Roles', 'roles_name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Joined', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('username')->operators(['contains']),
            Filter::inputText('roles_name')->operators(['contains']),
            Filter::inputText('email')->operators(['contains']),
            Filter::datePicker('created_at_formatted', 'created_at'),
        ];
    }

    public function actions(User $row): array
    {
        return [
            Button::add('view-user')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->route('users.show', ['user' => $row->id])
                ->tooltip('Show'),
            Button::add('edit-user')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>')
                ->route('users.edit', ['user' => $row->id])
                ->can(auth()->user()?->hasRole('admin') ?? false)
                ->tooltip('Edit'),
            Button::add('delete-user')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>')
                ->confirm('Apakah Anda yakin ingin menghapus user ini?')
                ->dispatch('delete-user', ['userId' => $row->id])
                ->can(auth()->user()?->hasRole('admin') ?? false)
                ->tooltip('Delete'),
        ];
    }

    #[On('delete-user')]
    public function deleteUser(int $userId): void
    {
        if (!auth()->user()?->hasRole('admin')) {
            return;
        }

        User::destroy($userId);

        Flux::toast(variant: 'success', text: 'User deleted successfully.');
    }
}
