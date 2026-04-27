<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Kategori
        $categoryNames = [
            'Shawarma Sosis', 'Shawarma Ayam', 'Shawarma Sapi',
            'Cheese Kebab Sosis', 'Cheese Kebab Ayam', 'Cheese Kebab Sapi',
            'Kebab Turki Sapi', 'Burger & Snack', 'Extra', 'Minuman',
        ];
        foreach ($categoryNames as $i => $name) {
            Category::create(['name' => $name, 'sort_order' => $i + 1]);
        }

        // Seed Menus
        $menus = [
            // Shawarma BBQ Sosis
            ['name' => 'Shawarma BBQ Sosis (S)', 'category' => 'Shawarma Sosis', 'price' => 7000],
            ['name' => 'Shawarma BBQ Sosis (M)', 'category' => 'Shawarma Sosis', 'price' => 10000],
            ['name' => 'Shawarma BBQ Sosis (L)', 'category' => 'Shawarma Sosis', 'price' => 14000],
            ['name' => 'Shawarma BBQ Sosis (Long)', 'category' => 'Shawarma Sosis', 'price' => 25000],
            
            // Shawarma BBQ Ayam
            ['name' => 'Shawarma BBQ Ayam (S)', 'category' => 'Shawarma Ayam', 'price' => 8000],
            ['name' => 'Shawarma BBQ Ayam (M)', 'category' => 'Shawarma Ayam', 'price' => 12000],
            ['name' => 'Shawarma BBQ Ayam (L)', 'category' => 'Shawarma Ayam', 'price' => 17000],
            ['name' => 'Shawarma BBQ Ayam (Pre)', 'category' => 'Shawarma Ayam', 'price' => 23000],
            ['name' => 'Shawarma BBQ Ayam (Long)', 'category' => 'Shawarma Ayam', 'price' => 28000],

            // Shawarma BBQ Daging Sapi
            ['name' => 'Shawarma BBQ Daging Sapi (S)', 'category' => 'Shawarma Sapi', 'price' => 9000],
            ['name' => 'Shawarma BBQ Daging Sapi (M)', 'category' => 'Shawarma Sapi', 'price' => 15000],
            ['name' => 'Shawarma BBQ Daging Sapi (L)', 'category' => 'Shawarma Sapi', 'price' => 21000],
            ['name' => 'Shawarma BBQ Daging Sapi (Pre)', 'category' => 'Shawarma Sapi', 'price' => 27000],
            ['name' => 'Shawarma BBQ Daging Sapi (Long)', 'category' => 'Shawarma Sapi', 'price' => 33000],

            // Cheese Kebab Sosis
            ['name' => 'Cheese Kebab Sosis (S)', 'category' => 'Cheese Kebab Sosis', 'price' => 8000],
            ['name' => 'Cheese Kebab Sosis (M)', 'category' => 'Cheese Kebab Sosis', 'price' => 11000],
            ['name' => 'Cheese Kebab Sosis (L)', 'category' => 'Cheese Kebab Sosis', 'price' => 15000],
            ['name' => 'Cheese Kebab Sosis (Long)', 'category' => 'Cheese Kebab Sosis', 'price' => 26500],

            // Cheese Kebab Ayam
            ['name' => 'Cheese Kebab Ayam (S)', 'category' => 'Cheese Kebab Ayam', 'price' => 9000],
            ['name' => 'Cheese Kebab Ayam (M)', 'category' => 'Cheese Kebab Ayam', 'price' => 13000],
            ['name' => 'Cheese Kebab Ayam (L)', 'category' => 'Cheese Kebab Ayam', 'price' => 18000],
            ['name' => 'Cheese Kebab Ayam (Long)', 'category' => 'Cheese Kebab Ayam', 'price' => 29500],

            // Cheese Kebab Daging Sapi
            ['name' => 'Cheese Kebab Daging Sapi (S)', 'category' => 'Cheese Kebab Sapi', 'price' => 10000],
            ['name' => 'Cheese Kebab Daging Sapi (M)', 'category' => 'Cheese Kebab Sapi', 'price' => 16000],
            ['name' => 'Cheese Kebab Daging Sapi (L)', 'category' => 'Cheese Kebab Sapi', 'price' => 22000],
            ['name' => 'Cheese Kebab Daging Sapi (Long)', 'category' => 'Cheese Kebab Sapi', 'price' => 34500],

            // Kebab Turki Daging Sapi
            ['name' => 'Kebab Turki Daging Sapi (S)', 'category' => 'Kebab Turki Sapi', 'price' => 9000],
            ['name' => 'Kebab Turki Daging Sapi (M)', 'category' => 'Kebab Turki Sapi', 'price' => 13000],
            ['name' => 'Kebab Turki Daging Sapi (L)', 'category' => 'Kebab Turki Sapi', 'price' => 21000],

            // Burger & Snack
            ['name' => 'Burger Shawarma Daging Sapi (Mini)', 'category' => 'Burger & Snack', 'price' => 6000],
            ['name' => 'Burger Shawarma Daging Sapi (Medium)', 'category' => 'Burger & Snack', 'price' => 13000],
            ['name' => 'Loaded Fries Ayam (BBQ Mayo)', 'category' => 'Burger & Snack', 'price' => 13000],
            ['name' => 'Loaded Fries Ayam (BBQ Cheese)', 'category' => 'Burger & Snack', 'price' => 14000],
            ['name' => 'Tortilla Fries (Tanpa Sosis)', 'category' => 'Burger & Snack', 'price' => 5000],
            ['name' => 'Tortilla Fries (Sosis)', 'category' => 'Burger & Snack', 'price' => 8000],
            ['name' => 'French Fries', 'category' => 'Burger & Snack', 'price' => 9000],

            // Extra Saus & Topping
            ['name' => 'Extra Saus Cheese', 'category' => 'Extra', 'price' => 1000],
            ['name' => 'Extra Topping Toum BBQ', 'category' => 'Extra', 'price' => 2000],
            ['name' => 'Extra Topping Cheese', 'category' => 'Extra', 'price' => 3000],
            ['name' => 'Cup Saus Toum', 'category' => 'Extra', 'price' => 3000],
            ['name' => 'Cup Saus Cheese', 'category' => 'Extra', 'price' => 4000],
            ['name' => 'Cup Saus Tomat/Cabai', 'category' => 'Extra', 'price' => 2000],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }

    }
}
