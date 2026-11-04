<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PathwayDataTable;
use App\Http\Controllers\Controller;
use App\Models\Pathway;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PathwayController extends Controller
{
    protected $viewPrefix  = 'admin.pathways';
    protected $routePrefix = 'admin.pathways';

    private $viewPermission;
    private $addPermission;
    private $editPermission;
    private $deletePermission;

    public const GROUPS = [
        'core'     => 'Core Coaching Track',
        'hormonal' => 'Hormonal & Life-Stage Journey',
        'general'  => 'General',
    ];

    public function __construct()
    {
        $this->viewPermission   = hasPermission('view_pathway');
        $this->addPermission    = hasPermission('add_pathway');
        $this->editPermission   = hasPermission('edit_pathway');
        $this->deletePermission = hasPermission('delete_pathway');

        view()->share([
            'viewPrefix'      => $this->viewPrefix,
            'routePrefix'     => $this->routePrefix,
            'addPermission'   => $this->addPermission,
            'moduleFullName'  => 'Pathways',
            'moduleShortName' => 'Pathway',
            'groups'          => self::GROUPS,
            'protocols'       => collect(config('protocols'))->map(fn ($protocol) => $protocol['title'])->all(),
        ]);
    }

    public function index(PathwayDataTable $dataTable)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }

        return $dataTable->render($this->viewPrefix . '.index', ['routePrefix' => $this->routePrefix]);
    }

    public function create()
    {
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        }

        return view($this->viewPrefix . '.create', ['routePrefix' => $this->routePrefix, 'pathway' => new Pathway()]);
    }

    public function store(Request $request)
    {
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        }

        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        if ($request->hasFile('image')) {
            $data['image'] = uploadFile($request->file('image'), 'pathways');
        }

        $pathway = Pathway::create($data);

        activity_log(log: 'Pathway', event: 'pathway_created', subject: $pathway, properties: ['title' => $pathway->title]);

        return redirect()->route($this->routePrefix . '.index')->with('success', 'Pathway created successfully.');
    }

    public function show(Pathway $pathway)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'title'       => $pathway->title,
                'group'       => self::GROUPS[$pathway->group] ?? $pathway->group,
                'description' => $pathway->description,
                'tags'        => implode(', ', $pathway->tags ?? []),
                'protocol'    => $pathway->protocol_key ? (config('protocols.' . $pathway->protocol_key . '.title') ?? $pathway->protocol_key) : 'No protocol form',
                'image'       => $pathway->image ? getFileUrl($pathway->image) : null,
                'sort_order'  => $pathway->sort_order,
                'status'      => $pathway->status,
                'clients'     => \App\Models\User::where('pathway_uuid', $pathway->uuid)->count(),
                'created_at'  => dateFormat($pathway->created_at),
            ],
        ]);
    }

    public function edit(Pathway $pathway)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }

        return view($this->viewPrefix . '.edit', ['routePrefix' => $this->routePrefix, 'pathway' => $pathway]);
    }

    public function update(Request $request, Pathway $pathway)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }

        $data = $this->validated($request, $pathway);
        if ($data['title'] !== $pathway->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $pathway->id);
        }
        if ($request->hasFile('image')) {
            $data['image'] = uploadFile($request->file('image'), 'pathways', $pathway->image);
        }

        $old = $pathway->only(['title', 'group', 'protocol_key', 'status']);
        $pathway->update($data);

        activity_log(log: 'Pathway', event: 'pathway_updated', subject: $pathway, properties: ['old' => $old, 'new' => $pathway->only(array_keys($old))]);

        return redirect()->route($this->routePrefix . '.index')->with('success', 'Pathway updated successfully.');
    }

    public function destroy(Pathway $pathway)
    {
        if (!$this->deletePermission) {
            return response()->json(['success' => false, 'message' => 'Permission denied.']);
        }
        if (\App\Models\User::where('pathway_uuid', $pathway->uuid)->exists()) {
            return response()->json(['success' => false, 'message' => 'This pathway is selected by clients. Deactivate it instead.']);
        }

        activity_log(log: 'Pathway', event: 'pathway_deleted', subject: $pathway, properties: ['title' => $pathway->title]);
        deleteFiles($pathway->image);
        $pathway->delete();

        return response()->json(['success' => true, 'message' => 'Pathway deleted successfully.']);
    }

    public function changeStatus($uuid, Request $request)
    {
        if (!$this->editPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Permission Denied']);
        }

        $pathway = Pathway::where('uuid', $uuid)->first();
        if (!$pathway) {
            return response()->json(['success' => false, 'message' => 'Pathway not found']);
        }

        $oldStatus = $pathway->status;
        $pathway->update(['status' => $request->status === 'Active' ? 'Active' : 'Inactive']);

        activity_log(log: 'Pathway', event: 'status_changed', subject: $pathway, properties: [
            'old' => ['status' => $oldStatus],
            'new' => ['status' => $pathway->status],
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    protected function validated(Request $request, ?Pathway $pathway = null): array
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255', Rule::unique('pathways', 'title')->ignore($pathway?->id)],
            'group'        => ['required', Rule::in(array_keys(self::GROUPS))],
            'description'  => ['nullable', 'string', 'max:2000'],
            'tags'         => ['nullable', 'string', 'max:500'],
            'protocol_key' => ['nullable', Rule::in(array_keys(config('protocols')))],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'status'       => ['required', 'in:Active,Inactive'],
            'image'        => ['nullable', 'image', 'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,webp'), 'max:' . env('IMAGE_SIZE', 2048)],
        ]);

        return [
            'title'        => $request->title,
            'group'        => $request->group,
            'description'  => $request->description,
            'tags'         => collect(explode(',', (string) $request->tags))->map(fn ($tag) => trim($tag))->filter()->values()->all(),
            'protocol_key' => $request->protocol_key ?: null,
            'sort_order'   => (int) $request->sort_order,
            'status'       => $request->status,
        ];
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (Pathway::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
