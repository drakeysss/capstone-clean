<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class StudentFeedbackController extends Controller
{
    /**
     * Display a listing of student feedback.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'week');
        $menuId = $request->input('menu_id');
        $rating = $request->input('rating');
        
        $query = Feedback::with(['user'])
            ->orderBy('created_at', 'desc');
            
        // Filter by period
        if ($period === 'week') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        }
        
        // Filter by meal type (instead of menu_id)
        if ($request->input('meal_type')) {
            $query->where('meal_type', $request->input('meal_type'));
        }
        
        // Filter by rating
        if ($rating) {
            $query->where('rating', $rating);
        }
        
        $feedback = $query->paginate(20);
        
        // Get average ratings by meal type
        $averageRatings = Feedback::select(
            'meal_type',
            DB::raw('AVG(rating) as average_rating'),
            DB::raw('COUNT(*) as total_ratings')
        )
            ->groupBy('meal_type')
            ->having('total_ratings', '>=', 3)
            ->orderBy('average_rating', 'desc')
            ->get();

        // Get meal types for filter
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
            
        // Get feedback summary stats
        $totalFeedback = Feedback::count();
        $averageOverallRating = Feedback::avg('rating') ?? 0;
        $recentTrend = $this->calculateRecentTrend();
        
        return view('cook.student-feedback', compact(
            'feedback',
            'averageRatings',
            'mealTypes',
            'period',
            'rating',
            'totalFeedback',
            'averageOverallRating',
            'recentTrend'
        ));
    }
    
    /**
     * Calculate the recent trend in ratings.
     *
     * @return array
     */
    private function calculateRecentTrend()
    {
        // Get average rating for last 7 days
        $lastWeek = Feedback::where('created_at', '>=', now()->subDays(7))
            ->avg('rating') ?? 0;
            
        // Get average rating for previous 7 days
        $previousWeek = Feedback::where('created_at', '>=', now()->subDays(14))
            ->where('created_at', '<', now()->subDays(7))
            ->avg('rating') ?? 0;
            
        $change = $previousWeek > 0 ? (($lastWeek - $previousWeek) / $previousWeek) * 100 : 0;
        
        return [
            'last_week' => round($lastWeek, 1),
            'previous_week' => round($previousWeek, 1),
            'change' => round($change, 1),
            'direction' => $change >= 0 ? 'up' : 'down'
        ];
    }
}
