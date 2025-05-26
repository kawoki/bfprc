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
                'price' => 280,
            ],
            [
                'name' => 'Chicken Adobo',
                'description' => 'Chicken adobo menu',
                'price' => 300,
            ],
            [
                'name' => 'Chicken Curry',
                'description' => 'Chicken curry menu',
                'price' => 350,
            ],
            [
                'name' => 'Tinolang Manok',
                'description' => 'Tinolang manok menu',
                'price' => 400,
            ],
            [
                'name' => 'Pork Adobo',
                'description' => 'Pork adobo menu',
                'price' => 400,
            ],
            [
                'name' => 'Prok Sinigang',
                'description' => 'Prok sinigang menu',
                'price' => 400,
            ],
            [
                'name' => 'Pork Menudo',
                'description' => 'Pork menudo menu',
                'price' => 400,
            ],
            [
                'name' => 'Shrimp Sinigang',
                'description' => 'Shrimp sinigang menu',
                'price' => 450,
            ],
            [
                'name' => 'Garlic Shrimp',
                'description' => 'Garlic shrimp menu',
                'price' => 450,
            ],
            [
                'name' => 'Letchon Kawali',
                'description' => 'Letchon kawali menu',
                'price' => 300,
            ],
            [
                'name' => 'Lumpiang Shanghai',
                'description' => 'Lumpiang shanghai menu',
                'price' => 120,
            ],
            [
                'name' => 'Sisig',
                'description' => 'Sisig menu',
                'price' => 300,
            ],
            [
                'name' => 'Chopsuey',
                'description' => 'Chopsuey menu',
                'price' => 350,
            ],
        ]);

        MenuCategory::create([
            'name' => 'All-Day Meryenda',
            'description' => 'All-day merienda menu',
        ])->menus()->createMany([
            [
                'name' => 'Pansit Palabok',
                'description' => 'Pansit palabok menu',
                'price' => 80,
            ],
            [
                'name' => 'Special Batchoy',
                'description' => 'Special batchoy menu',
                'price' => 60,
            ],
            [
                'name' => 'Bihon Batchoy',
                'description' => 'Bihon batchoy menu',
                'price' => 70,
            ],
            [
                'name' => 'Sotanghon Batchoy',
                'description' => 'Sotanghon batchoy menu',
                'price' => 80,
            ],
            [
                'name' => 'Burger',
                'description' => 'Burger menu',
                'price' => 55,
            ],
            [
                'name' => 'Fries',
                'description' => 'Fries menu',
                'price' => 60,
            ],
            [
                'name' => 'Spaghetti',
                'description' => 'Spaghetti menu',
                'price' => 60,
            ],

            [
                'name' => 'Carbonara',
                'description' => 'Carbonara menu',
                'price' => 90,
            ],
            [
                'name' => 'Cheese Sticks',
                'description' => 'Cheese sticks menu',
                'price' => 110,
            ],
        ]);

        MenuCategory::create([
            'name' => 'All-Day Breakfeat',
            'description' => 'All-day breakfeat menu',
        ])->menus()->createMany([
            [
                'name' => 'Tapsilog',
                'description' => 'Tapsilog menu',
                'price' => 100,
            ],
            [
                'name' => 'Hotsilog',
                'description' => 'Hotsilog menu',
                'price' => 100,
            ],
            [
                'name' => 'Cornsilog',
                'description' => 'Cornsilog menu',
                'price' => 100,
            ],
            [
                'name' => 'Longsilog',
                'description' => 'Longsilog menu',
                'price' => 100,
            ],
            [
                'name' => 'Spamsilog',
                'description' => 'Spamsilog menu',
                'price' => 100,
            ],
            [
                'name' => 'Nugetsilog',
                'description' => 'Nugetsilog menu',
                'price' => 100,
            ],
            [
                'name' => 'Shans Silog',
                'description' => 'Shans silog menu',
                'price' => 100,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Combo Meryenda',
            'description' => 'Combo merienda menu',
        ])->menus()->createMany([
            [
                'name' => 'Burger w/ Fries',
                'description' => 'Burger w/ fries menu',
                'price' => 120,
            ],
            [
                'name' => 'Hunggarian Sausage w/ Fries',
                'description' => 'Hunggarian sausage w/ fries menu',
                'price' => 250,
            ],
            [
                'name' => 'Angus Beef Burger w/ Fries',
                'description' => 'Angus beef burger menu',
                'price' => 250,
            ],
            [
                'name' => 'Fish and Fries',
                'description' => 'Fish and fries menu',
                'price' => 200,
            ],
            [
                'name' => 'Happydog w/ Fries',
                'description' => 'Happydog w/ fries menu',
                'price' => 120,
            ],
            [
                'name' => 'Spaghetti w/ Fries',
                'description' => 'Spaghetti w/ fries menu',
                'price' => 120,
            ],
            [
                'name' => 'Carbonara w/ Hash Brown',
                'description' => 'Carbonara w/ hash brown menu',
                'price' => 120,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Solo Meal Combo',
            'description' => 'Solo meal combo menu',
        ])->menus()->createMany([
            [
                'name' => 'Dinuguan Rice',
                'description' => 'Dinuguan rice menu',
                'price' => 90,
            ],
            [
                'name' => 'Adobo Chicken Rice',
                'description' => 'Adobo chicken rice menu',
                'price' => 90,
            ],
            [
                'name' => 'Chicken Curry Rice',
                'description' => 'Chicken curry rice menu',
                'price' => 90,
            ],
            [
                'name' => 'Menudo Rice',
                'description' => 'Menudo rice menu',
                'price' => 90,
            ],
            [
                'name' => 'Bopis Rice',
                'description' => 'Bopis rice menu',
                'price' => 90,
            ],
            [
                'name' => 'Bicol Express Rice',
                'description' => 'Bicol express rice menu',
                'price' => 90,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Short Order',
            'description' => 'Short order menu',
        ])->menus()->createMany([
            [
                'name' => 'Sotanghon Guisado',
                'description' => 'Sotanghon guisado menu',
                'price' => 300,
            ],
            [
                'name' => 'Canton Special',
                'description' => 'Canton special menu',
                'price' => 300,
            ],
            [
                'name' => 'Bihon Special',
                'description' => 'Bihon special menu',
                'price' => 300,
            ],
            [
                'name' => 'Bam-e',
                'description' => 'Bam-e menu',
                'price' => 300,
            ],
            [
                'name' => 'Lomi',
                'description' => 'Lomi menu',
                'price' => 300,
            ],
            [
                'name' => 'Pansit Palabok',
                'description' => 'Pansit palabok menu',
                'price' => 300,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Rice',
            'description' => 'Rice menu',
        ])->menus()->createMany([
            [
                'name' => 'Platter Plain Rice',
                'description' => 'Platter plain rice menu',
                'price' => 150,
            ],
            [
                'name' => 'Platter Garlic Rice',
                'description' => 'Platter garlic rice menu',
                'price' => 200,
            ],
            [
                'name' => 'Plain Rice Cup',
                'description' => 'Plain rice cup menu',
                'price' => 15,
            ],
            [
                'name' => 'Garlic Rice Cup',
                'description' => 'Garlic rice cup menu',
                'price' => 20,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Coffee',
            'description' => 'Coffee menu',
        ])->menus()->createMany([
            [
                'name' => 'Regular Black Coffee',
                'description' => 'Regular black coffee menu',
                'price' => 25,
            ],
            [
                'name' => 'Double Latte',
                'description' => 'Double latte menu',
                'price' => 70,
            ],
            [
                'name' => 'Strawberry Latte',
                'description' => 'Strawberry latte menu',
                'price' => 70,
            ],
            [
                'name' => 'Caramel Latte',
                'description' => 'Caramel latte menu',
                'price' => 70,
            ],
            [
                'name' => 'Mocha Latte',
                'description' => 'Mocha latte menu',
                'price' => 70,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Shakes',
            'description' => 'Shakes menu',
        ])->menus()->createMany([
            [
                'name' => 'Dragon Fruit',
                'description' => 'Dragon fruit menu',
                'price' => 90,
            ],
            [
                'name' => 'Mango',
                'description' => 'Mango menu',
                'price' => 90,
            ],
            [
                'name' => 'Banana',
                'description' => 'Banana menu',
                'price' => 90,
            ],
            [
                'name' => 'Banana w/ Peanut',
                'description' => 'Banana w/ peanut menu',
                'price' => 95,
            ],
            [
                'name' => 'Buko',
                'description' => 'Buko menu',
                'price' => 90,
            ],
            [
                'name' => 'Buko w/ Peanut',
                'description' => 'Buko w/ peanut menu',
                'price' => 95,
            ],
            [
                'name' => 'Avocado',
                'description' => 'Avocado menu',
                'price' => 90,
            ],
            [
                'name' => 'Vanilla',
                'description' => 'Vanilla menu',
                'price' => 90,
            ],
            [
                'name' => 'Chocolate',
                'description' => 'Chocolate menu',
                'price' => 90,
            ],
            [
                'name' => 'Strawberry',
                'description' => 'Strawberry menu',
                'price' => 90,
            ],
            [
                'name' => 'Papaya',
                'description' => 'Papaya menu',
                'price' => 90,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Frappe',
            'description' => 'Frappe menu',
        ])->menus()->createMany([
            [
                'name' => 'Chocolate',
                'description' => 'Chocolate menu',
                'price' => 120,
            ],
            [
                'name' => 'Strawberry',
                'description' => 'Strawberry menu',
                'price' => 120,
            ],
            [
                'name' => 'Vanilla',
                'description' => 'Vanilla menu',
                'price' => 120,
            ],
            [
                'name' => 'Red Velvet',
                'description' => 'Red velvet menu',
                'price' => 120,
            ],
            [
                'name' => 'Cookies n Cream',
                'description' => 'Cookies n cream menu',
                'price' => 120,
            ],
            [
                'name' => 'Mango Cheesecake',
                'description' => 'Mango cheesecake menu',
                'price' => 120,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Milktea',
            'description' => 'Milktea menu',
        ])->menus()->createMany([
            [
                'name' => 'Taro',
                'description' => 'Taro menu',
                'price' => 80,
            ],
            [
                'name' => 'Caramel Sugar',
                'description' => 'Caramel sugar menu',
                'price' => 80,
            ],
            [
                'name' => 'Cookies n Cream',
                'description' => 'Cookies n cream menu',
                'price' => 80,
            ],
            [
                'name' => 'Wintermelon',
                'description' => 'Wintermelon menu',
                'price' => 80,
            ],
            [
                'name' => 'Red Velvet',
                'description' => 'Red velvet menu',
                'price' => 80,
            ],
            [
                'name' => 'Black Forest',
                'description' => 'Black forest menu',
                'price' => 80,
            ],
            [
                'name' => 'Dark Chocolate',
                'description' => 'Dark chocolate menu',
                'price' => 80,
            ],
            [
                'name' => 'Okinawa',
                'description' => 'Okinawa menu',
                'price' => 80,
            ],

            [
                'name' => 'Matcha',
                'description' => 'Matcha menu',
                'price' => 80,
            ],
            [
                'name' => 'Hazelnut',
                'description' => 'Hazelnut menu',
                'price' => 80,
            ],
            [
                'name' => 'Mango Cheesecake',
                'description' => 'Mango cheesecake menu',
                'price' => 80,
            ],

            [
                'name' => 'Hokaido',
                'description' => 'Hokaido menu',
                'price' => 80,
            ],
        ]);

        MenuCategory::create([
            'name' => 'Beverages',
            'description' => 'Beverages menu',
        ])->menus()->createMany([
            [
                'name' => 'Ice Tea',
                'description' => 'Ice tea menu',
                'price' => 45,
            ],
            [
                'name' => 'Pineapple Juice',
                'description' => 'Pineapple juice menu',
                'price' => 45,
            ],
            [
                'name' => 'Blue Lemonade',
                'description' => 'Blue lemonade menu',
                'price' => 45,
            ],
            [
                'name' => 'Pink Lemonade',
                'description' => 'Pink lemonade menu',
                'price' => 45,
            ],
            [
                'name' => 'Coke',
                'description' => 'Coke menu',
                'price' => 25,
            ],
            [
                'name' => 'Coke Float',
                'description' => 'Coke float menu',
                'price' => 100,
            ],
            [
                'name' => 'Pitcher Juice',
                'description' => 'Pitcher juice menu',
                'price' => 100,
            ],
            [
                'name' => 'Jar Juice',
                'description' => 'Jar juice menu',
                'price' => 300,
            ],
            [
                'name' => 'Bucket Beer Mix',
                'description' => 'Bucket beer mix menu',
                'price' => 400,
            ],
            [
                'name' => 'San Mig Light',
                'description' => 'San mig light menu',
                'price' => 60,
            ],
            [
                'name' => 'Red Horse',
                'description' => 'Red horse menu',
                'price' => 60,
            ],

            [
                'name' => 'Palesin',
                'description' => 'Palesin menu',
                'price' => 60,
            ],
        ]);

        MenuCategory::create([
            'name' => 'All-Day Unlimited',
            'description' => 'All-day unlimited menu',
        ])->menus()->createMany([
            [
                'name' => 'Friend Chicken (Unli Rice / Drinks)',
                'description' => 'Friend chicken (unli rice / drinks) menu',
                'price' => 199,
            ],
        ]);
    }
}
