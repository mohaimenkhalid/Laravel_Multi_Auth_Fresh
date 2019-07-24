<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Admin;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
      return view('admin.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string',
             'password' => 'required',
        ]);

         //Find user By this Email...

         $admin = Admin::where('email', $request->email)->first();

         //If admin not find
         if (is_null($admin)) {
            \Session::flash('error', 'Email does not found in Database.Please contact with Web Admin.');
            return redirect()->back();
            }
         
             //login this admin...if email and passwd mached..

            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password'=>$request->password], $request->remember)) {
                //Login Now and redirect
                return redirect()->intended(route('admin.home'));
                
                } else{
                //if passwd don't match

                    \Session::flash('error', 'Incorrect Email or Password!');
                    return redirect()->back()->withInput($request->only('email, remember'));
                }
         
    }


    // admin logout....

    public function logout(Request $request)
    {
        $this->guard()->logout(); 

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route('admin.login');
    }
}
