<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token)
    {
        return view('admin.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', __('Password reset successfully'))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetFormForCustomer(Request $request, string $token)
    {
         return view('customer.auth.reset-password', compact('token'));
    }

    public function resetCustomerPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('reset_password_token', $request->token)
                    ->where('reset_password_expires_at', '>=', now())
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'token' => 'Reset link is invalid or expired.'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_password_token' => null,
            'reset_password_expires_at' => null,
        ]);

        return redirect()->back()->with('success', 'Password reset successfully');
    }

    
}
