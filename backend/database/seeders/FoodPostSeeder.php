<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('food_posts')->insert([
            [
                'food_id' => 1,
                'title' => 'Mì Kim Chi Thập Cẩm – Sự kết hợp tuyệt vời',
                'content' => 'Mì Kim Chi Thập Cẩm là món ăn được yêu thích bởi sự kết hợp hoàn hảo giữa bò Mỹ, hải sản tươi sống và kim chi đậm đà. Món ăn không chỉ hấp dẫn bởi màu sắc mà còn chinh phục vị giác người thưởng thức.',
                'image' => 'uploads/food_posts/food_1.jpg',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'food_id' => 2,
                'title' => 'Mì Kim Chi Đùi Gà – Vị cay nhẹ đầy quyến rũ',
                'content' => 'Sự kết hợp giữa đùi gà mềm ngọt và vị cay nhẹ của kim chi tạo nên một món ăn khó cưỡng. Phù hợp cho mọi độ tuổi, đặc biệt vào những ngày se lạnh.',
                'image' => 'uploads/food_posts/food_2.jpg',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'food_id' => 3,
                'title' => 'Mì Kim Chi Hải Sản – Đậm đà vị biển',
                'content' => 'Mì Kim Chi Hải Sản mang đến vị ngọt thanh từ tôm, mực kết hợp cùng vị chua cay nhẹ của kim chi, tạo cảm giác vừa lạ vừa quen cho thực khách.',
                'image' => 'uploads/food_posts/food_3.jpg',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'food_id' => 4,
                'title' => 'Mì Kim Chi Bò Mỹ – Món ngon dành cho tín đồ thịt',
                'content' => 'Bò Mỹ mềm thơm hòa quyện với nước dùng mì kim chi mang lại cảm giác tròn vị và cực kỳ hấp dẫn. Đây là lựa chọn tuyệt vời cho người mê thịt bò.',
                'image' => 'uploads/food_posts/food_4.jpg',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'food_id' => 5,
                'title' => 'Mì Kim Chi Cá – Lựa chọn mới mẻ',
                'content' => 'Món mì với vị ngọt thanh của cá, hòa quyện trong nước dùng kim chi cay nhẹ, tạo nên sự cân bằng hoàn hảo giữa dinh dưỡng và hương vị.',
                'image' => 'uploads/food_posts/food_5.jpg',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
