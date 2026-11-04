<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ClientDataTable extends DataTable
{
    protected $routePrefix = 'admin.clients';
    private $viewPermission;
    private $statusPermission;

    public function __construct()
    {
        $this->viewPermission = hasPermission('view_client');
        $this->statusPermission = hasPermission('edit_user');
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $totalChapters = count(config('onboarding.chapters'));

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function ($user) {
                $contact = trim(($user->phone_code ? $user->phone_code . ' ' : '') . $user->mobile);

                return '<div class="fw-semibold">' . e(trim($user->name . ' ' . $user->last_name)) . '</div>'
                    . '<div class="text-muted small">' . e($user->email ?? '') . ($contact ? ' &middot; ' . e($contact) : '') . '</div>';
            })
            ->addColumn('pathway', fn ($user) => $user->pathway ? e($user->pathway->title) : '<span class="text-muted">Not selected</span>')
            ->addColumn('onboarding', function ($user) use ($totalChapters) {
                if ($user->is_onboarding_completed) {
                    return '<span class="badge bg-success">Completed</span>';
                }

                return '<span class="badge bg-warning">' . (int) $user->completed_chapters . ' / ' . $totalChapters . '</span>';
            })
            ->addColumn('plan', function ($user) {
                $plan = $user->activePlan;
                if (!$plan) {
                    return '<span class="text-muted">No active plan</span>';
                }
                $class = $plan->days_left <= 14 ? 'text-danger' : 'text-success';

                return e($plan->title) . '<div class="small ' . $class . '">' . $plan->days_left . ' days left &middot; ends ' . $plan->end_date->format('d M Y') . '</div>';
            })
            ->addColumn('alerts', function ($user) {
                $badges = '';
                if ($user->pending_renewals) {
                    $badges .= '<span class="badge bg-danger me-1">Renewal requested</span>';
                }
                if ($user->unread_messages) {
                    $badges .= '<span class="badge bg-info">' . $user->unread_messages . ' unread message(s)</span>';
                }

                return $badges ?: '<span class="text-muted">-</span>';
            })
            ->editColumn('created_at', fn ($user) => $user->created_at->format('d-m-Y'))
            ->editColumn('status', function ($user) {
                $disabled = $this->statusPermission ? '' : 'disabled';
                $isActive = $user->status === 'Active';

                return '
                    <label class="custom-switch ps-0 d-flex align-items-center gap-2">
                        <input type="checkbox" class="custom-switch-input toggle-status" data-uuid="' . $user->uuid . '" ' . ($isActive ? 'checked' : '') . ' ' . $disabled . '>
                        <span class="custom-switch-indicator"></span>
                        <span class="btn btn-sm ' . ($isActive ? 'btn-success' : 'btn-danger') . ' btn-pill status-text">' . ($isActive ? 'Active' : 'Inactive') . '</span>
                    </label>';
            })
            ->addColumn('action', function ($user) {
                if (!$this->viewPermission) {
                    return '';
                }

                return '<a href="' . route($this->routePrefix . '.show', $user->uuid) . '" class="btn btn-sm btn-info" title="Open client"><span class="fe fe-eye"></span></a>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('mobile', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['name', 'pathway', 'onboarding', 'plan', 'alerts', 'status', 'action']);
    }

    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()->customer()
            ->with(['pathway', 'activePlan'])
            ->withCount([
                'onboardingChapters as completed_chapters' => fn ($q) => $q->where('status', 'completed'),
                'renewalRequests as pending_renewals'      => fn ($q) => $q->where('status', 'Pending'),
                'chatMessages as unread_messages'          => fn ($q) => $q->where('sender_type', 'client')->whereNull('read_at'),
            ]);

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('pathway_uuid')) {
            $query->where('pathway_uuid', request('pathway_uuid'));
        }
        if (request()->filled('onboarding')) {
            $query->where('is_onboarding_completed', request('onboarding') === 'completed');
        }
        if (request('attention') === 'renewal') {
            $query->whereHas('renewalRequests', fn ($q) => $q->where('status', 'Pending'));
        } elseif (request('attention') === 'unread') {
            $query->whereHas('chatMessages', fn ($q) => $q->where('sender_type', 'client')->whereNull('read_at'));
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('clients-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'f>>" .
                "<'row'<'col-sm-12'tr>>" .
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>")
            ->orderBy(7, 'desc');
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->width(40)->addClass('text-center'),
            Column::make('name')->title('Client'),
            Column::computed('pathway')->title('Pathway'),
            Column::computed('onboarding')->title('Onboarding')->addClass('text-center'),
            Column::computed('plan')->title('Plan'),
            Column::computed('alerts')->title('Needs Attention'),
            Column::make('status')->title('Status')->addClass('text-center')->width(110),
            Column::make('created_at')->title('Joined')->width(100),
            Column::computed('action')->title('Actions')->exportable(false)->printable(false)->width(70)->addClass('text-center'),
        ];
    }
}
