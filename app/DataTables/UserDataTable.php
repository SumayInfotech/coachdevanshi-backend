<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;


class UserDataTable extends DataTable
{
    protected $viewPrefix = 'admin.user';
    protected $routePrefix = 'admin.user';
    private $viewPermission;
    private $addPermission;
    private $editPermission;
    private $deletePermission;

    public function __construct()
    {
        $this->viewPermission = hasPermission('view_user');
        $this->addPermission = hasPermission('add_user');
        $this->editPermission = hasPermission('edit_user');
        $this->deletePermission = hasPermission('delete_user');
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('status', function ($user) {

                $statusChangePermission = $this->editPermission ? '' : 'disabled';

                $isActive = $user->status === 'Active';

                return '
                    <label class="custom-switch ps-0 d-flex align-items-center gap-2">
                        <input type="checkbox"
                            class="custom-switch-input toggle-status"
                            data-uuid="' . $user->uuid . '"
                            ' . ($isActive ? 'checked' : '') . '
                            ' . $statusChangePermission . '>

                        <span class="custom-switch-indicator"></span>

                        <span class="btn btn-sm ' . ($isActive ? 'btn-success' : 'btn-danger') . ' btn-pill status-text">
                             ' . ($isActive ? __('users.active') : __('users.inactive')) . '
                        </span>
                    </label>';
            })
            
            ->editColumn('created_at', fn($user) => $user->created_at->format('d-m-Y H:i:s'))
            ->addColumn('action', function ($user) {
                $actions = '<div class="btn-list d-flex gap-1">';

                // Edit button
                if ($this->editPermission) {
                    $editUrl = route($this->routePrefix . '.edit', $user->uuid);
                    $actions .= '
                        <a href="' . $editUrl . '" 
                        class="btn btn-sm btn-primary"
                         title="' . __('users.edit') . '">
                            <span class="fe fe-edit"></span>
                        </a>';
                }
                if($this->viewPermission) {
                    $viewUrl = route($this->routePrefix . '.view', $user->uuid);
                    $actions .= '
                        <a href="' . $viewUrl . '" 
                        class="btn btn-sm btn-info view-btn"
                        title="' . __('users.view') . '"
                        data-uuid="' . $user->uuid . '">
                            <span class="fe fe-eye"></span>
                        </a>';

                    $activityUrl = route($this->routePrefix . '.activity', $user->uuid);
                    $actions .= '
                        <a href="'.$activityUrl.'" 
                        class="btn btn-sm btn-warning"
                        title="Activity Log">
                            <span class="fe fe-activity"></span>
                        </a>';
                }
                
                // Delete button
                if ($this->deletePermission) {
                    $actions .= '
                        <button type="button"
                                class="btn btn-sm btn-danger delete-btn"
                                data-uuid="' . $user->uuid . '"
                                title="' . __('users.delete') . '">
                            <span class="fe fe-trash-2"></span>
                        </button>';
                }

                $actions .= '</div>';

                return $actions;
            })
            ->editColumn('role', function ($user) {
                return ucwords(str_replace('_', ' ', $user->role?->name ?? __('users.na')));
            })
            ->filterColumn('role', function ($query, $keyword) {
                $query->whereHas('role', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(name) like ?', ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->orderColumn('role', function ($query, $order) {
                $query->withAggregate('role', 'name')
                    ->orderBy('role_name', $order);
            })
            ->rawColumns(['status', 'action']);
    }

    public function query(User $model)
    {
        $query =  $model->newQuery()->excludeAdmin()->with('role');
        // $query =  $model->newQuery()->with('role');
        if (request()->filled('state_id')) {
            $query->where('state_id', request('state_id'));
        }

        if (request()->filled('city_id')) {
            $query->where('city_id', request('city_id'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        $userType = request()->filled('user_type')? request('user_type'): 'admin';
        if ($userType == 'customer') {
            $query->where('user_type', 'customer');
        }else{
            $query->where('user_type', 'admin');
        }

        return $query;
    }

    public function html()
    {
        $buttons = [
            Button::make('collection')
                ->text('<i class="ri-external-link-line me-sm-1"></i> <span class="d-none d-sm-inline-block">' . __('users.export') . '</span>')
                ->className('btn btn-outline-primary dropdown-toggle me-0 waves-effect waves-light')
                ->buttons([
                    Button::make('print')->text('<i class="ri-printer-line me-1"></i>' . __('users.print'))->className('dropdown-item')->exportOptions(['columns' => [1,2,3,4,5]]),

                    Button::make('csv')
                        ->text('<i class="ri-file-text-line me-1"></i>' . __('users.csv'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4,5]]),

                    Button::make('excel')
                        ->text('<i class="ri-file-excel-line me-1"></i>' . __('users.excel'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4,5]]),

                    Button::make('pdf')
                        ->text('<i class="ri-file-pdf-line me-1"></i>' . __('users.pdf'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4,5]]),

                    Button::make('copy')
                        ->text('<i class="ri-file-copy-line me-1"></i>' . __('users.copy'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,4,5]]),
                ]),
        ];


        return $this->builder()
            ->setTableId('users-table')
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
            
            ->orderBy(6, 'desc')
            ->buttons($buttons)
            // ->parameters([
            //     'drawCallback' => 'function(settings) {
            //         $("#users-table tfoot").remove();
            //     }',
            // ]);
            ->parameters([
                'pageLength'     => (int) setting('pagination_length'),
                'lengthMenu' => datatable_length_menu(),
                'scrollX'        => true,
                'scrollY'        => '60vh',
                'scrollCollapse' => true,
                'autoWidth'      => false,
                'initComplete' => 'function(settings, json) {
                  $(".dt-buttons").prependTo(".export-btn-container");
                }',
                'drawCallback' => 'function(settings) {
                    $("#users-table tfoot").remove();
                }',
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->orderable(false)->searchable(false),
            Column::make('name')->title(__('users.name')),

            Column::make('email')->title(__('users.email')),

            Column::make('mobile')->title(__('users.mobile_no')),

            Column::make('role')->title(__('users.role')),

            Column::computed('status')->title(__('users.status'))->orderable(false)->searchable(false),

            Column::make('created_at')->title(__('users.created_at')),

            Column::computed('action')->title(__('users.action'))->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
