<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Settings\BaseSettingsController;
use Illuminate\Http\Request;

class StudentSettingsController extends BaseSettingsController
{
    public function __construct()
    {
        parent::__construct('student', 'student');
    }

    public function index()
    {
        return view('student.settings');
    }

    public function viewSettings()
    {
        return view('student.settings');
    }

    public function updateSettings(Request $request)
    {
        // Update settings logic
        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function updatePreferences(Request $request)
    {
        // Student-specific preference updates
        // Add any additional student-specific logic here
        return parent::updatePreferences($request);
    }
}