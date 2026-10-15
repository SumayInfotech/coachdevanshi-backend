<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    protected $viewPrefix = 'admin.settings';
    protected $routePrefix = 'admin.settings';

    private $viewPermission;
    private $addPermission;
    private $editPermission;
    private $deletePermission;

    public function __construct()
    {
        $this->viewPermission   = hasPermission('view_settings');
        // $this->addPermission    = hasPermission('add_settings');
        $this->editPermission   = hasPermission('edit_settings');
        // $this->deletePermission = hasPermission('delete_settings');

    }
    public function index()
    {
        if (!$this->viewPermission) abort(403);
        $settings = Setting::orderBy('key')->get()->keyBy('key');
        return view($this->viewPrefix.'.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!$this->editPermission) abort(403);
        $request->validate([
            'language' => 'nullable|in:en,it',
            'project_title' => 'nullable|string|max:30',
            'footer_text'   => 'nullable|string|max:50',
            'pagination_length' => pagination_length_validation_rule(),
            'loader_image' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
            'favicon_icon' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
            'app_icon' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
            'sidebar_logo_dark_full' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
            'sidebar_logo_dark_toggle' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
            'sidebar_logo_light_full' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
            'sidebar_logo_light_toggle' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
        ]);
        $oldLanguage = Setting::where('key', 'language')->value('value');
        $oldData = Setting::pluck('value', 'key')->toArray();
        foreach ($request->except('_token') as $key => $value) {

            // IMAGE FIELDS
            if ($request->hasFile($key)) {

                $oldFile = Setting::where('key', $key)->value('value');

                $value = uploadFile(
                    $request->file($key),
                    'settings',
                    $oldFile
                );
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            

            // if ($key === 'language') {
            //     Cache::forget('app_language');

            //     if ($request->ajax()) {
            //         session()->put(
            //             'language_changed_success',
            //             __('messages.language_updated')
            //         );
            //     }
            // }
            
           
        }
        $newData = Setting::pluck('value', 'key')->toArray();
        activity_log(
            log: 'Settings',
            event: 'updated',
            subject: null,
            properties: [
                'old' => $oldData,
                'new' => $newData
            ]
        );

        // If language is changed, clear the cache and set a session variable to show success message after page reload
        $newLanguage = $request->input('language');
        if ($newLanguage && $oldLanguage !== $newLanguage) {

            Cache::forget('app_language');

            if ($request->ajax()) {
                session()->put(
                    'language_changed_success',
                    __('messages.language_updated')
                );
            }
        }
         //Normal settings page submit
        if (!$request->ajax()) {
            return back()->with('success', __('messages.settings_updated'));
        }

        //AJAX response
        return response()->json(['success' => true]);
    }
}
