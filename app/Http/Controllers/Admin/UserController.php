<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\DataTables\UserDataTable;
use App\DataTables\UserActivityDataTable;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class UserController extends Controller
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
        view()->share([
            'viewPrefix' => $this->viewPrefix,
            'routePrefix' => $this->routePrefix,
            'addPermission' =>  $this->addPermission,
            'moduleFullName'  => __('users.module_plural'),
            'moduleShortName' => __('users.module_name'),
        ]);
    }

    

    public function index(UserDataTable $dataTable)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }
        $states = State::active()->where('country_id', '101')->orderBy('name','asc')->get();
        return $dataTable->render($this->viewPrefix.'.index',compact('states'));
    }


    public function create()
    {
        // sleep(2);
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        }

        // $states = State::where('status', 'Active')->where('country_id', '101')->orderBy('name','asc')->get();
        $states = State()->active()->where('country_id', '101')->orderBy('name','asc')->get();
        $roles = Role()->active()->get();
        return view($this->viewPrefix.'.create', compact('states', 'roles'));
    }
    
    public function store(Request $request)
    {
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        } 
        
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|digits:10|unique:users,mobile',
            'status' => 'required|in:Active,Inactive',
            'password' => 'required|string|min:6|max:15',
            'role_uuid' => 'required|exists:roles,uuid',
            'image' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
            'state_id' => 'nullable|exists:states,id',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        
    
        $user = new User();
        $user->user_type = 'admin';
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile_no;
        $user->status = $request->status;
        $user->password = Hash::make($request->password);
        $user->role_uuid = $request->role_uuid;
        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;

        if(!empty($request->file('image'))){
            $image = uploadFile($request->file('image'), 'users');
            $user->profile_image = $image;
        } 
        $user->save();
        activity_log(
            log: 'User',
            event: 'created',
            subject: $user,
            properties: ['new' => $user->toArray()]
        );

        return redirect()->route($this->viewPrefix)->with('success', __('users.created_success'));
    }

    public function edit($uuid)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }

        
        $user = User::where('uuid', $uuid)->first();
        activity_log(log: 'User',event: 'edit_opened',subject: $user);
        // $countries = [Country::where('status', 'Active')->get()];
        $countries = [];
        $states = State::where('country_id',101)->get(); 
        $roles = Role::active()->get();
        $cities = [];
        return view($this->viewPrefix.'.edit', compact('user', 'countries', 'states', 'roles', 'cities'));
    }

    // Method to handle the update
    public function update(Request $request,$uuid)
    {
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $uuid . ',uuid',
            'email' => 'required|email|unique:users,email,' . $uuid . ',uuid',
            'mobile_no' => 'required|digits:10|unique:users,mobile,' . $uuid . ',uuid',
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:6|max:15',
            'image' => [
                'nullable',
                'image',
                'mimes:' . env('IMAGE_TYPES', 'jpg,jpeg,png,gif'),
                'max:' . env('IMAGE_SIZE', 2048)
            ],
        ]);
        $user = User::where('uuid',$uuid)->firstOrFail();
        $oldData = $user->getOriginal();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile_no;
        $user->status = $request->status;
        $user->role_uuid = $request->role_uuid;
        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }
        if(!empty($request->file('image'))){
            $image = uploadFile($request->file('image'), 'users',$user->profile_image);
            $user->profile_image = $image;
        } 
        $user->save();

        activity_log(
            log: 'User',
            event: 'updated',
            subject: $user,
            properties: [
                'old' => $oldData,
                'new' => $user->fresh()->toArray()
            ]
        );

        return redirect()->route($this->viewPrefix)->with('success', __('users.updated_success'));
    }

    public function delete($uuid)
    {
        if (!$this->deletePermission) {
            return response()->json(['success' => false,  'message' => __('users.permission_denied')]);
        }

        try {
            $userObj = User::where('uuid', $uuid)->firstOrFail();
            $deletedData = $userObj->toArray();

            //Profile Image Delete 
            if ($userObj->profile_image) {
                $imagePath = "uploads/{$userObj->profile_image}";
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            
            $userObj->delete();

            activity_log(
                log: 'User',
                event: 'deleted',
                subject: $userObj,
                properties: [
                    'data' => $deletedData
                ]
            );

            return response()->json(['success' => true,  __('users.deleted_success')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function changeStatus($uuid, Request $request)
    {
        if (!$this->editPermission) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Permission Denied']);
        }
        $userObj = User::where('uuid', $uuid)->first();
        $oldStatus = $userObj->status;
        if (!$userObj) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        // Update role status
        $userObj->status = $request->status;
        $userObj->save();

        //ACTIVITY LOG
        activity_log(
            log: 'User',
            event: 'status_changed',
            subject: $userObj,
            properties: [
                'old' => ['status' => $oldStatus],
                'new' => ['status' => $request->status]
            ]
        );
        return response()->json(['success' => true, 'message' => __('users.status_updated')]);
    }

    public function view($uuid)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }
        $data = User::where('uuid', $uuid)->firstOrFail();
        activity_log(log: 'User',event: 'viewed',subject: $data);

        if ($data) {
            return view($this->viewPrefix.'.view', compact('data'));
        } 
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->orderBy('name','asc')->get();
        return response()->json($cities);
    }
    public function changePassword()
    {
        echo "change pass";die;
        $data =  Auth::user();
        activity_log(
            log: 'User',
            event: 'viewed_change_password',
            subject: $data
        );
        return view($this->viewPrefix.'.change-password', compact('data'));
    }
    public function updatePassword(Request $request)
    {
        echo "updatepassword";die;
        $data = Auth::guard('admin')->user();
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|max:15|confirmed',
        ]);  
        // 🔐 Verify current password
        if (!\Hash::check($request->current_password, $data->password)) {
            return back()->withErrors([
                'current_password' => __('auth.password')
            ]);
        }

        // Update password
        $data->password = \Hash::make($request->new_password);
        $data->save();

        // 🔥 Activity log (NO password data stored)
        activity_log(
            log: 'User',
            event: 'password_changed',
            subject: $data,
            properties: [
                'message' => 'User changed own password'
            ]
        );

        return back()->with('success', __('auth.password_updated'));
    }

    //View Activity Page
    public function activity(UserActivityDataTable $dataTable, $uuid)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('uuid', $uuid)->firstOrFail();

        return $dataTable->render(
            $this->viewPrefix . '.activity',
            compact('user')
        );
    }
    //View Activity Logs
    public function changes($id)
    {
        $activity = ActivityLog::findOrFail($id);

        return response()->json([
            'old' => $activity->properties['old'] ?? [],
            'new' => $activity->properties['new'] ?? [],
        ]);
    }

    public function updateLanguage(Request $request) {
        $oldLanguage = auth()->check() ? (auth()->user()->language ?: Setting::where('key', 'language')->value('value')) : Setting::where('key', 'language')->value('value');

        $newLanguage = $request->input('language');
        if ($newLanguage && $oldLanguage !== $newLanguage) {

            if (auth()->check()) {
                auth()->user()->update([
                    'language' => $newLanguage
                ]);
            } else {
                session()->put('language', $newLanguage);
            }

            if ($request->ajax()) {
                session()->put(
                    'language_changed_success',
                    __('messages.language_updated')
                );
            }
        }
    }
}
