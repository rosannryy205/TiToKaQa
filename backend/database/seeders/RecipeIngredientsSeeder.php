<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeIngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('recipe_ingredients')->insert([
            // Món ăn: Mì Kim Chi Thập Cẩm (food_id = 1)
            ['food_id' => 1, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 1, 'ingredient_id' => 2, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 2 (Bò Mỹ)
            ['food_id' => 1, 'ingredient_id' => 3, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 3 (Tôm)
            ['food_id' => 1, 'ingredient_id' => 4, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 4 (Mực)
            ['food_id' => 1, 'ingredient_id' => 5, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 5 (Chả cá Hàn Quốc)
            ['food_id' => 1, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 1, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 1, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 1, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 1, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Kim Chi Đùi Gà (food_id = 2)
            ['food_id' => 2, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 2, 'ingredient_id' => 11, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 11 (Đùi gà)
            ['food_id' => 2, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 2, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 2, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 2, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 2, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Kim Chi Hải Sản (food_id = 3)
            ['food_id' => 3, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 3, 'ingredient_id' => 3, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 3 (Tôm)
            ['food_id' => 3, 'ingredient_id' => 4, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 4 (Mực)
            ['food_id' => 3, 'ingredient_id' => 5, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 5 (Chả cá Hàn Quốc)
            ['food_id' => 3, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 3, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 3, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 3, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 3, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Kim Chi Bò Mỹ (food_id = 4)
            ['food_id' => 4, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 4, 'ingredient_id' => 2, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 2 (Bò Mỹ)
            ['food_id' => 4, 'ingredient_id' => 12, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 12 (Xúc xích)
            ['food_id' => 4, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 4, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 4, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 4, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 4, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Kim Chi Cá (food_id = 5)
            ['food_id' => 5, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 5, 'ingredient_id' => 13, 'quantity_required' => 80, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 13 (Cá)
            ['food_id' => 5, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 5, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 5, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 5, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 5, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Kim Chi Gogi (food_id = 6)
            ['food_id' => 6, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 6, 'ingredient_id' => 14, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 14 (Heo)
            ['food_id' => 6, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 6, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 6, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 6, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 6, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)
            ['food_id' => 6, 'ingredient_id' => 12, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 12 (Xúc xích)

            // Món ăn: Mì Soyum Thập Cẩm (food_id = 7)
            ['food_id' => 7, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 7, 'ingredient_id' => 2, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 2 (Bò Mỹ)
            ['food_id' => 7, 'ingredient_id' => 3, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 3 (Tôm)
            ['food_id' => 7, 'ingredient_id' => 4, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 4 (Mực)
            ['food_id' => 7, 'ingredient_id' => 5, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 5 (Chả cá Hàn Quốc)
            ['food_id' => 7, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 7, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 7, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 7, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 7, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Soyum Hải Sản (food_id = 8)
            ['food_id' => 8, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 8, 'ingredient_id' => 3, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 3 (Tôm)
            ['food_id' => 8, 'ingredient_id' => 4, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 4 (Mực)
            ['food_id' => 8, 'ingredient_id' => 5, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 5 (Chả cá Hàn Quốc)
            ['food_id' => 8, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 8, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 8, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 8, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 8, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Soyum Đùi Gà (food_id = 9)
            ['food_id' => 9, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 9, 'ingredient_id' => 11, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 11 (Đùi gà)
            ['food_id' => 9, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 9, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 9, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 9, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 9, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Soyum Bò Mỹ (food_id = 10)
            ['food_id' => 10, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 10, 'ingredient_id' => 2, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 2 (Bò Mỹ)
            ['food_id' => 10, 'ingredient_id' => 12, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 12 (Xúc xích)
            ['food_id' => 10, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 10, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 10, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 10, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 10, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Sincay Hải Sản (food_id = 11)
            ['food_id' => 11, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 11, 'ingredient_id' => 3, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 3 (Tôm)
            ['food_id' => 11, 'ingredient_id' => 4, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 4 (Mực)
            ['food_id' => 11, 'ingredient_id' => 5, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 5 (Chả cá Hàn Quốc)
            ['food_id' => 11, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 11, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 11, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 11, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 11, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Sincay Đùi Gà (food_id = 12)
            ['food_id' => 12, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 12, 'ingredient_id' => 11, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 11 (Đùi gà)
            ['food_id' => 12, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 12, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 12, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 12, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 12, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Sincay Bò Mỹ (food_id = 13)
            ['food_id' => 13, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 13, 'ingredient_id' => 2, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 2 (Bò Mỹ)
            ['food_id' => 13, 'ingredient_id' => 12, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 12 (Xúc xích)
            ['food_id' => 13, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 13, 'ingredient_id' => 7, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 13, 'ingredient_id' => 8, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 13, 'ingredient_id' => 9, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 13, 'ingredient_id' => 10, 'quantity_required' => 20, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Mì Trộn Tương Đen Bò Mỹ (food_id = 14)
            ['food_id' => 14, 'ingredient_id' => 1, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 14, 'ingredient_id' => 2, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 2 (Bò Mỹ)
            ['food_id' => 14, 'ingredient_id' => 6, 'quantity_required' => 30, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 14, 'ingredient_id' => 15, 'quantity_required' => 0.5, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 15 (Hành tây)
            ['food_id' => 14, 'ingredient_id' => 16, 'quantity_required' => 0.5, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 16 (Ớt chuông)
            ['food_id' => 14, 'ingredient_id' => 17, 'quantity_required' => 0.5, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 17 (Cà rốt)
            ['food_id' => 14, 'ingredient_id' => 18, 'quantity_required' => 0.5, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 18 (Hành baro)

            // Món ăn: Lẩu Kim Chi Bò Mỹ (2 Người) (food_id = 15)
            ['food_id' => 15, 'ingredient_id' => 28, 'quantity_required' => 0.8, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 28 (Nước lẩu kim chi)
            ['food_id' => 15, 'ingredient_id' => 1, 'quantity_required' => 2, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 15, 'ingredient_id' => 29, 'quantity_required' => 200, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 29 (Thịt bò Mỹ)
            ['food_id' => 15, 'ingredient_id' => 30, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 30 (Bò viên)
            ['food_id' => 15, 'ingredient_id' => 6, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 15, 'ingredient_id' => 5, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 5 (Chả cá Hàn Quốc)
            ['food_id' => 15, 'ingredient_id' => 31, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 31 (Chả cá sợi)
            ['food_id' => 15, 'ingredient_id' => 7, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)
            ['food_id' => 15, 'ingredient_id' => 9, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)
            ['food_id' => 15, 'ingredient_id' => 8, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 15, 'ingredient_id' => 10, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 10 (Bắp cải tím)

            // Món ăn: Lẩu Tokbokki Bò Mỹ (2 người) (food_id = 16)
            ['food_id' => 16, 'ingredient_id' => 1, 'quantity_required' => 2, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 1 (Mì Chinnoo)
            ['food_id' => 16, 'ingredient_id' => 19, 'quantity_required' => 150, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 19 (Tokbokki)
            ['food_id' => 16, 'ingredient_id' => 2, 'quantity_required' => 150, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 2 (Bò Mỹ)
            ['food_id' => 16, 'ingredient_id' => 14, 'quantity_required' => 150, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 14 (Heo)
            ['food_id' => 16, 'ingredient_id' => 5, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 5 (Chả cá Hàn Quốc)
            ['food_id' => 16, 'ingredient_id' => 6, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 6 (Cá viên)
            ['food_id' => 16, 'ingredient_id' => 12, 'quantity_required' => 2, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 12 (Xúc xích)
            ['food_id' => 16, 'ingredient_id' => 8, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 8 (Cải thìa)
            ['food_id' => 16, 'ingredient_id' => 20, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 20 (Cải thảo)
            ['food_id' => 16, 'ingredient_id' => 21, 'quantity_required' => 0.5, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 21 (Bắp)
            ['food_id' => 16, 'ingredient_id' => 9, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 9 (Nấm)

            // Món ăn: Phô Mai Viên (food_id = 17)
            ['food_id' => 17, 'ingredient_id' => 22, 'quantity_required' => 6, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 22 (Phô mai viên)

            // Món ăn: Nước Gạo Hàn Quốc (food_id = 18)
            ['food_id' => 18, 'ingredient_id' => 23, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 23 (Nước gạo)

            // Món ăn: Kim Chi (food_id = 19)
            ['food_id' => 19, 'ingredient_id' => 7, 'quantity_required' => 100, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 7 (Kim chi)

            // Món ăn: Coca Size L (food_id = 20)
            ['food_id' => 20, 'ingredient_id' => 24, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 24 (Coca)

            // Món ăn: Kimbap Chiên (food_id = 21)
            ['food_id' => 21, 'ingredient_id' => 25, 'quantity_required' => 1, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 25 (Kimbap)

            // Món ăn: Cải Vàng (food_id = 22)
            ['food_id' => 22, 'ingredient_id' => 26, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 26 (Cải Vàng)

            // Món ăn: Rong Biển Xốt (food_id = 23)
            ['food_id' => 23, 'ingredient_id' => 27, 'quantity_required' => 50, 'created_at' => now(), 'updated_at' => now()], // ingredient_id: 27 (Rong Biển)
        ]);
    }
}
