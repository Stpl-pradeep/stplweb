<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
	use AuthenticatesUsers;

    // public function login(Request $request)
    // {
    // 	$credentials = $request->only('email', 'password');
    // 	if(Auth::guard('admin')->attempt($credentials, $request->remember)) {
    // 		$user = Admin::where('email', $request->email)->first();
    // 		Auth::guard('admin')->login($user);
    // 		return redirect()->route('admin.home');
    // 	}
    // return redirect()->route('admin.login')->with('status', 'Failed To Precess Login');
    // }

        /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.home');
    }

            /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
    

        /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
       return redirect()->route('admin.login');
    }

        /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return FacadesAuth::guard('admin');
    }

    // public function logout(){
    // 	$guards = array_keys(config('auth.guards'));
    // 	dd($guards);
    // 	if(Auth::guard('admin')->logout()) {
    // 		return redirect()->route('admin.login')->with('status','Logout Successfully!');
    // 	}
    // }

 public function AdminDashboard(){
     $userId = FacadesAuth::id();
  return view('admin.home');
 }

    public function changePassword()
    {
        $Title = "Change Password";
        return view('admin.changepassword.changepassword', compact('Title'));
    }

    public function updateChangePass(Request $request)
    {
        $userId = FacadesAuth::guard('admin')->user()->id;
        $data = $request->all();
        $request->validate(
            [
                'current_password' => 'required',
                'password' => 'required|string|confirmed|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => 'required',
            ],
            [
                'password.regex' => 'Your password must be more than 6 characters long, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character.'
            ]
        );

        $old_pwd = Admin::where('id', $userId)->first();
        $current_password = $data['current_password'];

        if (Hash::check($current_password, $old_pwd->password)) {
            $new_password = bcrypt($data['password']);
            Admin::where('id', $userId)->update(['password' => $new_password]);
            return back()->with('msg', 'Password successfully changed!');
        }
        return back()->with('errormsg', 'Current password does not match!');
    }

}
