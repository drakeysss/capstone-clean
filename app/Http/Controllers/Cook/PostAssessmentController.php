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
            
        // Get dates with post-assessments for the filter
        $assessmentDates = PostAssessment::select('date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->limit(30)
            ->pluck('date');
            
        return view('cook.post-assessment', compact(
            'menuItems', 
            'preOrderCounts', 
            'postAssessments', 
            'date', 
            'mealType', 
            'assessmentDates'
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
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'menu_id' => 'required|exists:menus,id',
            'prepared_quantity' => 'required|numeric|min:0',
            'leftover_quantity' => 'required|numeric|min:0',
            'wastage_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if assessment already exists
        $existingAssessment = PostAssessment::where('date', $request->date)
            ->where('meal_type', $request->meal_type)
            ->where('menu_id', $request->menu_id)
            ->first();
            
        if ($existingAssessment) {
            return redirect()->back()
                ->with('error', 'A post-assessment for this menu item already exists.')
                ->withInput();
        }

        PostAssessment::create([
            'date' => $request->date,
            'meal_type' => $request->meal_type,
            'menu_id' => $request->menu_id,
            'user_id' => Auth::id(),
            'prepared_quantity' => $request->prepared_quantity,
            'leftover_quantity' => $request->leftover_quantity,
            'wastage_percentage' => $request->wastage_percentage,
            'notes' => $request->notes,
        ]);

        return redirect()->route('cook.post-assessment', ['date' => $request->date, 'meal_type' => $request->meal_type])
            ->with('success', 'Post-assessment added successfully.');
    }
    
    /**
     * Update the specified post-assessment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'prepared_quantity' => 'required|numeric|min:0',
            'leftover_quantity' => 'required|numeric|min:0',
            'wastage_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $assessment = PostAssessment::findOrFail($id);
        $assessment->update([
            'prepared_quantity' => $request->prepared_quantity,
            'leftover_quantity' => $request->leftover_quantity,
            'wastage_percentage' => $request->wastage_percentage,
            'notes' => $request->notes,
        ]);

        return redirect()->route('cook.post-assessment', ['date' => $assessment->date, 'meal_type' => $assessment->meal_type])
            ->with('success', 'Post-assessment updated successfully.');
    }
}
