<?php

namespace App\DataTables;

use App\Models\OnboardingMaster;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class OnboardingMasterDataTable extends DataTable
{
    protected $viewPrefix = 'admin.onboarding-masters';
    protected $routePrefix = 'admin.onboarding-masters';
    private $viewPermission;
    private $addPermission;
    private $editPermission;
    private $deletePermission;

    public function __construct()
    {
        $this->viewPermission   = hasPermission('view_onboarding_master');
        $this->addPermission    = hasPermission('add_onboarding_master');
        $this->editPermission   = hasPermission('edit_onboarding_master');
        $this->deletePermission = hasPermission('delete_onboarding_master');
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '-')

            ->editColumn('category', function ($row) {
                $categoryName = OnboardingMaster::CATEGORIES[$row->category] ?? ucwords(str_replace('_', ' ', $row->category));
                return '<span class="badge bg-primary-transparent text-primary rounded-pill px-3 py-1 font-weight-semibold">'
                    . e($categoryName) .
                '</span>';
            })

            ->editColumn('title', function ($row) {
                return '<span class="font-weight-semibold text-dark">' . e($row->title) . '</span>';
            })

            ->editColumn('image', function ($row) {
                if (!$row->image) {
                    return '<div class="text-center text-muted">-</div>';
                }

                return '
                    <div class="text-center">
                        <a href="' . getFileUrl($row->image) . '" target="_blank">
                            <img src="' . getFileUrl($row->image) . '"
                                width="45"
                                height="45"
                                class="rounded border"
                                style="object-fit:cover;cursor:pointer;">
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
                             ' . ($isActive ? 'Active' : 'Inactive') . '
                        </span>
                    </label>';
            })

            ->editColumn('description', function ($row) {
                return $row->description
                    ? Str::limit(strip_tags($row->description), 60, '...')
                    : '<span class="text-muted">-</span>';
            })

            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-list d-flex gap-1">';

                // Edit button
                if ($this->editPermission) {
                    $editUrl = route($this->routePrefix . '.edit', $row->uuid);
                    $actions .= '
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit Master">
                            <span class="fe fe-edit"></span>
                        </a>';
                }

                // View button
                if ($this->viewPermission) {
                    $showUrl = route($this->routePrefix . '.show', $row->uuid);
                    $actions .= '
                        <button type="button" class="btn btn-sm btn-info view-btn" data-url="' . $showUrl . '" title="View Details">
                            <span class="fe fe-eye"></span>
                        </button>';
                }

                // Delete button
                if ($this->deletePermission) {
                    $actions .= '
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-uuid="' . $row->uuid . '" title="Delete Master">
                            <span class="fe fe-trash-2"></span>
                        </button>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['category', 'title', 'image', 'status', 'description', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(OnboardingMaster $model): QueryBuilder
    {
        $query = $model->newQuery();

        // Filter by Status
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // Filter by Category
        if (request()->filled('category')) {
            $query->where('category', request('category'));
        }

        return $query->orderBy('category')->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('onboarding-masters-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'f>>" .
                "<'row'<'col-sm-12'tr>>" .
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>")
            ->orderBy(1, 'asc')
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('#')
                ->width(40)
                ->addClass('text-center'),
            Column::make('category')->title('Category')->width(180),
            Column::make('title')->title('Label / Title'),
            Column::make('image')->title('Image / Icon')->addClass('text-center')->width(80),
            Column::make('description')->title('Description / Subtitle'),
            Column::make('sort_order')->title('Sort')->addClass('text-center')->width(60),
            Column::make('status')->title('Status')->addClass('text-center')->width(110),
            Column::make('created_at')->title('Created At')->width(150),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }
}
