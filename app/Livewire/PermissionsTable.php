<?php

namespace App\Livewire;

use Spatie\Permission\Models\Permission;
// use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Flux\Flux;
use Livewire\Attributes\On;

final class PermissionsTable extends PowerGridComponent
{
    public string $tableName = 'permissions-table';
    public bool $showForm = false;
    public string $formMode = 'create';
    public ?int $permissionId = null;
    public string $name;
    public string $guard_name = 'web';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->includeViewOnTop('permissions.form'),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Permission::query();
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
            ->add('created_at_formatted', fn(Permission $model) => $model->created_at?->format('d M Y H:i') ?? '-');
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

    public function resetForm(): void
    {
        $this->formMode = 'create';
        $this->permissionId = null;
        $this->name = '';
        $this->guard_name = 'web';
        $this->resetValidation();
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function editPermission(int $permissionId): void
    {
        $permission = Permission::findOrFail($permissionId);

        $this->permissionId = $permission->id;
        $this->name = $permission->name;
        $this->guard_name = $permission->guard_name;
        $this->formMode = 'edit';
        $this->resetValidation();
        $this->showForm = true;

        $this->js("window.scrollTo({ top: 0, behavior: 'smooth' });");
    }

    public function save(): void
    {
        $uniqueRules = $this->formMode === 'edit'
            ? ['name' => 'required|unique:permissions,name,' . $this->permissionId]
            : ['name' => 'required|unique:permissions,name'];

        $this->validate([
            'name' => ['required', $uniqueRules],
            'guard_name' => ['required'],
        ]);

        if ($this->formMode === 'edit') {
            $permission = Permission::findOrFail($this->permissionId);

            $permission->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            Flux::toast(variant: 'success', text: 'Permission updated successfully.');
        } else {
            Permission::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            Flux::toast(variant: 'success', text: 'Permission created successfully.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    #[On('open-edit-permission')]
    public function openEditPermission(int $permissionId): void
    {
        $this->editPermission($permissionId);
    }

    #[On('delete-permission')]
    public function deletePermission(int $permissionId): void
    {
        $permission = Permission::findOrFail($permissionId);

        if (!$permission) return;

        $permission->delete();
        Flux::toast(variant: 'success', text: 'Permission deleted successfully.');
    }

    public function actions(Permission $row): array
    {
        return [
            Button::add('view-permission')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->route('permissions.show', ['permission' => $row->id])
                ->tooltip('Show'),
            Button::add('edit-permission')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>')
                ->dispatch('open-edit-permission', ['permissionId' => $row->id])
                ->tooltip('Edit'),
            Button::add('delete-permission')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>')
                ->confirm('Are you sure you want to delete this permission?')
                ->dispatch('delete-permission', ['permissionId' => $row->id])
                ->tooltip('Delete'),
        ];
    }
}
