<?php

namespace App\DataTables;

use App\Http\Controllers\Admin\PathwayController;
use App\Models\Pathway;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class PathwayDataTable extends DataTable
{
    protected $routePrefix = 'admin.pathways';
    private $viewPermission;
    private $editPermission;
    private $deletePermission;

    public function __construct()
    {
        $this->viewPermission   = hasPermission('view_pathway');
        $this->editPermission   = hasPermission('edit_pathway');
        $this->deletePermission = hasPermission('delete_pathway');
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('group', fn ($row) => PathwayController::GROUPS[$row->group] ?? $row->group)
            ->editColumn('protocol_key', fn ($row) => $row->protocol_key
                ? '<span class="badge bg-primary-transparent text-primary">' . e(config('protocols.' . $row->protocol_key . '.title', $row->protocol_key)) . '</span>'
                : '<span class="text-muted">-</span>')
            ->editColumn('description', fn ($row) => $row->description ? e(Str::limit($row->description, 70)) : '<span class="text-muted">-</span>')
            ->addColumn('clients', fn ($row) => \App\Models\User::where('pathway_uuid', $row->uuid)->count())
            ->editColumn('status', function ($row) {
                $disabled = $this->editPermission ? '' : 'disabled';
                $isActive = $row->status === 'Active';

                return '
                    <label class="custom-switch ps-0 d-flex align-items-center gap-2">
                        <input type="checkbox" class="custom-switch-input toggle-status" data-uuid="' . $row->uuid . '" ' . ($isActive ? 'checked' : '') . ' ' . $disabled . '>
                        <span class="custom-switch-indicator"></span>
                        <span class="btn btn-sm ' . ($isActive ? 'btn-success' : 'btn-danger') . ' btn-pill status-text">' . ($isActive ? 'Active' : 'Inactive') . '</span>
                    </label>';
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-list d-flex gap-1">';
                if ($this->editPermission) {
                    $actions .= '<a href="' . route($this->routePrefix . '.edit', $row->uuid) . '" class="btn btn-sm btn-primary" title="Edit"><span class="fe fe-edit"></span></a>';
                }
                if ($this->viewPermission) {
                    $actions .= '<button type="button" class="btn btn-sm btn-info view-btn" data-url="' . route($this->routePrefix . '.show', $row->uuid) . '" title="View"><span class="fe fe-eye"></span></button>';
                }
                if ($this->deletePermission) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-uuid="' . $row->uuid . '" title="Delete"><span class="fe fe-trash-2"></span></button>';
                }

                return $actions . '</div>';
            })
            ->rawColumns(['protocol_key', 'description', 'status', 'action']);
    }

    public function query(Pathway $model): QueryBuilder
    {
        $query = $model->newQuery();
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('group')) {
            $query->where('group', request('group'));
        }

        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('pathways-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'f>>" .
                "<'row'<'col-sm-12'tr>>" .
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>")
            ->orderBy(1, 'asc');
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->width(40)->addClass('text-center'),
            Column::make('title')->title('Title'),
            Column::make('group')->title('Group'),
            Column::make('description')->title('Description'),
            Column::make('protocol_key')->title('Protocol Form'),
            Column::computed('clients')->title('Clients')->addClass('text-center'),
            Column::make('sort_order')->title('Sort')->addClass('text-center')->width(60),
            Column::make('status')->title('Status')->addClass('text-center')->width(110),
            Column::computed('action')->title('Actions')->exportable(false)->printable(false)->width(120)->addClass('text-center'),
        ];
    }
}
