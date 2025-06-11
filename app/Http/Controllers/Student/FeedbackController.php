<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Menu;
use App\Models\Meal;
use App\Services\NotificationService;
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

        // Get student's previous feedback
        $studentFeedback = Feedback::where('student_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('student.feedback.index', compact('studentFeedback'));
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
            'meal_name' => 'required|string|max:255',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'meal_date' => 'required|date|before_or_equal:today',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'suggestions' => 'nullable|string|max:500',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Allow multiple feedback submissions - students can provide feedback as many times as they want
        // Create new feedback entry each time
        $feedback = Feedback::create([
            'student_id' => $user->id,
            'meal_id' => null, // No longer required since we're allowing manual input
            'meal_date' => $request->meal_date,
            'meal_type' => $request->meal_type,
            'meal_name' => $request->meal_name,
            'rating' => $request->rating,
            'comments' => $request->comment,
            'suggestions' => $request->suggestions,
            'food_quality' => [],
            'dietary_concerns' => [],
            'is_anonymous' => $request->has('is_anonymous') && $request->is_anonymous,
        ]);

        // Send notifications to cook and kitchen staff
        $notificationService = new NotificationService();
        $notificationService->feedbackSubmitted([
            'meal_name' => $request->meal_name,
            'meal_type' => $request->meal_type,
            'rating' => $request->rating,
            'student_name' => $request->has('is_anonymous') && $request->is_anonymous ? 'Anonymous' : $user->name,
            'feedback_id' => $feedback->id
        ]);

        return redirect()->route('student.feedback')->with('success', 'Thank you for your feedback!');
    }
}
