<?php

namespace App\DataTables;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
{
    protected $viewPrefix  = 'admin.role';
    protected $routePrefix = 'admin.roles';

    private $viewPermission;
    private $addPermission;
    private $editPermission;
    private $deletePermission;

    public function __construct()
    {
        $this->viewPermission   = hasPermission('view_role');
        $this->addPermission    = hasPermission('add_role');
        $this->editPermission   = hasPermission('edit_role');
        $this->deletePermission = hasPermission('delete_role');
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()

            /* ===============================
               STATUS COLUMN
            ================================ */
            ->editColumn('status', function ($role) {

                $statusChangePermission = $this->editPermission ? '' : 'disabled';
                $isActive = $role->status === 'Active';

                return '
                    <label class="custom-switch ps-0 d-flex align-items-center gap-2">
                        <input type="checkbox"
                               class="custom-switch-input toggle-status"
                               data-uuid="' . $role->uuid . '"
                               ' . ($isActive ? 'checked' : '') . '
                               ' . $statusChangePermission . '>

                        <span class="custom-switch-indicator"></span>

                        <span class="btn btn-sm ' . ($isActive ? 'btn-success' : 'btn-danger') . ' btn-pill status-text">
                            ' . ($isActive ? __('roles.active'): __('roles.inactive')) . '
                        </span>
                    </label>';
            })

            /* ===============================
               CREATED AT
            ================================ */
            ->editColumn('created_at', fn ($role) =>
                $role->created_at?->format('d-m-Y H:i:s')
            )

            /* ===============================
               ACTION COLUMN
            ================================ */
            ->addColumn('action', function ($role) {

                $actions = '<div class="btn-list d-flex gap-1">';

                // Edit
                if ($this->editPermission) {
                    $editUrl = route($this->routePrefix . '.edit', $role->uuid);
                    $actions .= '
                        <a href="' . $editUrl . '"
                           class="btn btn-sm btn-primary"
                           title="' . __('roles.edit') . '">
                            <span class="fe fe-edit"></span>
                        </a>';
                }

                // Delete (restrict system roles)
                // $restrictedRoles = ['admin'];
                // if ($this->deletePermission && !in_array($role->name, $restrictedRoles)) 
                $restrictedRoles = ['admin','super_admin'];
                // echo "<pre>";print_r($assignedUsers);"</pre>";die;
                if ( $this->deletePermission && !in_array($role->name, $restrictedRoles) && $role->assigned_users_count === 0) 
                {
                    $actions .= '
                        <button type="button"
                                class="btn btn-sm btn-danger delete-btn"
                                data-uuid="' . $role->uuid . '"
                                title="' . __('roles.delete') . '">
                            <span class="fe fe-trash-2"></span>
                        </button>';
                }

                $actions .= '</div>';

                return $actions;
            })

            /* ===============================
               NAME FORMATTING
            ================================ */
            ->editColumn('name', function ($role) {
                return ucwords(str_replace('_', ' ', $role->name));
            })

            ->rawColumns(['status', 'action']);
    }

    /**
     * Get the query source.
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->withCount('assignedUsers');
    }

    /**
     * Optional HTML builder.
     */
    public function html()
    {
        $buttons = [
            Button::make('collection')
                ->text('<i class="ri-external-link-line me-sm-1"></i> <span class="d-none d-sm-inline-block">' . __('roles.export') . '</span>')
                ->className('btn btn-outline-primary dropdown-toggle me-0 waves-effect waves-light')
                ->buttons([
                    Button::make('print')
                        ->text('<i class="ri-printer-line me-1"></i> ' . __('roles.print'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4]]),

                    Button::make('csv')
                        ->text('<i class="ri-file-text-line me-1"></i> ' . __('roles.csv'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4]]),

                    Button::make('excel')
                        ->text('<i class="ri-file-excel-line me-1"></i> ' . __('roles.excel'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4]]),

                    Button::make('pdf')
                        ->text('<i class="ri-file-pdf-line me-1"></i> ' . __('roles.pdf'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4]]),

                    Button::make('copy')
                        ->text('<i class="ri-file-copy-line me-1"></i>  ' . __('roles.copy'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4]]),
                ]),
        ];

        return $this->builder()
            ->setTableId('roles-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('
                <"dt-temp-buttons d-none"B>
                <"row mt-3"
                    <"col-sm-12 col-md-6"l>
                    <"col-sm-12 col-md-6 d-flex justify-content-end"f>
                >
                t
                <"row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                >
            ')
            ->orderBy(4, 'desc')
            ->buttons($buttons)
            ->parameters([
                'pageLength'     => (int) setting('pagination_length'),
                'lengthMenu' => datatable_length_menu(),
                'scrollX'        => true,
                'scrollY'        => '60vh',
                'scrollCollapse' => true,
                'autoWidth'      => false,
                'initComplete'   => 'function() {
                    $(".dt-buttons").prependTo(".export-btn-container");
                }',
                'drawCallback'   => 'function() {
                    $("#roles-table tfoot").remove();
                }',
            ]);
    }

    /**
     * Get columns.
     */
    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(30),
            Column::make('name')->title(__('roles.name'))->width(200),
            Column::computed('status')->title(__('roles.status'))->orderable(false)->searchable(false)->width(50),
            Column::make('created_at')->title(__('roles.created_at'))->width(120),
            Column::computed('action')->title(__('roles.action'))->orderable(false)->searchable(false)->width(40),
        ];
    }

    protected function filename(): string
    {
        return 'Roles_' . date('YmdHis');
    }
}
