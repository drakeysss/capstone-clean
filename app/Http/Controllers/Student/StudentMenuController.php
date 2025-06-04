<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Carbon\Carbon;

class StudentMenuController extends Controller
{
    /**
     * Display the weekly menu for students.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get the week start date from request or use current week
        $weekStart = $request->input('week_start') 
            ? Carbon::parse($request->input('week_start'))->startOfWeek() 
            : Carbon::now()->startOfWeek();
        
        $weekEnd = (clone $weekStart)->endOfWeek();
        
        // Get menu items for the selected week
        $menuItems = Menu::whereBetween('date', [$weekStart, $weekEnd])
            ->orderBy('date')
            ->orderBy('meal_type')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });
        
        // Get the next and previous week dates for navigation
        $nextWeek = (clone $weekStart)->addWeek()->format('Y-m-d');
        $prevWeek = (clone $weekStart)->subWeek()->format('Y-m-d');
        
        // Get today's menu for highlighting
        $today = Carbon::today()->format('Y-m-d');
        
        // Create an array of dates for the week
        $weekDates = [];
        $currentDate = clone $weekStart;
        
        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->format('l'),
                'formatted' => $currentDate->format('M d'),
                'is_today' => $currentDate->format('Y-m-d') === $today
            ];
            $currentDate->addDay();
        }
        
        return view('student.menu', compact(
            'menuItems',
            'weekDates',
            'weekStart',
            'weekEnd',
            'nextWeek',
            'prevWeek',
            'today'
        ));
    }
}