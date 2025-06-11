<?php

namespace App\Services;

use Carbon\Carbon;

class WeekCycleService
{
    /**
     * Get the current week cycle based on the week of the month
     * 
     * Week 1 of month = Week Cycle 1
     * Week 2 of month = Week Cycle 2  
     * Week 3 of month = Week Cycle 1 (repeats)
     * Week 4 of month = Week Cycle 2 (repeats)
     * Week 5 of month = Week Cycle 1 (if exists)
     * 
     * @param Carbon|null $date Optional date, defaults to now()
     * @return int Week cycle (1 or 2)
     */
    public static function getCurrentWeekCycle($date = null)
    {
        $date = $date ?: now();
        $weekOfMonth = $date->weekOfMonth;
        
        // Odd weeks (1, 3, 5) = Cycle 1
        // Even weeks (2, 4) = Cycle 2
        return ($weekOfMonth % 2 === 1) ? 1 : 2;
    }
    
    /**
     * Get the current week of month
     * 
     * @param Carbon|null $date Optional date, defaults to now()
     * @return int Week of month (1-5)
     */
    public static function getCurrentWeekOfMonth($date = null)
    {
        $date = $date ?: now();
        return $date->weekOfMonth;
    }
    
    /**
     * Get week cycle information for display
     *
     * @param Carbon|null $date Optional date, defaults to now()
     * @return array Array with week info
     */
    public static function getWeekInfo($date = null)
    {
        $date = $date ?: now();
        $weekOfMonth = self::getCurrentWeekOfMonth($date);
        $weekCycle = self::getCurrentWeekCycle($date);

        return [
            'week_of_month' => $weekOfMonth,
            'week_cycle' => $weekCycle,
            'cycle_description' => $weekCycle === 1 ? 'Week 1 & 3' : 'Week 2 & 4',
            'cycle_short' => "Week {$weekCycle}",
            'week_name' => "Week {$weekOfMonth} of " . $date->format('F'),
            'current_day' => strtolower($date->format('l')),
            'current_day_name' => $date->format('l'),
            'formatted_date' => $date->format('Y-m-d'),
            'display_date' => $date->format('l, F j, Y'),
            'is_current_week' => true, // Always true for current date
            'month_name' => $date->format('F'),
            'year' => $date->format('Y')
        ];
    }
    
    /**
     * JavaScript function to calculate week cycle on frontend
     * Returns JavaScript code that can be embedded in views
     * 
     * @return string JavaScript function
     */
    public static function getJavaScriptFunction()
    {
        return "
        /**
         * Calculate current week cycle consistently with backend
         * @param {Date} date Optional date, defaults to now
         * @returns {Object} Week cycle information
         */
        function getCurrentWeekCycle(date = null) {
            const now = date || new Date();

            // Calculate week of month properly
            const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            const firstDayWeekday = firstDayOfMonth.getDay(); // 0 = Sunday, 1 = Monday, etc.
            const weekOfMonth = Math.ceil((now.getDate() + firstDayWeekday) / 7);

            // Odd weeks = 1, Even weeks = 2
            const weekCycle = (weekOfMonth % 2 === 1) ? 1 : 2;

            // Dynamic naming
            const monthName = now.toLocaleDateString('en-US', { month: 'long' });
            const currentDayName = now.toLocaleDateString('en-US', { weekday: 'long' });

            return {
                weekOfMonth: weekOfMonth,
                weekCycle: weekCycle,
                cycleDescription: weekCycle === 1 ? 'Week 1 & 3' : 'Week 2 & 4',
                cycleShort: 'Week ' + weekCycle,
                weekName: 'Week ' + weekOfMonth + ' of ' + monthName,
                currentDay: ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'][now.getDay()],
                currentDayName: currentDayName,
                formattedDate: now.toISOString().split('T')[0],
                displayDate: now.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }),
                timeString: now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                }),
                isCurrentWeek: true,
                monthName: monthName,
                year: now.getFullYear()
            };
        }

        /**
         * UNIFIED: Get highlighting information for menu rows
         * @param {string} day - Day name (monday, tuesday, etc.)
         * @param {number} selectedWeekCycle - Currently selected week cycle
         * @returns {Object} Highlighting information
         */
        function getMenuHighlighting(day, selectedWeekCycle) {
            const weekInfo = getCurrentWeekCycle();
            const today = weekInfo.currentDay;
            const currentWeekCycle = weekInfo.weekCycle;

            const isToday = day === today && selectedWeekCycle === currentWeekCycle;
            const isCurrentWeek = selectedWeekCycle === currentWeekCycle;

            return {
                isToday: isToday,
                isCurrentWeek: isCurrentWeek,
                todayClass: isToday ? 'current-day' : (isCurrentWeek ? 'current-week-row' : ''),
                todayBadge: isToday ? '<span class=\"today-badge\"><i class=\"bi bi-star-fill\"></i> Today</span>' :
                           (isCurrentWeek ? '<span class=\"week-badge\"><i class=\"bi bi-calendar-check\"></i> This Week</span>' : ''),
                dayClass: isToday ? 'fw-bold text-primary' : (isCurrentWeek ? 'fw-bold text-success' : 'fw-bold'),
                weekStatus: isCurrentWeek ? 'Current Week' : 'Viewing Week ' + selectedWeekCycle
            };
        }
        ";
    }
    
    /**
     * Debug current week cycle calculation
     * 
     * @param Carbon|null $date Optional date, defaults to now()
     * @return array Debug information
     */
    public static function debug($date = null)
    {
        $date = $date ?: now();
        $weekInfo = self::getWeekInfo($date);
        
        return [
            'input_date' => $date->toDateTimeString(),
            'carbon_week_of_month' => $date->weekOfMonth,
            'calculated_week_cycle' => $weekInfo['week_cycle'],
            'cycle_description' => $weekInfo['cycle_description'],
            'current_day' => $weekInfo['current_day'],
            'explanation' => "Week {$weekInfo['week_of_month']} of month = Cycle {$weekInfo['week_cycle']} ({$weekInfo['cycle_description']})"
        ];
    }
}
