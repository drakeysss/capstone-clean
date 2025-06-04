<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        // Log the attempt for debugging
        \Log::info('Login attempt for: ' . $request->email);
        
        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            \Log::warning('User not found: ' . $request->email);
            return back()->withErrors([
                'email' => 'No user found with this email address.',
            ])->onlyInput('email');
        }
        
        // Attempt authentication
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            \Log::info('Login successful for: ' . $request->email);
            $request->session()->regenerate();
            
            // Get the dashboard route based on user role
            return redirect()->route($user->getDashboardRoute());
        }
        
        \Log::warning('Invalid credentials for: ' . $request->email);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}