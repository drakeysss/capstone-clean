<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Define appropriate units for different item types
$itemUnits = [
    // Vegetables (kg)
    'Ampalaya' => 'kg',
    'Baguio Beans' => 'kg',
    'Cabbage' => 'kg',
    'Carrots' => 'kg',
    'Eggplant' => 'kg',
    'Garlic' => 'kg',
    'Kalabasa' => 'kg',
    'Mixed Vegetables' => 'kg',
    'Onion' => 'kg',
    'Pakbit' => 'kg',
    'Potatoes' => 'kg',
    'String Beans' => 'kg',
    'Tomatoes' => 'kg',
    
    // Meats (kg)
    'Burger Patty' => 'kg',
    'Chicken' => 'kg',
    'Chicken Loaf' => 'kg',
    'Chorizo' => 'kg',
    'Fish' => 'kg',
    'Ground Pork' => 'kg',
    'Ham' => 'kg',
    'Hotdogs' => 'kg',
    'Luncheon Meat' => 'kg',
    'Meatballs' => 'kg',
    'Pork' => 'kg',
    'Porkchop' => 'kg',
    
    // Dry goods
    'Monggo Beans' => 'kg',
    'Odong Noodles' => 'kg',
    'Pepper' => 'kg',
    'Salt' => 'kg',
    'Sotanghon' => 'kg',
    
    // Liquids
    'Oil' => 'liters',
    'Soy Sauce' => 'liters',
    'Vinegar' => 'liters',
    'Water' => 'liters',
    
    // Canned/Packaged
    'Buwad' => 'packs',
    'Energen' => 'sachets',
    'Sardines' => 'cans',
    
    // Count items
    'Eggs' => 'pieces'
];

// Update each inventory item with the appropriate unit
$updated = 0;
foreach ($itemUnits as $itemName => $unit) {
    $count = DB::table('inventory')
        ->where('name', $itemName)
        ->update(['unit' => $unit]);
    
    if ($count > 0) {
        echo "Updated $itemName to use $unit\n";
        $updated += $count;
    }
}

echo "\nTotal items updated: $updated\n";
