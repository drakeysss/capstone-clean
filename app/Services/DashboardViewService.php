<?php

namespace App\Services;

use App\Models\UserDashboardView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class DashboardViewService
{
    /**
     * Filter data to show only unviewed items for the current user
     */
    public static function filterUnviewedData($query, string $dataType, string $identifierColumn = 'id')
    {
        $user = Auth::user();
        if (!$user) {
            return $query;
        }

        // Use the actual user_id from the database, not the email
        $userId = $user->user_id;
        return UserDashboardView::getUnviewedData($query, $userId, $dataType, $identifierColumn);
    }

    /**
     * Mark dashboard data as viewed by current user
     */
    public static function markDataAsViewed(Collection $data, string $dataType, string $identifierColumn = 'id'): void
    {
        $user = Auth::user();
        if (!$user || $data->isEmpty()) {
            return;
        }

        // Use the actual user_id from the database, not the email
        $userId = $user->user_id;
        foreach ($data as $item) {
            $identifier = is_object($item) ? $item->{$identifierColumn} : $item[$identifierColumn];
            UserDashboardView::markAsViewed($userId, $dataType, (string)$identifier);
        }
    }

    /**
     * Get filtered dashboard data and mark as viewed
     */
    public static function getAndMarkDashboardData($query, string $dataType, string $identifierColumn = 'id')
    {
        // Get unviewed data
        $filteredQuery = static::filterUnviewedData($query, $dataType, $identifierColumn);
        $data = $filteredQuery->get();

        // Mark as viewed
        static::markDataAsViewed($data, $dataType, $identifierColumn);

        return $data;
    }

    /**
     * Check if data type should be exempted from "show once" logic
     */
    public static function isExemptedDataType(string $dataType): bool
    {
        $exemptedTypes = [
            'todays_menu_kitchen',
            'todays_menu_student',
            'daily_menu_updates'
        ];

        return in_array($dataType, $exemptedTypes);
    }

    /**
     * Process dashboard data with "show once" logic
     */
    public static function processDashboardData($query, string $dataType, string $identifierColumn = 'id')
    {
        // If data type is exempted, return all data without filtering
        if (static::isExemptedDataType($dataType)) {
            return $query->get();
        }

        // Otherwise, apply "show once" logic
        return static::getAndMarkDashboardData($query, $dataType, $identifierColumn);
    }

    /**
     * Reset user's viewed data for a specific data type (useful for testing or admin functions)
     */
    public static function resetUserViews(string $userId, string $dataType = null): void
    {
        $query = UserDashboardView::where('user_id', $userId);

        if ($dataType) {
            $query->where('data_type', $dataType);
        }

        $query->delete();
    }

    /**
     * Reset all views for current user (useful for testing)
     */
    public static function resetCurrentUserViews(): void
    {
        $user = Auth::user();
        if ($user) {
            // Use the actual user_id from the database, not the email
            static::resetUserViews($user->user_id);
        }
    }
}
