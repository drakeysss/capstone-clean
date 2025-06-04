<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $totalOrders = Order::count();
        $totalMenuItems = Menu::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $recentOrders = Order::with('student')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalMenuItems',
            'totalRevenue',
            'recentOrders'
        ));
    }

    public function menus()
    {
        $menus = Menu::all();
        return view('admin.menus', compact('menus'));
    }

    public function inventory()
    {
        return view('admin.inventory');
    }

    public function suppliers()
    {
        return view('admin.suppliers');
    }

    public function purchases()
    {
        return view('admin.purchases');
    }

    public function reports()
    {
        return view('admin.reports');
    }
}



