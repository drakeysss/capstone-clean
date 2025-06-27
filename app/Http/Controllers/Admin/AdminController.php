<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreOrder;
use App\Models\Menu;
use App\Services\DashboardViewService;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $totalOrders = PreOrder::count();
        $totalMenuItems = Menu::count();

        // Get recent orders with "show once" logic
        $recentOrders = DashboardViewService::processDashboardData(
            PreOrder::with('user')->latest()->take(10),
            'recent_orders'
        );

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalMenuItems',
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



