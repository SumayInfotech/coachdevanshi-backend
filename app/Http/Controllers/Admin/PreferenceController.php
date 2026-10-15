<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PreferenceDataTable;
use App\Http\Controllers\Controller;
use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PreferenceController extends Controller
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
        view()->share([
            'viewPrefix' => $this->viewPrefix,
            'routePrefix' => $this->routePrefix,
            'addPermission' =>  $this->addPermission,
            'moduleFullName'  => __('preferences.module_plural'),
            'moduleShortName' => __('preferences.module_name'),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PreferenceDataTable $dataTable)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }
        return $dataTable->render($this->viewPrefix . '.index',['routePrefix' => $this->routePrefix,]);
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
            'title' => 'required|max:255|unique:preferences,title',
            'description' => 'nullable',
            'image' => [
                    'nullable',
                    'image',
                    'mimes:' . env('IMAGE_TYPES'),
                    'max:' . env('IMAGE_SIZE'),
                ],
            'type' => [
                'nullable',
                Rule::in([
                    'dietary',
                    'goals',
                    'food_allergies'
                ])
            ],
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($request->hasFile('image')) {
            $image = uploadFile($request->file('image'), 'preferences');
        }

        $preference = Preference::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image ??null,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        activity_log(
            log: 'Preference',
            event: 'preference_created',
            subject: $preference,
            properties: [
                'title' => $preference->title,
            ]
        );

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('success', __('preferences.created_success'));
    }

    public function show(Preference $preference)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }
        return response()->json([
            'success' => true,
            'data' => [
                'title' => $preference->title,
                'description' => $preference->description,
                'image' => $preference->image
                    ? getFileUrl($preference->image)
                    : null,
                'type' => $preference->type
                    ? ucwords(str_replace('_', ' ', $preference->type))
                    : '-',
                'status' => $preference->status,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Preference $preference)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }
        return view($this->viewPrefix . '.edit', [
            'routePrefix' => $this->routePrefix,
            'preference' => $preference,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Preference $preference)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'title' => [
                'required',
                'max:255',
                Rule::unique('preferences', 'title')
                    ->ignore($preference->uuid, 'uuid'),
            ],
            'description' => 'nullable',
            'type' => [
                'nullable',
                Rule::in([
                    'dietary',
                    'goals',
                    'food_allergies'
                ])
            ],
            'status' => 'required|in:Active,Inactive',
            'image' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES'),
                'max:' . env('IMAGE_SIZE'),
            ],
        ]);

        if ($request->hasFile('image')) {
            $image = uploadFile($request->file('image'), 'preferences',$preference->image);
        }

        $preference->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image ?? $preference->image,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        activity_log(
            log: 'Preference',
            event: 'preference_updated',
            subject: $preference,
            properties: [
                'title' => $preference->title,
            ]
        );

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('success', __('preferences.updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Preference $preference)
    {
        if (!$this->deletePermission) {
            return response()->json(['success' => false,  'message' => __('users.permission_denied')]);
        }
        activity_log(
            log: 'Preference',
            event: 'preference_deleted',
            subject: $preference,
            properties: [
                'title' => $preference->title,
            ]
        );

        $preference->delete();

        return response()->json([
            'success' => true,
            'message' => __('preferences.deleted_success'),
        ]);
    }

    public function changeStatus($uuid, Request $request)
    {
        if (!$this->editPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Permission Denied']);
        }
        $preferenceObj = Preference()->where('uuid', $uuid)->first();
        $oldStatus = $preferenceObj->status;
        if (!$preferenceObj) {
            return response()->json(['success' => false, 'message' => 'Preference not found']);
        }

        // Update role status
        $preferenceObj->status = $request->status;
        $preferenceObj->save();

        //ACTIVITY LOG
        activity_log(
            log: 'Preference',
            event: 'status_changed',
            subject: $preferenceObj,
            properties: [
                'old' => ['status' => $oldStatus],
                'new' => ['status' => $request->status]
            ]
        );
        return response()->json(['success' => true, 'message' => __('preferences.status_updated')]);
    }
}

