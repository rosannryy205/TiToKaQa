<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FoodPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $now = Carbon::now();

        // DB::table('food_posts')->insert([
        //     [
        //         'user_id'      => 1,
        //         'category'     => 'Tin tức',
        //         'title'        => 'Khai trương nhà hàng TiToKaQa',
        //         'content'      => 'Chào mừng quý khách đến với TiToKaQa. Hãy đến trải nghiệm những món ăn hấp dẫn cùng nhiều ưu đãi khai trương.',
        //         'image'        => 'anh1.jpg',
        //         'is_hidden'    => 0,
        //         'published_at' => $now,
        //         'created_at'   => $now,
        //         'updated_at'   => $now,
        //         'deleted_at'   => null,
        //     ],
        //     [
        //         'user_id'      => 1,
        //         'category'     => 'Khuyến mãi',
        //         'title'        => 'Giảm giá 20% tất cả món ăn cuối tuần',
        //         'content'      => 'Chương trình khuyến mãi đặc biệt: Giảm ngay 20% cho toàn bộ menu vào thứ 7 và chủ nhật hàng tuần.',
        //         'image'        => 'anh2.jpg',
        //         'is_hidden'    => 0,
        //         'published_at' => $now,
        //         'created_at'   => $now,
        //         'updated_at'   => $now,
        //         'deleted_at'   => null,
        //     ],
        //     [
        //         'user_id'      => 1,
        //         'category'     => 'Món mới',
        //         'title'        => 'Ra mắt món Lẩu Thái siêu cay',
        //         'content'      => 'Thưởng thức hương vị chua cay đặc trưng với món Lẩu Thái mới ra mắt tại TiToKaQa.',
        //         'image'        => 'anh3.jpg',
        //         'is_hidden'    => 0,
        //         'published_at' => $now,
        //         'created_at'   => $now,
        //         'updated_at'   => $now,
        //         'deleted_at'   => null,
        //     ],
        //     [
        //         'user_id'      => 1,
        //         'category'     => 'Sự kiện',
        //         'title'        => 'Cuộc thi Ăn nhanh - Nhận quà liền tay',
        //         'content'      => 'Tham gia cuộc thi ăn nhanh tại nhà hàng để nhận nhiều phần quà hấp dẫn. Đăng ký ngay hôm nay!',
        //         'image'        => 'anh4.jpg',
        //         'is_hidden'    => 0,
        //         'published_at' => $now,
        //         'created_at'   => $now,
        //         'updated_at'   => $now,
        //         'deleted_at'   => null,
        //     ],
        // ]);
    }
}
