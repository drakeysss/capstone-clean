<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostAssessment;
use App\Models\Menu;
use App\Models\PreOrder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostAssessmentController extends Controller
{
    /**
     * Display a listing of post-assessments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        \Log::info('🍽️ Cook Post-Assessment Index Request', [
            'filters' => $request->all(),
            'user_id' => Auth::id()
        ]);

        $date = $request->input('date');
        $mealType = $request->input('meal_type');

        // Build query for post-assessments sent by kitchen team
        $query = PostAssessment::with(['assessedBy'])
            ->where('is_completed', true)
            ->orderBy('date', 'desc')
            ->orderBy('meal_type', 'asc');

        // Apply filters if provided
        if ($date) {
            $query->where('date', $date);
        }

        if ($mealType) {
            $query->where('meal_type', $mealType);
        }

        // Get assessments (reports from kitchen)
        $assessments = $query->get();

        // Get dates with post-assessments for the filter
        $assessmentDates = PostAssessment::select('date')
            ->distinct()
            ->where('is_completed', true)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->pluck('date');

        \Log::info('📊 Cook Post-Assessment Data Loaded', [
            'total_assessments' => $assessments->count(),
            'date_filter' => $date,
            'meal_type_filter' => $mealType
        ]);

        return view('cook.post-assessment', compact(
            'assessments',
            'date',
            'mealType',
            'assessmentDates'
        ));
    }

    /**
     * Update a post-assessment (if needed for cook interface)
     */
    public function update(Request $request, $id)
    {
        \Log::info('🍽️ Cook Post-Assessment Update Request', [
            'assessment_id' => $id,
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        $assessment = PostAssessment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'total_prepared' => 'required|numeric|min:0',
            'total_leftover' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'report_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $totalConsumed = $request->total_prepared - $request->total_leftover;

            // Handle image upload
            $imagePath = $assessment->image_path; // Keep existing image by default
            if ($request->hasFile('report_image')) {
                $image = $request->file('report_image');

                // Delete old image if it exists
                if ($assessment->image_path && file_exists(public_path($assessment->image_path))) {
                    try {
                        unlink(public_path($assessment->image_path));
                        \Log::info('📸 Old image deleted during update', ['path' => $assessment->image_path]);
                    } catch (\Exception $e) {
                        \Log::warning('⚠️ Failed to delete old image', [
                            'path' => $assessment->image_path,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Store new image
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $uploadPath = 'uploads/post-assessments';

                // Create directory if it doesn't exist
                if (!file_exists(public_path($uploadPath))) {
                    mkdir(public_path($uploadPath), 0755, true);
                }

                $image->move(public_path($uploadPath), $filename);
                $imagePath = $uploadPath . '/' . $filename;

                \Log::info('📸 New image uploaded during update', [
                    'filename' => $filename,
                    'path' => $imagePath,
                    'assessment_id' => $assessment->id
                ]);
            }

            $assessment->update([
                'date' => $request->date,
                'meal_type' => $request->meal_type,
                'planned_portions' => $request->total_prepared,
                'leftover_portions' => $request->total_leftover,
                'actual_portions_served' => $totalConsumed,
                'notes' => $request->notes,
                'image_path' => $imagePath,
            ]);

            \Log::info('✅ Cook Post-Assessment Updated Successfully', [
                'assessment_id' => $assessment->id,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assessment updated successfully',
                'assessment' => $assessment
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Cook Post-Assessment Update Failed', [
                'assessment_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update assessment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a post-assessment report
     */
    public function destroy($id)
    {
        \Log::info('🗑️ Cook Post-Assessment Delete Request', [
            'assessment_id' => $id,
            'user_id' => Auth::id()
        ]);

        try {
            $assessment = PostAssessment::findOrFail($id);

            // Store image path for cleanup
            $imagePath = $assessment->image_path;

            // Delete the assessment record
            $assessment->delete();

            // Clean up associated image file if it exists
            if ($imagePath && file_exists(public_path($imagePath))) {
                try {
                    unlink(public_path($imagePath));
                    \Log::info('📸 Image file deleted', ['path' => $imagePath]);
                } catch (\Exception $e) {
                    \Log::warning('⚠️ Failed to delete image file', [
                        'path' => $imagePath,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            \Log::info('✅ Cook Post-Assessment Deleted Successfully', [
                'assessment_id' => $id,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assessment report deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('❌ Assessment not found for deletion', [
                'assessment_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Assessment report not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('❌ Cook Post-Assessment Delete Failed', [
                'assessment_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete assessment: ' . $e->getMessage()
            ], 500);
        }
    }

}
