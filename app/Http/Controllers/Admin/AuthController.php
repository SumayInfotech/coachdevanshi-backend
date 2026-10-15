<?php
  
namespace App\Http\Controllers\Admin;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Session;
use App\Models\User;
use Hash;
use Validator;
use Illuminate\Support\Str;
use App\Mail\SendNewUserPassword;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function index()
    {
        return view('admin.auth.login');
    }

    public function postLogin(Request $request)
    {
        // echo "<pre>";print_r($request->all());"</pre>";die;
        // Validate input
        $request->validate([
            'login' => 'required', // Accepts email, mobile_no_1, or username
            'password' => 'required',
        ]);

        // Check if login input is an email, mobile number, or username
        $user = User::active()->where('email', $request->login)->where('user_type','admin')->first();
        if (!$user) {
            return redirect()->route('admin.login')->withErrors(['login' => 'Invalid email or credentials.']);
        }
        // $frontendUserRole = Role::where('name', 'frontend_user')->first();
        // if(!$frontendUserRole) {
        //     return redirect()->route('admin.login')->withErrors(['login' => 'Frontend role not found. Please contact support.']);
        // }        
        // //Check if the user role is frontend_user
        // if ($user->role_uuid === $frontendUserRole->uuid) {
        //     return redirect()->route('admin.login')->withErrors(['login' => 'You are not authorized to access this portal.']);
        // }

        // Attempt login with found user credentials
        if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
            return redirect()->intended(route('admin.dashboard'))->withSuccess('You have successfully logged in.');
        }
        return  redirect()->route('admin.login')->withErrors(['password' => 'Oops! You have entered an invalid password.']);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout() {
        Session::flush();
        Auth::logout();
  
        return redirect()->route('admin.login');
    }

    public function changePassword()
    {
        $data = Auth::user();
        activity_log(
            log: 'User',
            event: 'viewed_change_password',
            subject: $data
        );
        return view('admin.auth.change-password', compact('data'));
    }

    public function updatePassword(Request $request)
    {
        $user =  Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|max:15|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('users.current_password_incorrect'),
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        activity_log(
            log: 'User',
            event: 'password_changed',
            subject: $user,
            properties: [
                'message' => 'User changed own password'
            ]
        );

        return back()->with('success', __('users.password_updated_success'));
    }
}