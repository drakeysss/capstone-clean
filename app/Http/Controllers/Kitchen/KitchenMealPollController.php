<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\KitchenMenuPoll;
use App\Models\KitchenPollResponse;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenMealPollController extends Controller
{
    /**
     * Get active polls for students
     */
    public function getActivePolls()
    {
        try {
            \Log::info('🔄 Kitchen KitchenMealPollController: Getting active polls');
            
            $activePolls = KitchenMenuPoll::where('status', 'active')
                ->orWhere('status', 'sent')
                ->where('poll_date', '>=', now()->format('Y-m-d'))
                ->orderBy('poll_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'polls' => $activePolls,
                'message' => 'Active polls retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Kitchen KitchenMealPollController error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load polls: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new poll
     */
    public function createPoll(Request $request)
    {
        try {
            $request->validate([
                'meal_name' => 'required|string|max:255',
                'ingredients' => 'nullable|string',
                'poll_date' => 'required|date|after_or_equal:today',
                'meal_type' => 'required|string|in:breakfast,lunch,dinner',
                'deadline' => 'required|date_format:H:i'
            ]);

            $poll = KitchenMenuPoll::create([
                'meal_name' => $request->meal_name,
                'ingredients' => $request->ingredients,
                'poll_date' => $request->poll_date,
                'meal_type' => $request->meal_type,
                'deadline' => $request->deadline,
                'status' => 'draft',
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'poll' => $poll,
                'message' => 'Poll created successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Kitchen KitchenMealPollController create error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create poll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send poll to students
     */
    public function sendPoll(Request $request)
    {
        try {
            $request->validate([
                'poll_id' => 'required|exists:kitchen_menu_polls,id'
            ]);

            $poll = KitchenMenuPoll::findOrFail($request->poll_id);

            if ($poll->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft polls can be sent'
                ], 400);
            }

            // Update poll status
            $poll->update([
                'status' => 'active',
                'sent_at' => now()
            ]);

            // Send notifications to students
            $notificationService = new NotificationService();
            $notificationService->pollCreated([
                'poll_id' => $poll->id,
                'meal_name' => $poll->meal_name,
                'poll_date' => $poll->poll_date->format('Y-m-d'),
                'meal_type' => $poll->meal_type
            ]);

            $studentCount = User::where('role', 'student')->count();

            return response()->json([
                'success' => true,
                'message' => "Poll sent to {$studentCount} students",
                'student_count' => $studentCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Kitchen KitchenMealPollController send error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send poll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get poll results
     */
    public function getPollResults($pollId)
    {
        try {
            $poll = KitchenMenuPoll::with(['responses'])->findOrFail($pollId);
            $totalStudents = User::where('role', 'student')->count();

            $yesCount = $poll->responses()->where('will_eat', true)->count();
            $noCount = $poll->responses()->where('will_eat', false)->count();
            $totalResponses = $poll->responses()->count();

            $responseRate = $totalStudents > 0 ? ($totalResponses / $totalStudents) * 100 : 0;
            $participationRate = $totalStudents > 0 ? ($yesCount / $totalStudents) * 100 : 0;

            return response()->json([
                'success' => true,
                'results' => [
                    'poll' => $poll,
                    'total_students' => $totalStudents,
                    'total_responses' => $totalResponses,
                    'yes_count' => $yesCount,
                    'no_count' => $noCount,
                    'response_rate' => round($responseRate, 1),
                    'participation_rate' => round($participationRate, 1)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Kitchen KitchenMealPollController results error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load poll results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a poll
     */
    public function deletePoll($pollId)
    {
        try {
            $poll = KitchenMenuPoll::findOrFail($pollId);

            // Delete associated responses first
            $poll->responses()->delete();
            
            // Delete the poll
            $poll->delete();

            return response()->json([
                'success' => true,
                'message' => 'Poll deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Kitchen KitchenMealPollController delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete poll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update poll deadline
     */
    public function updatePollDeadline(Request $request)
    {
        try {
            $request->validate([
                'poll_id' => 'required|exists:kitchen_menu_polls,id',
                'deadline' => 'required|date_format:H:i'
            ]);

            $poll = KitchenMenuPoll::findOrFail($request->poll_id);
            
            $poll->update([
                'deadline' => $request->deadline
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Poll deadline updated successfully',
                'poll' => $poll
            ]);

        } catch (\Exception $e) {
            \Log::error('Kitchen KitchenMealPollController deadline update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update deadline: ' . $e->getMessage()
            ], 500);
        }
    }
}
