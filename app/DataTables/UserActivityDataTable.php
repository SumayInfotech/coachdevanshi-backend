<?php

namespace App\DataTables;

use App\Models\ActivityLog;
use App\Models\User;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;

class UserActivityDataTable extends DataTable
{
    protected $userId;

    public function __construct()
    {
        $this->userId = User::where('uuid', request()->route('uuid'))->value('id');
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()

            ->editColumn('event', function ($row) {

                $event = ucwords(str_replace('_', ' ', $row->event));

                // If subject exists (User model)
                if ($row->subject instanceof \App\Models\User) {
                    $subjectText = $row->subject?->name.'('.$row->subject?->email .')';

                    return $event . ' – ' . __('activity.user') . ' ' . $subjectText;
                }

                return $event;
            })

            ->editColumn('causer', function ($row) {
                return $row->causer
                    ? $row->causer->name
                    : __('common.na');
            })

            // ->addColumn('changes', function ($row) {

            //     $props = $row->properties ?? [];

            //     if (!isset($props['old']) && !isset($props['new'])) {
            //         return __('common.na');
            //     }

            //     return '
            //         <button 
            //             class="btn btn-sm btn-primary view-changes"
            //             data-old=\''.json_encode($props['old'] ?? []).'\'
            //             data-new=\''.json_encode($props['new'] ?? []).'\'>
            //             ' . __('activity.view') . '
            //         </button>
            //     ';
            // })
            ->addColumn('changes', function ($row) {

                $props = $row->properties ?? [];

                if (!isset($props['old']) && !isset($props['new'])) {
                    return __('common.na');
                }


                return '
                    <button 
                        class="btn btn-sm btn-primary view-changes"
                        data-id="'.$row->id.'">
                        ' . __('activity.view') . '
                    </button>
                ';
            })

            ->editColumn('created_at', fn ($row) =>
                $row->created_at->format('d-m-Y H:i:s')
            )

            ->rawColumns(['changes']);
    }

    public function query()
    {
        $query = ActivityLog::query()->with(['causer', 'subject'])->where('causer_id', $this->userId);

        // Filters (DITTO users index style)
        if (request()->filled('event')) {
            $query->where('event', request('event'));
        }

        if (request()->filled('log_name')) {
            $query->where('log_name', request('log_name'));
        }

        if (request()->filled('date')) {
            $query->whereDate('created_at', request('date'));
        }

        return $query->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('user-activity-table')
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
            ->parameters([
                'pageLength'     => (int) setting('pagination_length'),
                'lengthMenu'     => datatable_length_menu(),
                'scrollX'        => true,
                'scrollY'        => '60vh',
                'scrollCollapse' => true,
                'autoWidth'      => false,
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->orderable(false)->searchable(false),
            Column::make('log_name')->title(__('activity.module')),
            Column::make('event')->title(__('activity.action')),
            Column::computed('causer')->title(__('activity.performed_by')),
            Column::computed('changes')->title(__('activity.changes'))->orderable(false)->searchable(false),
            Column::make('created_at')->title(__('activity.date')),
        ];
    }
}
