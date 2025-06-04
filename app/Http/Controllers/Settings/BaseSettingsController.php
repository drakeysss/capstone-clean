<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BaseSettingsController extends Controller
{
    protected $role;
    protected $viewPath;

    public function __construct($role, $viewPath)
    {
        $this->middleware("role:$role");
        $this->role = $role;
        $this->viewPath = $viewPath;
    }

    public function index()
    {
        return view("$this->viewPath.settings");
    }

    public function updateSettings(Request $request)
    {
        // Common settings update logic
        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function updatePreferences(Request $request)
    {
        // Common preferences update logic
        return redirect()->back()->with('success', 'Preferences updated successfully');
    }
}
