<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class RegistrationController extends Controller
{
    public function login_page()
    {
        return view('rtl.admin.modules.auth.login');
    }
    public function registration_page()
    {
        return view('rtl.admin.modules.auth.register');
    }
    public function register(Request $request)
    {
       // dd($request);
               // Validate the form data
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Create a new user instance and save it to the database
        $user = new User([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        $user->save();

        // Optionally, you can redirect to a success page or do something else
        return redirect()->back()->with('success', 'User registered successfully');

    }
    public function signin(Request $request)
    {
        // Validate the login data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed
            return redirect()->route('dashboard'); // Redirect to the intended URL or a default one
        } else {
            // Authentication failed
            return redirect()->back()->withErrors(['email' => 'Invalid credentials']); // You can customize the error message
        }
    }
}
