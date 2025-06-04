<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class StudentFeedbackController extends Controller
{
    /**
     * Display a listing of student feedback for the kitchen team.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'week');
        $menuId = $request->input('menu_id');
        
        $query = Feedback::with(['user', 'menu'])
            ->orderBy('created_at', 'desc');
            
        // Filter by period
        if ($period === 'week') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        }
        
        // Filter by menu item
        if ($menuId) {
            $query->where('menu_id', $menuId);
        }
        
        $feedback = $query->paginate(15);
        
        // Get top-rated menu items
        $topRatedItems = Feedback::select(
            'menu_id',
            DB::raw('AVG(rating) as average_rating'),
            DB::raw('COUNT(*) as total_ratings')
        )
            ->groupBy('menu_id')
            ->having('total_ratings', '>=', 3)
            ->orderBy('average_rating', 'desc')
            ->limit(5)
            ->with('menu')
            ->get();
            
        // Get low-rated menu items
        $lowRatedItems = Feedback::select(
            'menu_id',
            DB::raw('AVG(rating) as average_rating'),
            DB::raw('COUNT(*) as total_ratings')
        )
            ->groupBy('menu_id')
            ->having('total_ratings', '>=', 3)
            ->orderBy('average_rating', 'asc')
            ->limit(5)
            ->with('menu')
            ->get();
            
        // Get menu items for filter
        $menuItems = Menu::orderBy('date', 'desc')
            ->limit(30)
            ->get();
            
        return view('kitchen.student-feedback', compact(
            'feedback',
            'topRatedItems',
            'lowRatedItems',
            'menuItems',
            'period',
            'menuId'
        ));
    }
}
