<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostAssessment;
use App\Models\Menu;
use App\Models\PreOrder;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostAssessmentController extends Controller
{
    /**
     * Display a listing of post-assessments for the kitchen team.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $mealType = $request->input('meal_type', 'lunch');
        
        // Get the menu items for the selected date and meal type
        $menuItems = Menu::where('date', $date)
            ->where('meal_type', $mealType)
            ->get();
            
        // Get pre-order counts for each menu item
        $preOrderCounts = PreOrder::where('date', $date)
            ->where('meal_type', $mealType)
            ->select('menu_id', DB::raw('count(*) as total_orders'))
            ->groupBy('menu_id')
            ->pluck('total_orders', 'menu_id')
            ->toArray();
            
        // Get post-assessments for the selected date and meal type
        $postAssessments = PostAssessment::where('date', $date)
            ->where('meal_type', $mealType)
            ->get()
            ->keyBy('menu_id');
            
        // Get dates with menus for the filter
        $menuDates = Menu::select('date')
            ->distinct()
            ->where('date', '<=', now()->format('Y-m-d'))
            ->orderBy('date', 'desc')
            ->limit(14)
            ->pluck('date');
            
        return view('kitchen.post-assessment', compact(
            'menuItems', 
            'preOrderCounts', 
            'postAssessments', 
            'date', 
            'mealType', 
            'menuDates'
        ));
    }
    
    /**
     * Store a newly created post-assessment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Log::info('🍽️ Kitchen Post-Assessment Store Request', [
            'user_id' => Auth::id(),
            'date' => $request->input('date'),
            'meal_type' => $request->input('meal_type'),
            'items_count' => $request->has('items') ? count($request->input('items', [])) : 0,
            'has_image' => $request->hasFile('report_image')
        ]);

        // Handle image upload validation more gracefully
        $hasValidImage = false;
        if ($request->hasFile('report_image')) {
            $file = $request->file('report_image');
            if ($file->isValid() && $file->getSize() > 0) {
                $hasValidImage = true;
            } else {
                \Log::warning('❌ Invalid image file detected, proceeding without image', [
                    'error' => $file->getError(),
                    'size' => $file->getSize(),
                    'user_id' => Auth::id()
                ]);
                // Remove the invalid file from request to avoid validation error
                $request->request->remove('report_image');
            }
        }

        $rules = [
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.prepared_quantity' => 'required|numeric|min:0',
            'items.*.leftover_quantity' => 'required|numeric|min:0',
        ];

        // Only validate image if it's present and valid
        if ($hasValidImage) {
            $rules['report_image'] = 'image|mimes:jpeg,png,jpg,gif|max:5120'; // 5MB max
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::warning('❌ Kitchen Post-Assessment Validation Failed', [
                'errors' => $validator->errors()->toArray(),
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Check if assessment already exists for this date and meal type (database has unique constraint)
            $existingAssessment = PostAssessment::where('date', $request->date)
                ->where('meal_type', $request->meal_type)
                ->first();

            if ($existingAssessment) {
                DB::rollBack();

                $message = 'A leftover report for this date and meal type already exists. Only one report per meal per day is allowed.';

                \Log::warning('❌ Duplicate post-assessment attempt', [
                    'date' => $request->date,
                    'meal_type' => $request->meal_type,
                    'existing_assessment_id' => $existingAssessment->id,
                    'existing_assessed_by' => $existingAssessment->assessed_by,
                    'current_user' => Auth::id()
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            // Process each food item
            $totalPrepared = 0;
            $totalLeftover = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $totalPrepared += $item['prepared_quantity'];
                $totalLeftover += $item['leftover_quantity'];

                $itemsData[] = [
                    'name' => $item['name'],
                    'prepared_quantity' => $item['prepared_quantity'],
                    'leftover_quantity' => $item['leftover_quantity']
                ];
            }

            // Calculate wastage percentage
            $wastagePercentage = $totalPrepared > 0 ? ($totalLeftover / $totalPrepared) * 100 : 0;

            // Handle image upload
            $imagePath = null;
            if ($hasValidImage && $request->hasFile('report_image')) {
                try {
                    $image = $request->file('report_image');
                    $imageName = 'leftover_report_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    // Create directory if it doesn't exist
                    $uploadPath = public_path('uploads/post-assessments');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    // Move the uploaded file
                    $image->move($uploadPath, $imageName);
                    $imagePath = 'uploads/post-assessments/' . $imageName;

                    \Log::info('📸 Image uploaded successfully', [
                        'original_name' => $image->getClientOriginalName(),
                        'saved_as' => $imageName,
                        'path' => $imagePath
                    ]);
                } catch (\Exception $e) {
                    \Log::error('❌ Image upload failed', [
                        'error' => $e->getMessage()
                    ]);
                    // Continue without image if upload fails
                }
            }

            // Create the assessment record
            $assessment = PostAssessment::create([
                'date' => $request->date,
                'meal_type' => $request->meal_type,
                'planned_portions' => $totalPrepared,
                'actual_portions_served' => $totalPrepared - $totalLeftover,
                'leftover_portions' => $totalLeftover,
                'food_waste_kg' => $totalLeftover, // Using leftover_portions as kg for now
                'notes' => $request->notes,
                'image_path' => $imagePath,
                'assessed_by' => Auth::id(),
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // Send notification to cook about post-meal report
            $notificationService = new NotificationService();
            $notificationService->postMealReportSubmitted([
                'assessment_id' => $assessment->id,
                'meal_type' => $request->meal_type,
                'date' => $request->date,
                'wastage_percentage' => round($wastagePercentage, 1),
                'total_prepared' => $totalPrepared,
                'total_leftover' => $totalLeftover,
                'items_count' => count($itemsData),
                'items' => $itemsData
            ]);

            DB::commit();

            \Log::info('✅ Kitchen Post-Assessment Created Successfully', [
                'assessment_id' => $assessment->id,
                'total_prepared' => $totalPrepared,
                'total_leftover' => $totalLeftover,
                'wastage_percentage' => round($wastagePercentage, 1)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leftover report submitted successfully!',
                    'assessment_id' => $assessment->id
                ]);
            }

            return redirect()->route('kitchen.post-assessment')
                ->with('success', 'Leftover report submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('❌ Kitchen Post-Assessment Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit leftover report. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to submit leftover report. Please try again.')
                ->withInput();
        }
    }
}
