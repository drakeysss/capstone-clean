<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Settings\BaseSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AdminSettingsController extends BaseSettingsController
{
    public function __construct()
    {
        parent::__construct('admin', 'admin');
    }

    public function index()
    {
        return view('admin.settings');
    }
    
    public function updateSettings(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'siteName' => 'required|string|max:255',
            'siteDescription' => 'required|string',
            'contactEmail' => 'required|email',
            'timezone' => 'required|string',
            'dateFormat' => 'required|string',
            'currency' => 'required|string',
            'mailDriver' => 'required|string',
            'mailHost' => 'required|string',
            'mailPort' => 'required|numeric',
            'emailNotifications' => 'sometimes|boolean',
            'systemNotifications' => 'sometimes|boolean',
            'lowStockAlerts' => 'sometimes|boolean',
        ]);
        
        // Here you would typically save these settings to your database
        // For example, using a Settings model or config files
        
        // For demonstration, we'll just return success
        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}



