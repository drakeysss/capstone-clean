<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Send notification to specific users
     */
    public function sendToUsers(array $userIds, string $title, string $message, string $type = 'info', array $data = [])
    {
        foreach ($userIds as $userId) {
            $this->createNotification($userId, $title, $message, $type, $data);
        }
    }

    /**
     * Send notification to users by role
     */
    public function sendToRole(string $role, string $title, string $message, string $type = 'info', array $data = [])
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            $this->createNotification($user->id, $title, $message, $type, $data);
        }
    }

    /**
     * Send notification to all users except sender
     */
    public function sendToAll(string $title, string $message, string $type = 'info', array $data = [])
    {
        $users = User::where('id', '!=', Auth::id())->get();
        foreach ($users as $user) {
            $this->createNotification($user->id, $title, $message, $type, $data);
        }
    }

    /**
     * Create individual notification
     */
    public function createNotification(int $userId, string $title, string $message, string $type, array $data)
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'read_at' => null
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to create notification: ' . $e->getMessage());
        }
    }

    /**
     * Menu Planning Notifications
     */
    public function menuCreated($menuData)
    {
        $this->sendToRole('kitchen',
            'New Menu Available',
            'Cook has created a new menu for ' . $menuData['day'] . '. You can now view and update meal status.',
            'menu_update',
            ['action_url' => '/kitchen/daily-menu', 'menu_data' => $menuData, 'feature' => 'kitchen.daily-menu']
        );

        $this->sendToRole('student',
            'New Menu Available',
            'Today\'s menu has been updated! Check out what\'s available for ' . $menuData['day'] . '.',
            'menu_update',
            ['action_url' => '/student/menu', 'menu_data' => $menuData, 'feature' => 'student.menu']
        );
    }

    public function menuUpdated($menuData)
    {
        $this->sendToRole('kitchen',
            'Menu Updated',
            'Cook has updated the menu for ' . $menuData['day'] . '. Please review the changes.',
            'menu_update',
            ['action_url' => '/kitchen/daily-menu', 'menu_data' => $menuData, 'feature' => 'kitchen.daily-menu']
        );

        $this->sendToRole('student',
            'Menu Updated',
            'The menu for ' . $menuData['day'] . ' has been updated. Check out the changes!',
            'menu_update',
            ['action_url' => '/student/menu', 'menu_data' => $menuData, 'feature' => 'student.menu']
        );
    }

    /**
     * Pre-Order Notifications
     */
    public function pollCreated($pollData)
    {
        $this->sendToRole('student',
            'New Meal Poll Available',
            'Kitchen has created a new meal poll. Please submit your meal preferences.',
            'poll_created',
            ['action_url' => '/student/pre-order', 'poll_data' => $pollData, 'feature' => 'student.pre-order']
        );

        // Note: Cook/admin no longer has access to pre-orders interface
        // Poll creation notifications are only sent to students
    }

    public function pollResponseSubmitted($responseData)
    {
        $this->sendToRole('kitchen',
            'New Poll Response',
            'A student has submitted their meal preferences for the poll.',
            'poll_response',
            ['action_url' => '/kitchen/pre-orders', 'response_data' => $responseData, 'feature' => 'kitchen.pre-orders']
        );

        // Note: Cook/admin no longer has access to pre-orders interface
        // Poll response notifications are only handled by kitchen team
    }

    /**
     * Inventory Notifications
     */
    public function inventoryReportSubmitted($reportData)
    {
        $this->sendToRole('cook',
            'New Inventory Report',
            'Kitchen team has submitted an inventory check report. Please review and approve.',
            'inventory_report',
            ['action_url' => '/cook/inventory/report/' . $reportData['id'], 'report_data' => $reportData, 'feature' => 'cook.inventory']
        );
    }

    public function inventoryReportApproved($reportData)
    {
        $this->sendToUsers([$reportData['submitted_by']],
            'Inventory Report Approved',
            'Your inventory report has been approved by the cook team.',
            'inventory_approved',
            ['action_url' => '/kitchen/inventory', 'report_data' => $reportData, 'feature' => 'kitchen.inventory']
        );
    }

    public function inventoryReportDeleted($reportData)
    {
        $this->sendToUsers([$reportData['submitted_by']],
            'Inventory Report Deleted',
            'Your inventory report has been deleted by the cook/admin team.',
            'inventory_deleted',
            ['action_url' => '/kitchen/inventory', 'report_data' => $reportData, 'feature' => 'kitchen.inventory']
        );
    }

    public function inventoryReportsCleared($reportData)
    {
        $this->sendToUsers([$reportData['submitted_by']],
            'All Inventory Reports Cleared',
            "All inventory reports have been cleared by the cook/admin team. {$reportData['total_reports']} of your reports were removed.",
            'inventory_cleared',
            ['action_url' => '/kitchen/inventory', 'report_data' => $reportData, 'feature' => 'kitchen.inventory']
        );
    }

    public function lowStockAlert($itemData)
    {
        $this->sendToRole('cook',
            'Low Stock Alert',
            $itemData['name'] . ' is running low. Current stock: ' . $itemData['quantity'] . ' ' . $itemData['unit'],
            'low_stock',
            ['action_url' => '/cook/inventory', 'item_data' => $itemData, 'feature' => 'cook.inventory']
        );

        $this->sendToRole('kitchen',
            'Low Stock Alert',
            $itemData['name'] . ' is running low. Please check inventory and report if restock is needed.',
            'low_stock',
            ['action_url' => '/kitchen/inventory', 'item_data' => $itemData, 'feature' => 'kitchen.inventory']
        );
    }

    /**
     * Feedback Notifications
     */
    public function feedbackSubmitted($feedbackData)
    {
        $this->sendToRole('cook',
            'New Student Feedback',
            'A student has submitted feedback about a meal. Rating: ' . $feedbackData['rating'] . '/5',
            'feedback_submitted',
            ['action_url' => '/cook/feedback', 'feedback_data' => $feedbackData, 'feature' => 'cook.feedback']
        );

        $this->sendToRole('kitchen',
            'New Student Feedback',
            'A student has provided feedback about a meal. Check the feedback to improve meal quality.',
            'feedback_submitted',
            ['action_url' => '/kitchen/feedback', 'feedback_data' => $feedbackData, 'feature' => 'kitchen.feedback']
        );
    }

    /**
     * Post-Meal Report Notifications
     */
    public function postMealReportSubmitted($reportData)
    {
        $this->sendToRole('cook',
            'New Post-Meal Report',
            'Kitchen team has submitted a post-meal report with waste assessment data.',
            'post_meal_report',
            ['action_url' => '/cook/post-assessment', 'report_data' => $reportData, 'feature' => 'cook.post-assessment']
        );
    }

    /**
     * General System Notifications
     */
    public function systemUpdate($title, $message)
    {
        $this->sendToAll($title, $message, 'system_update');
    }

    public function deadlineReminder($userRole, $title, $message, $actionUrl = null)
    {
        $data = $actionUrl ? ['action_url' => $actionUrl] : [];
        $this->sendToRole($userRole, $title, $message, 'deadline_reminder', $data);
    }
}
