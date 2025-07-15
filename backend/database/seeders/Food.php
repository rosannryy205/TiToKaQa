<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Food extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('foods')->insert([
            [
                'name' => 'Mì Kim Chi Thập Cẩm', // id = 1
                'price' => '69000',
                'image' => 'mykimchithapcam.png',
                'description' => 'Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Đùi Gà', // id = 2
                'price' => '55000',
                'image' => 'mykimchiduiga.png',
                'description' => 'Mì Chinnoo, đùi gà, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Hải Sản', // id = 3
                'price' => '62000',
                'image' => 'mykimchihaisan.png',
                'description' => 'Mì Chinnoo, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Bò Mỹ', // id = 4
                'price' => '59000',
                'image' => 'mykimchibomy.png',
                'description' => 'Mì Chinnoo, bò Mỹ, xúc xích, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Cá', // id = 5
                'price' => '49000',
                'image' => 'mykimchica.png',
                'description' => 'Mì Chinnoo, cá, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Gogi', // id = 6
                'price' => '49000',
                'image' => 'mykimchigogi.png',
                'description' => 'Mì Chinnoo, heo, cá viên, kim chi, cải thìa, nấm, bắp cải tím, xúc xích.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Soyum Thập Cẩm', // id = 7
                'price' => '69000',
                'image' => 'mysoyumthapcam.png',
                'description' => 'Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Soyum Hải Sản', // id = 8
                'price' => '62000',
                'image' => 'mysoyumhaisan.png',
                'description' => 'Mì Chinnoo, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Soyum Đùi Gà', // id = 9
                'price' => '59000',
                'image' => 'mysoyumduiga.png',
                'description' => 'Mì Chinnoo, đùi gà, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Soyum Bò Mỹ', // id = 10
                'price' => '59000',
                'image' => 'mysoyumbomy.png',
                'description' => 'Mì Chinnoo, bò Mỹ, xúc xích, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Sincay Hải Sản', // id = 11
                'price' => '62000',
                'image' => 'mysincayhaisan.png',
                'description' => 'Mì Chinnoo, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 6
            ],
            [
                'name' => 'Mì Sincay Đùi Gà', // id = 12
                'price' => '59000',
                'image' => 'mysincayduiga.png',
                'description' => 'Mì Chinnoo, đùi gà, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 6
            ],
            [
                'name' => 'Mì Sincay Bò Mỹ', // id = 13
                'price' => '59000',
                'image' => 'mysincaybomy.png',
                'description' => 'Mì Chinnoo, bò Mỹ, xúc xích, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 6
            ],
            [
                'name' => 'Mì Trộn Tương Đen Bò Mỹ', // id = 14
                'price' => '65000',
                'image' => 'mitrontuongdenbomy.png',
                'description' => 'Mì Chinnoo, bò, cá viên, hành tây, ớt chuông, cà rốt, hành baro.',
                'category_id' => 7
            ],
            [
                'name' => 'Lẩu Kim Chi Bò Mỹ (2 Người)', // id = 15
                'price' => '199000',
                'image' => 'laukimbobomy.png',
                'description' => 'Nước lẩu kim chi, Mì Chinnoo, thịt bò Mỹ, bò viên, cá viên, chả cá Hàn Quốc, chả cá sợi, kim chi, nấm, cải thìa, bắp cải tím.',
                'category_id' => 9
            ],
            [
                'name' => 'Lẩu Tokbokki Bò Mỹ (2 người)', // id = 16
                'price' => '199000',
                'image' => 'lautokbokkibomy.png',
                'description' => 'Mì Chinnoo, tokbokki, bò Mỹ, heo, chả cá Hàn Quốc, cá viên, xúc xích, cải thìa, cải thảo, bắp, nấm.',
                'category_id' => 10
            ],
            [
                'name' => 'Phô Mai Viên', // id = 17
                'price' => '29000',
                'image' => 'phomaivien.png',
                'description' => 'Phô mai viên chiên giòn, thơm béo.',
                'category_id' => 11
            ],
            [
                'name' => 'Nước Gạo Hàn Quốc', // id = 18
                'price' => '35000',
                'image' => 'nuocgaohanquoc.png',
                'description' => 'Nước gạo ngọt dịu, thơm ngon, giải khát.',
                'category_id' => 12
            ],
            [
                'name' => 'Kim Chi', // id = 19
                'price' => '9000',
                'image' => 'kimchi.png',
                'description' => 'Kim chi Hàn Quốc cay nồng, giòn ngon, ăn kèm hoàn hảo.',
                'category_id' => 13
            ],
            [
                'name' => 'Coca Size L', // id = 20
                'price' => '27000',
                'image' => 'coca.png',
                'description' => 'Coca cola size L',
                'category_id' => 12
            ],
            [
                'name' => 'Kimbap Chiên', // id = 21
                'price' => '45000',
                'image' => 'kimbapchien.png',
                'description' => 'Kimbap Chiên Giòn',
                'category_id' => 11
            ],
            [
                'name' => 'Cải Vàng', // id = 22
                'price' => '9000',
                'image' => 'caivang.png',
                'description' => '단무지',
                'category_id' => 13
            ],
            [
                'name' => 'Rong Biển Xốt', // id = 23
                'price' => '9000',
                'image' => 'rongbienxot.png',
                'description' => '미역나물 무침',
                'category_id' => 13
            ]
        ]);


    }
}
