<?php

namespace App\DataTables;

use App\Models\Preference;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Illuminate\Support\Str;

class PreferenceDataTable extends DataTable
{
    protected $viewPrefix = 'admin.preferences';
    protected $routePrefix = 'admin.preferences';
    private $viewPermission;
    private $addPermission;
    private $editPermission;
    private $deletePermission;

    public function __construct()
    {
        $this->viewPermission = hasPermission('view_preference');
        $this->addPermission = hasPermission('add_preference');
        $this->editPermission = hasPermission('edit_preference');
        $this->deletePermission = hasPermission('delete_preference');
    }
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('created_at', fn($user) => $user->created_at->format('d-m-Y H:i:s'))

            ->editColumn('type', function ($row) {
                return $row->type
                    ? ucwords(str_replace('_', ' ', $row->type))
                    : '-';
            })

            ->editColumn('image', function ($row) {

                if (!$row->image) {
                    return '<div class="text-center">-</div>';
                }

                return '
                    <div class="text-center">
                        <a href="' . getFileUrl($row->image) . '" target="_blank">
                            <img src="' . getFileUrl($row->image) . '"
                                width="50"
                                height="50"
                                class="rounded"
                                style="cursor:pointer;">
                        </a>
                    </div>
                ';
            })
            ->editColumn('status', function ($row) {

                $statusChangePermission = $this->editPermission ? '' : 'disabled';

                $isActive = $row->status === 'Active';

                return '
                    <label class="custom-switch ps-0 d-flex align-items-center gap-2">
                        <input type="checkbox"
                            class="custom-switch-input toggle-status"
                            data-uuid="' . $row->uuid . '"
                            ' . ($isActive ? 'checked' : '') . '
                            ' . $statusChangePermission . '>

                        <span class="custom-switch-indicator"></span>

                        <span class="btn btn-sm ' . ($isActive ? 'btn-success' : 'btn-danger') . ' btn-pill status-text">
                             ' . ($isActive ? __('users.active') : __('users.inactive')) . '
                        </span>
                    </label>';
            })

            
            ->editColumn('description', function ($row) {
                return $row->description
                    ? Str::limit(strip_tags($row->description),70, '.....')
                    : '-';
            })

            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-list d-flex gap-1">';

                // Edit button
                if ($this->editPermission) {
                    $editUrl = route($this->routePrefix . '.edit', $row->uuid);
                    $actions .= '
                        <a href="' . $editUrl . '" 
                        class="btn btn-sm btn-primary"
                         title="' . __('users.edit') . '">
                            <span class="fe fe-edit"></span>
                        </a>';
                }
                if($this->viewPermission) {
                    $actions .= '
                        <button type="button"
                            class="btn btn-sm btn-info view-btn"
                            title="' . __('users.view') . '"
                            data-url="' . route($this->routePrefix . '.show', $row->uuid) . '">
                            <span class="fe fe-eye"></span>
                        </button>';
                }
                
                // Delete button
                if ($this->deletePermission) {
                    $actions .= '
                        <button type="button"
                                class="btn btn-sm btn-danger delete-btn"
                                data-uuid="' . $row->uuid . '"
                                title="' . __('users.delete') . '">
                            <span class="fe fe-trash-2"></span>
                        </button>';
                }

                $actions .= '</div>';

                return $actions;
            })

            ->rawColumns(['image', 'status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Preference $model): QueryBuilder
    {
        $query =  $model->newQuery();

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }
        
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html()
    {
        $buttons = [
            Button::make('collection')
                ->text('<i class="ri-external-link-line me-sm-1"></i> <span class="d-none d-sm-inline-block">' . __('users.export') . '</span>')
                ->className('btn btn-outline-primary dropdown-toggle me-0 waves-effect waves-light')
                ->buttons([
                    Button::make('print')->text('<i class="ri-printer-line me-1"></i>' . __('users.print'))->className('dropdown-item')->exportOptions(['columns' => [1,2,3,5,6,7]]),

                    Button::make('csv')
                        ->text('<i class="ri-file-text-line me-1"></i>' . __('users.csv'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,5,6,7]]),

                    Button::make('excel')
                        ->text('<i class="ri-file-excel-line me-1"></i>' . __('users.excel'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,5,6,7]]),

                    Button::make('pdf')
                        ->text('<i class="ri-file-pdf-line me-1"></i>' . __('users.pdf'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,5,6,7]]),

                    Button::make('copy')
                        ->text('<i class="ri-file-copy-line me-1"></i>' . __('users.copy'))
                        ->className('dropdown-item')
                        ->exportOptions(['columns' => [1,2,3,5,6,7]]),
                ]),
        ];


        return $this->builder()
            ->setTableId('preferences-table')
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
            
            ->orderBy(0, 'desc')
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
                    $("#preferences-table tfoot").remove();
                }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('created_at')->title('')->visible(false),
            Column::computed('DT_RowIndex')
                ->title('Sr No.')
                ->searchable(false)
                ->orderable(false),

            Column::make('title')->title(__('preferences.title')),
            Column::make('description')->title(__('preferences.description'))->width(200),

            Column::make('image')
                ->title(__('preferences.image'))
                ->searchable(false)
                ->orderable(false),

            Column::make('type')
                ->title(__('preferences.type')),

            Column::make('status')
                ->title(__('preferences.status')),
            Column::make('created_at')->title(__('preferences.created_at')),

            Column::computed('action')
                ->title(__('preferences.action'))
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Preferences_' . date('YmdHis');
    }
}
