# Database Cleanup Summary

## Current Database Status
Your database currently has **32 tables**, many of which are duplicates or unused.

## Tables to KEEP (Essential - 16 tables):

### Core System (4 tables)
- `users` - User authentication and roles
- `sessions` - User sessions  
- `password_reset_tokens` - Password reset functionality
- `migrations` - Laravel migration tracking

### Menu & Meal System (5 tables)
- `meals` - Cook-created meals
- `menus` - Menu items from cook interface
- `daily_menu_updates` - Real-time menu updates
- `kitchen_menu_polls` - Kitchen polling system
- `kitchen_poll_responses` - Student poll responses

### Feedback & Communication (2 tables)
- `feedback` - Student feedback system
- `notifications` - System notifications

### Inventory Management (4 tables)
- `inventory` - Current inventory items
- `inventory_checks` - Inventory check reports
- `inventory_check_items` - Individual check items
- `inventory_history` - Inventory change tracking
- `post_assessments` - Kitchen reports with images

### Student Features (2 tables)
- `pre_orders` - Student meal pre-orders
- `announcements` - System announcements

## Tables to REMOVE (Unused/Duplicate - 16 tables):

### Duplicate Tables
- `meal_polls` → Replaced by `kitchen_menu_polls`
- `meal_poll_responses` → Replaced by `kitchen_poll_responses`
- `polls` → Replaced by `kitchen_menu_polls`
- `poll_responses` → Replaced by `kitchen_poll_responses`
- `weekly_menus` → Replaced by `daily_menu_updates`
- `menu_items` → Redundant with `menus`
- `orders` → Replaced by `pre_orders`
- `order_items` → Replaced by `pre_orders`

### Unused Features
- `meal_statuses` - Not used in current system
- `ingredients` - Not actively implemented
- `suppliers` - Not implemented
- `system_logs` - Not actively used
- `admin_settings` - Not implemented
- `reports` - Replaced by specific report tables

## How to Clean Up

1. **Backup your database first!**
   ```bash
   mysqldump -u username -p capstone_db > backup_before_cleanup.sql
   ```

2. **Run the cleanup migrations:**
   ```bash
   php artisan migrate
   ```

3. **Verify the cleanup:**
   - Check that essential features still work
   - Verify that only 16 tables remain
   - Test the updated feedback system

## Benefits After Cleanup:
- **Reduced complexity** - Easier to understand database structure
- **Better performance** - Fewer tables to query
- **Cleaner codebase** - Remove unused model files
- **Easier maintenance** - Clear purpose for each table

## Next Steps:
1. Run the cleanup migrations
2. Test the updated student feedback system
3. Remove unused model files from `app/Models/`
4. Update any remaining references to deleted tables
