<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // [
        //     {
        //         name: 'Main Course',
        //         list: [
        //             'Fried Chicken',
        //             'Chicken Adobo',
        //             'Chicken Curry',
        //             'Tinolang Manok',
        //             'Pork Adobo',
        //             'Prok Sinigang',
        //             'Pork Menudo',
        //             'Shrimp Sinigang',
        //             'Garlic Shrimp',
        //             'Letchon Kawali',
        //             'Lumpiang Shanghai',
        //             'Sisig',
        //             'Chopsuey',
        //         ],
        //     },
        //     {
        //         name: 'All-Day Meryenda',
        //         list: [
        //             'Pansit Palabok',
        //             ' Special Batchoy',
        //             'Bihon Batchoy',
        //             'Sotanghon Batchoy',
        //             'Burger',
        //             'Fries',
        //             'Spaghetti',
        //             'Carbonara',
        //             'Cheese Sticks',
        //         ],
        //     },
        //     { name: 'All-Day Breakfeat', list: ['Tapsilog', 'Hotsilog', 'Cornsilog', 'Longsilog', 'Spamsilog', 'Nugetsilog', 'Shans Silog'] },
        //     {
        //         name: 'Combo Meryenda',
        //         list: [
        //             'Burger w/ Fries',
        //             'Hunggarian Sausage w/ Fries',
        //             'Angus Beef Burger w/ Fries',
        //             'Fish and Fries',
        //             'Happydog w/ Fries',
        //             'Spaghetti w/ Fries',
        //             'Carbonara w/ Hash Brown',
        //         ],
        //     },
        //     {
        //         name: 'Solo Meal Combo',
        //         list: ['Dinuguan Rice', 'Adobo Chicken Rice', 'Chicken Curry Rice', 'Menudo Rice', 'Bopis Rice', 'Bicol Express Rice'],
        //     },
        //     {
        //         name: 'Short Order',
        //         list: ['Sotanghon Guisado', 'Canton Special', 'Bihon Special', 'Bam-e', 'Lomi', 'Pansit Palabok'],
        //     },
        //     { name: 'Rice', list: ['Platter Plain Rice', 'Platter Garlic Rice', 'Plain Rice Cup', 'Garlic Rice Cup'] },
        //     { name: 'Coffee', list: ['Regular Black Coffee', 'Double Latte', 'Strawberry Latte', 'Caramel Latte', 'Mocha Latte'] },
        //     {
        //         name: 'Shakes',
        //         list: [
        //             'Dragon Fruit',
        //             'Mango',
        //             'Banana',
        //             'Banana w/ Peanut',
        //             'Buko',
        //             'Buko w/ Peanut',
        //             'Avocado',
        //             'Vanilla',
        //             'Chocolate',
        //             'Strawberry',
        //             'Papaya',
        //         ],
        //     },
        //     { name: 'Frappe', list: ['Chocolate', 'Strawberry', 'Vanilla', 'Red Velvet', 'Cookies n Cream', 'Mango Cheesecake'] },
        //     {
        //         name: 'Milktea',
        //         list: [
        //             'Taro',
        //             'Caramel Sugar',
        //             'Cookies n Cream',
        //             'Wintermelon',
        //             'Red Velvet',
        //             'Black Forest',
        //             'Dark Chocolate',
        //             'Okinawa',
        //             'Matcha',
        //             'Hazelnut',
        //             'Mango Cheesecake',
        //             'Hokaido',
        //         ],
        //     },
        //     {
        //         name: 'Beverages',
        //         list: [
        //             'Ice Tea',
        //             'Pineapple Juice',
        //             'Blue Lemonade',
        //             'Pink Lemonade',
        //             'Coke',
        //             'Coke Float',
        //             'Pitcher Juice',
        //             'Jar Juice',
        //             '',
        //             'Bucket Beer Mix',
        //             'San Mig Light',
        //             'Red Horse',
        //             'Palesin',
        //         ],
        //     },
        //     { name: 'All-Day Unlimited', list: ['Friend Chicken (Unli Rice / Drinks)'] },
        // ];
        // convert this array to menu categories with menu

        MenuCategory::create([
            'name' => 'Main Course',
            'description' => 'Main course menu',
        ])->menus()->createMany([
            [
                'name' => 'Fried Chicken',
                'description' => 'Fried chicken menu',
            ],
            [
                'name' => 'Chicken Adobo',
                'description' => 'Chicken adobo menu',
            ],
            [
                'name' => 'Chicken Curry',
                'description' => 'Chicken curry menu',
            ],
            [
                'name' => 'Tinolang Manok',
                'description' => 'Tinolang manok menu',
            ],
            [
                'name' => 'Pork Adobo',
                'description' => 'Pork adobo menu',
            ],
            [
                'name' => 'Prok Sinigang',
                'description' => 'Prok sinigang menu',
            ],
            [
                'name' => 'Pork Menudo',
                'description' => 'Pork menudo menu',
            ],
            [
                'name' => 'Shrimp Sinigang',
                'description' => 'Shrimp sinigang menu',
            ],
            [
                'name' => 'Garlic Shrimp',
                'description' => 'Garlic shrimp menu',
            ],
            [
                'name' => 'Letchon Kawali',
                'description' => 'Letchon kawali menu',
            ],
            [
                'name' => 'Lumpiang Shanghai',
                'description' => 'Lumpiang shanghai menu',
            ],
            [
                'name' => 'Sisig',
                'description' => 'Sisig menu',
            ],
            [
                'name' => 'Chopsuey',
                'description' => 'Chopsuey menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'All-Day Meryenda',
            'description' => 'All-day merienda menu',
        ])->menus()->createMany([
            [
                'name' => 'Pansit Palabok',
                'description' => 'Pansit palabok menu',
            ],
            [
                'name' => 'Special Batchoy',
                'description' => 'Special batchoy menu',
            ],
            [
                'name' => 'Bihon Batchoy',
                'description' => 'Bihon batchoy menu',
            ],
            [
                'name' => 'Sotanghon Batchoy',
                'description' => 'Sotanghon batchoy menu',
            ],
            [
                'name' => 'Burger',
                'description' => 'Burger menu',
            ],
            [
                'name' => 'Fries',
                'description' => 'Fries menu',
            ],
            [
                'name' => 'Spaghetti',
                'description' => 'Spaghetti menu',
            ],

            [
                'name' => 'Carbonara',
                'description' => 'Carbonara menu',
            ],
            [
                'name' => 'Cheese Sticks',
                'description' => 'Cheese sticks menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'All-Day Breakfeat',
            'description' => 'All-day breakfeat menu',
        ])->menus()->createMany([
            [
                'name' => 'Tapsilog',
                'description' => 'Tapsilog menu',
            ],
            [
                'name' => 'Hotsilog',
                'description' => 'Hotsilog menu',
            ],
            [
                'name' => 'Cornsilog',
                'description' => 'Cornsilog menu',
            ],
            [
                'name' => 'Longsilog',
                'description' => 'Longsilog menu',
            ],
            [
                'name' => 'Spamsilog',
                'description' => 'Spamsilog menu',
            ],
            [
                'name' => 'Nugetsilog',
                'description' => 'Nugetsilog menu',
            ],
            [
                'name' => 'Shans Silog',
                'description' => 'Shans silog menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Combo Meryenda',
            'description' => 'Combo merienda menu',
        ])->menus()->createMany([
            [
                'name' => 'Burger w/ Fries',
                'description' => 'Burger w/ fries menu',
            ],
            [
                'name' => 'Hunggarian Sausage w/ Fries',
                'description' => 'Hunggarian sausage w/ fries menu',
            ],
            [
                'name' => 'Angus Beef Burger w/ Fries',
                'description' => 'Angus beef burger menu',
            ],
            [
                'name' => 'Fish and Fries',
                'description' => 'Fish and fries menu',
            ],
            [
                'name' => 'Happydog w/ Fries',
                'description' => 'Happydog w/ fries menu',
            ],
            [
                'name' => 'Spaghetti w/ Fries',
                'description' => 'Spaghetti w/ fries menu',
            ],
            [
                'name' => 'Carbonara w/ Hash Brown',
                'description' => 'Carbonara w/ hash brown menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Solo Meal Combo',
            'description' => 'Solo meal combo menu',
        ])->menus()->createMany([
            [
                'name' => 'Dinuguan Rice',
                'description' => 'Dinuguan rice menu',
            ],
            [
                'name' => 'Adobo Chicken Rice',
                'description' => 'Adobo chicken rice menu',
            ],
            [
                'name' => 'Chicken Curry Rice',
                'description' => 'Chicken curry rice menu',
            ],
            [
                'name' => 'Menudo Rice',
                'description' => 'Menudo rice menu',
            ],
            [
                'name' => 'Bopis Rice',
                'description' => 'Bopis rice menu',
            ],
            [
                'name' => 'Bicol Express Rice',
                'description' => 'Bicol express rice menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Short Order',
            'description' => 'Short order menu',
        ])->menus()->createMany([
            [
                'name' => 'Sotanghon Guisado',
                'description' => 'Sotanghon guisado menu',
            ],
            [
                'name' => 'Canton Special',
                'description' => 'Canton special menu',
            ],
            [
                'name' => 'Bihon Special',
                'description' => 'Bihon special menu',
            ],
            [
                'name' => 'Bam-e',
                'description' => 'Bam-e menu',
            ],
            [
                'name' => 'Lomi',
                'description' => 'Lomi menu',
            ],
            [
                'name' => 'Pansit Palabok',
                'description' => 'Pansit palabok menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Rice',
            'description' => 'Rice menu',
        ])->menus()->createMany([
            [
                'name' => 'Platter Plain Rice',
                'description' => 'Platter plain rice menu',
            ],
            [
                'name' => 'Platter Garlic Rice',
                'description' => 'Platter garlic rice menu',
            ],
            [
                'name' => 'Plain Rice Cup',
                'description' => 'Plain rice cup menu',
            ],
            [
                'name' => 'Garlic Rice Cup',
                'description' => 'Garlic rice cup menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Coffee',
            'description' => 'Coffee menu',
        ])->menus()->createMany([
            [
                'name' => 'Regular Black Coffee',
                'description' => 'Regular black coffee menu',
            ],
            [
                'name' => 'Double Latte',
                'description' => 'Double latte menu',
            ],
            [
                'name' => 'Strawberry Latte',
                'description' => 'Strawberry latte menu',
            ],
            [
                'name' => 'Caramel Latte',
                'description' => 'Caramel latte menu',
            ],
            [
                'name' => 'Mocha Latte',
                'description' => 'Mocha latte menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Shakes',
            'description' => 'Shakes menu',
        ])->menus()->createMany([
            [
                'name' => 'Dragon Fruit',
                'description' => 'Dragon fruit menu',
            ],
            [
                'name' => 'Mango',
                'description' => 'Mango menu',
            ],
            [
                'name' => 'Banana',
                'description' => 'Banana menu',
            ],
            [
                'name' => 'Banana w/ Peanut',
                'description' => 'Banana w/ peanut menu',
            ],
            [
                'name' => 'Buko',
                'description' => 'Buko menu',
            ],
            [
                'name' => 'Buko w/ Peanut',
                'description' => 'Buko w/ peanut menu',
            ],
            [
                'name' => 'Avocado',
                'description' => 'Avocado menu',
            ],
            [
                'name' => 'Vanilla',
                'description' => 'Vanilla menu',
            ],
            [
                'name' => 'Chocolate',
                'description' => 'Chocolate menu',
            ],
            [
                'name' => 'Strawberry',
                'description' => 'Strawberry menu',
            ],
            [
                'name' => 'Papaya',
                'description' => 'Papaya menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Frappe',
            'description' => 'Frappe menu',
        ])->menus()->createMany([
            [
                'name' => 'Chocolate',
                'description' => 'Chocolate menu',
            ],
            [
                'name' => 'Strawberry',
                'description' => 'Strawberry menu',
            ],
            [
                'name' => 'Vanilla',
                'description' => 'Vanilla menu',
            ],
            [
                'name' => 'Red Velvet',
                'description' => 'Red velvet menu',
            ],
            [
                'name' => 'Cookies n Cream',
                'description' => 'Cookies n cream menu',
            ],
            [
                'name' => 'Mango Cheesecake',
                'description' => 'Mango cheesecake menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Milktea',
            'description' => 'Milktea menu',
        ])->menus()->createMany([
            [
                'name' => 'Taro',
                'description' => 'Taro menu',
            ],
            [
                'name' => 'Caramel Sugar',
                'description' => 'Caramel sugar menu',
            ],
            [
                'name' => 'Cookies n Cream',
                'description' => 'Cookies n cream menu',
            ],
            [
                'name' => 'Wintermelon',
                'description' => 'Wintermelon menu',
            ],
            [
                'name' => 'Red Velvet',
                'description' => 'Red velvet menu',
            ],
            [
                'name' => 'Black Forest',
                'description' => 'Black forest menu',
            ],
            [
                'name' => 'Dark Chocolate',
                'description' => 'Dark chocolate menu',
            ],
            [
                'name' => 'Okinawa',
                'description' => 'Okinawa menu',
            ],

            [
                'name' => 'Matcha',
                'description' => 'Matcha menu',
            ],
            [
                'name' => 'Hazelnut',
                'description' => 'Hazelnut menu',
            ],
            [
                'name' => 'Mango Cheesecake',
                'description' => 'Mango cheesecake menu',
            ],

            [
                'name' => 'Hokaido',
                'description' => 'Hokaido menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'Beverages',
            'description' => 'Beverages menu',
        ])->menus()->createMany([
            [
                'name' => 'Ice Tea',
                'description' => 'Ice tea menu',
            ],
            [
                'name' => 'Pineapple Juice',
                'description' => 'Pineapple juice menu',
            ],
            [
                'name' => 'Blue Lemonade',
                'description' => 'Blue lemonade menu',
            ],
            [
                'name' => 'Pink Lemonade',
                'description' => 'Pink lemonade menu',
            ],
            [
                'name' => 'Coke',
                'description' => 'Coke menu',
            ],
            [
                'name' => 'Coke Float',
                'description' => 'Coke float menu',
            ],
            [
                'name' => 'Pitcher Juice',
                'description' => 'Pitcher juice menu',
            ],
            [
                'name' => 'Jar Juice',
                'description' => 'Jar juice menu',
            ],
            [
                'name' => 'Bucket Beer Mix',
                'description' => 'Bucket beer mix menu',
            ],
            [
                'name' => 'San Mig Light',
                'description' => 'San mig light menu',
            ],
            [
                'name' => 'Red Horse',
                'description' => 'Red horse menu',
            ],

            [
                'name' => 'Palesin',
                'description' => 'Palesin menu',
            ],
        ]);

        MenuCategory::create([
            'name' => 'All-Day Unlimited',
            'description' => 'All-day unlimited menu',
        ])->menus()->createMany([
            [
                'name' => 'Friend Chicken (Unli Rice / Drinks)',
                'description' => 'Friend chicken (unli rice / drinks) menu',
            ],
        ]);
    }
}
