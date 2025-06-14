<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                Category::class,
                Food::class,
                Category_toppings::class,
                Topping::class,
                Food_toppings::class,
                Combo::class,
                Combo_detail::class,
                Discount::class,
                Table::class,
                RolePermission::class
            ]
        );
    }
}
