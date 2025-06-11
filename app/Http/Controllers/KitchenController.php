<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function dailyMenu()
    {
        $todayMenu = Menu::where('day', now()->format('l'))->first();
        $weeklyMenu = Menu::all()->groupBy('day');
        return view('kitchen.daily-menu', compact('todayMenu', 'weeklyMenu'));
    }
} 