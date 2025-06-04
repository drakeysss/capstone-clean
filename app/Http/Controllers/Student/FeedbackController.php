<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the student feedback form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get past week's menu items for feedback
        $pastWeekMenus = Menu::where('date', '>=', Carbon::now()->subDays(7))
            ->where('date', '<=', Carbon::now())
            ->orderBy('date', 'desc')
            ->orderBy('meal_type')
            ->get();
        
        // Get student's previous feedback
        $studentFeedback = Feedback::where('user_id', $user->id)
            ->with('menu')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // If menu_id is provided, get the selected menu item
        $selectedMenu = null;
        if ($request->has('menu_id')) {
            $selectedMenu = Menu::find($request->menu_id);
        }
        
        return view('student.feedback.index', compact('pastWeekMenus', 'studentFeedback', 'selectedMenu'));
    }

    /**
     * Store a newly created feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'suggestions' => 'nullable|string|max:500',
        ]);
        
        $user = Auth::user();
        
        // Check if user has already provided feedback for this menu item
        $existingFeedback = Feedback::where('user_id', $user->id)
            ->where('menu_id', $request->menu_id)
            ->first();
        
        if ($existingFeedback) {
            // Update existing feedback
            $existingFeedback->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'suggestions' => $request->suggestions,
            ]);
            
            return redirect()->route('student.feedback')->with('success', 'Your feedback has been updated successfully!');
        }
        
        // Create new feedback
        Feedback::create([
            'user_id' => $user->id,
            'menu_id' => $request->menu_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'suggestions' => $request->suggestions,
        ]);
        
        return redirect()->route('student.feedback')->with('success', 'Thank you for your feedback!');
    }
}
