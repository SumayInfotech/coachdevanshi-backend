<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\OnboardingMasterDataTable;
use App\Http\Controllers\Controller;
use App\Models\OnboardingMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingMasterController extends Controller
{
    protected $viewPrefix  = 'admin.onboarding-masters';
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

        view()->share([
            'viewPrefix'      => $this->viewPrefix,
            'routePrefix'     => $this->routePrefix,
            'addPermission'   => $this->addPermission,
            'moduleFullName'  => 'Onboarding Masters',
            'moduleShortName' => 'Onboarding Master',
            'categories'      => OnboardingMaster::CATEGORIES,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OnboardingMasterDataTable $dataTable)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }

        return $dataTable->render($this->viewPrefix . '.index', [
            'routePrefix' => $this->routePrefix,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        }

        return view($this->viewPrefix . '.create', [
            'routePrefix' => $this->routePrefix,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'category'    => ['required', 'string', 'max:60'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer'],
            'status'      => ['required', 'in:Active,Inactive'],
            'image'       => [
                'nullable',
                'image',
                'mimes:' . (env('IMAGE_TYPES') ?: 'jpeg,png,jpg,webp,svg'),
                'max:' . (env('IMAGE_SIZE') ?: 5120),
            ],
        ]);

        if ($request->hasFile('image')) {
            $image = uploadFile($request->file('image'), 'onboarding_masters');
        }

        $master = OnboardingMaster::create([
            'category'    => $request->category,
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $image ?? null,
            'sort_order'  => $request->sort_order ?? 0,
            'status'      => $request->status,
        ]);

        activity_log(
            log: 'OnboardingMaster',
            event: 'onboarding_master_created',
            subject: $master,
            properties: [
                'title'    => $master->title,
                'category' => $master->category,
            ]
        );

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('success', 'Onboarding master created successfully.');
    }

    /**
     * Display the specified resource in modal.
     */
    public function show(OnboardingMaster $onboarding_master)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'category'    => $onboarding_master->category_label,
                'title'       => $onboarding_master->title,
                'description' => $onboarding_master->description ?: '-',
                'image'       => $onboarding_master->image ? getFileUrl($onboarding_master->image) : null,
                'sort_order'  => $onboarding_master->sort_order,
                'status'      => $onboarding_master->status,
                'created_at'  => $onboarding_master->created_at ? $onboarding_master->created_at->format('d-m-Y H:i:s') : '-',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OnboardingMaster $onboarding_master)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }

        return view($this->viewPrefix . '.edit', [
            'routePrefix' => $this->routePrefix,
            'master'      => $onboarding_master,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnboardingMaster $onboarding_master)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'category'    => ['required', 'string', 'max:60'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer'],
            'status'      => ['required', 'in:Active,Inactive'],
            'image'       => [
                'nullable',
                'image',
                'mimes:' . (env('IMAGE_TYPES') ?: 'jpeg,png,jpg,webp,svg'),
                'max:' . (env('IMAGE_SIZE') ?: 5120),
            ],
        ]);

        if ($request->hasFile('image')) {
            $image = uploadFile($request->file('image'), 'onboarding_masters', $onboarding_master->image);
        }

        $onboarding_master->update([
            'category'    => $request->category,
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $image ?? $onboarding_master->image,
            'sort_order'  => $request->sort_order ?? 0,
            'status'      => $request->status,
        ]);

        activity_log(
            log: 'OnboardingMaster',
            event: 'onboarding_master_updated',
            subject: $onboarding_master,
            properties: [
                'title'    => $onboarding_master->title,
                'category' => $onboarding_master->category,
            ]
        );

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('success', 'Onboarding master updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnboardingMaster $onboarding_master)
    {
        if (!$this->deletePermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.']);
        }

        activity_log(
            log: 'OnboardingMaster',
            event: 'onboarding_master_deleted',
            subject: $onboarding_master,
            properties: [
                'title'    => $onboarding_master->title,
                'category' => $onboarding_master->category,
            ]
        );

        $onboarding_master->delete();

        return response()->json([
            'success' => true,
            'message' => 'Onboarding master deleted successfully.',
        ]);
    }

    /**
     * Toggle active/inactive status via AJAX.
     */
    public function changeStatus($uuid, Request $request)
    {
        if (!$this->editPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Permission Denied.']);
        }

        $master = OnboardingMaster::where('uuid', $uuid)->first();
        if (!$master) {
            return response()->json(['success' => false, 'message' => 'Onboarding master not found.']);
        }

        $oldStatus = $master->status;
        $master->status = $request->status;
        $master->save();

        activity_log(
            log: 'OnboardingMaster',
            event: 'status_changed',
            subject: $master,
            properties: [
                'old' => ['status' => $oldStatus],
                'new' => ['status' => $request->status],
            ]
        );

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
