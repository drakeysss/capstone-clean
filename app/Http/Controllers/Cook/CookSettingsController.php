<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Settings\BaseSettingsController;

class CookSettingsController extends BaseSettingsController
{
    public function __construct()
    {
        parent::__construct('cook', 'cook');
    }

    public function index()
    {
        return view('cook.settings');
    }
}