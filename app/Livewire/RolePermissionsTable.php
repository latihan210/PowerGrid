<?php

namespace App\Livewire;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RolePermissionsTable extends PowerGridComponent
{
    public string $tableName = 'rolePermissionsTable';
    public int $roleId;
    protected ?Role $role = null;

    protected function getRole(): Role
    {
        if (!$this->role) {
            $this->role = Role::findOrFail($this->roleId);
        }

        return $this->role;
    }

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
        return Permission::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        $assignedIds = $this->getRole()->permissions()->pluck('id')->toArray();

        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('guard_name')
            ->add('assigned', function (Permission $permission) use ($assignedIds) {
                $checked = in_array($permission->id, $assignedIds) ? 'checked' : '';
                return '<input 
                    type="checkbox" 
                    ' . $checked . ' 
                    wire:click="togglePermission(' . $permission->id . ')"
                    class="rounded border-zinc-300 text-blue-600 cursor-pointer"
                >';
            })
            ->add('created_at_formatted', fn(Permission $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id'),
            Column::make('Assigned', 'assigned')
                ->bodyAttribute('class', 'text-center'),
            Column::make('Permission Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Guard', 'guard_name')
                ->sortable()
                ->searchable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('guard_name')->operators(['contains']),
            Filter::datetimepicker('created_at'),
        ];
    }

    public function togglePermission(int $permissionId): void
    {
        $role = $this->getRole();
        $permission = Permission::findOrFail($permissionId);

        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            Flux::toast(variant: 'warning', text: "Permission '{$permission->name}' dicabut dari role '{$role->name}'.");
        } else {
            $role->givePermissionTo($permission);
            Flux::toast(variant: 'success', text: "Permission '{$permission->name}' diberikan ke role '{$role->name}'.");
        }

        // Reset cache role agar fresh
        $this->role = null;
    }

    // public function actions(Permission $row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: '.$row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
    //     ];
    // }
}
