<?php

namespace App\Livewire;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Flux\Flux;
use Livewire\Attributes\On;

final class RolesTable extends PowerGridComponent
{
    public string $tableName = 'roles-table';
    public bool $showForm = false;
    public string $formMode = 'create';
    public ?int $roleId = null;
    public string $name;
    public string $guard_name = 'web';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->includeViewOnTop('role.form'),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Role::query()->withCount('permissions');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('guard_name')
            ->add('permissions_count')
            ->add('created_at_formatted', fn(Role $model) => $model->created_at?->format('d M Y H:i') ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id'),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Guard Name', 'guard_name')
                ->sortable()
                ->searchable(),
            Column::make('Permissions', 'permissions_count')
                ->sortable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('guard_name')->operators(['contains']),
            Filter::datetimepicker('created_at_formatted', 'created_at'),
        ];
    }

    public function toggleForm(): void
    {
        $this->resetForm();
        $this->showForm = !$this->showForm;
    }

    private function resetForm(): void
    {
        $this->formMode = 'create';
        $this->roleId = null;
        $this->name = '';
        $this->guard_name = 'web';
        $this->resetValidation();
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function editRole(int $roleId): void
    {
        $role = Role::findOrFail($roleId);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->formMode = 'edit';
        $this->resetValidation();
        $this->showForm = true;

        $this->js("window.scrollTo({ top: 0, behavior: 'smooth' });");
    }

    public function save(): void
    {
        $uniqueRules = $this->formMode === 'edit'
            ? 'unique:roles,name,' . $this->roleId
            : 'unique:roles,name';

        $this->validate([
            'name' => ['required', 'string', 'max:255', $uniqueRules],
            'guard_name' => ['required', 'string', 'max:255'],
        ]);

        if ($this->formMode === 'edit') {
            $role = Role::findOrFail($this->roleId);

            if ($role->name === 'admin' && $this->name !== 'admin') {
                Flux::toast(variant: 'danger', text: 'You cannot change the name of the admin role.');
                return;
            }

            $role->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            Flux::toast(variant: 'success', text: 'Role updated successfully.');
        } else {
            Role::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            Flux::toast(variant: 'success', text: 'Role created successfully.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    #[On('open-edit-role')]
    public function openEditRole(int $roleId): void
    {
        $this->editRole($roleId);
    }

    #[On('delete-role')]
    public function deleteRole(int $roleId): void
    {
        $role = Role::find($roleId);

        if (!$role) return;

        if ($role->name === 'admin') {
            Flux::toast(variant: 'danger', text: 'You cannot delete the admin role.');
            return;
        }

        $role->delete();
        Flux::toast(variant: 'success', text: 'Role deleted successfully.');
    }

    public function actions(Role $row): array
    {
        return [
            Button::add('view-role')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->route('roles.show', ['role' => $row->id])
                ->tooltip('Manage Permissions'),
            Button::add('edit-role')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>')
                ->dispatch('open-edit-role', ['roleId' => $row->id])
                ->tooltip('Edit'),
            Button::add('delete-role')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>')
                ->confirm('Are you sure you want to delete this role?')
                ->dispatch('delete-role', ['roleId' => $row->id])
                ->tooltip('Delete'),
        ];
    }
}
