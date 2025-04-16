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
                'name_ascii' => 'mi kim chi thap cam',
                'price' => '69000',
                'image' => 'mykimchithapcam.webp',
                'description' => 'Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Đùi Gà', // id = 2
                'name_ascii' => 'mi kim chi dui ga',
                'price' => '55000',
                'image' => 'mykimchiduiga.webp',
                'description' => 'Mì Chinnoo, đùi gà, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Hải Sản', // id = 3
                'name_ascii' => 'mi kim chi hai san',
                'price' => '62000',
                'image' => 'mykimchihaisan.webp',
                'description' => 'Mì Chinnoo, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Bò Mỹ', // id = 4
                'name_ascii' => 'mi kim chi bo my',
                'price' => '59000',
                'image' => 'mykimchibomy.webp',
                'description' => 'Mì Chinnoo, bò Mỹ, xúc xích, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Cá', // id = 5
                'name_ascii' => 'mi kim chi ca',
                'price' => '49000',
                'image' => 'mykimchica.webp',
                'description' => 'Mì Chinnoo, cá, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Kim Chi Gogi', // id = 6
                'name_ascii' => 'mi kim chi gogi',
                'price' => '49000',
                'image' => 'mykimchigogi.webp',
                'description' => 'Mì Chinnoo, heo, cá viên, kim chi, cải thìa, nấm, bắp cải tím, xúc xích.',
                'category_id' => 3
            ],
            [
                'name' => 'Mì Soyum Thập Cẩm', // id = 7
                'name_ascii' => 'mi soyum thap cam',
                'price' => '69000',
                'image' => 'mysoyumthapcam.webp',
                'description' => 'Mì Chinnoo, bò Mỹ, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Soyum Hải Sản', // id = 8
                'name_ascii' => 'mi soyum hai san',
                'price' => '62000',
                'image' => 'mysoyumhaisan.webp',
                'description' => 'Mì Chinnoo, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Soyum Đùi Gà', // id = 9
                'name_ascii' => 'mi soyum dui ga',
                'price' => '59000',
                'image' => 'mysoyumduiga.webp',
                'description' => 'Mì Chinnoo, đùi gà, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Soyum Bò Mỹ', // id = 10
                'name_ascii' => 'mi soyum bo my',
                'price' => '59000',
                'image' => 'mysoyumbomy.webp',
                'description' => 'Mì Chinnoo, bò Mỹ, xúc xích, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 5
            ],
            [
                'name' => 'Mì Sincay Hải Sản', // id = 11
                'name_ascii' => 'mi sincay hai san',
                'price' => '62000',
                'image' => 'mysincayhaisan.webp',
                'description' => 'Mì Chinnoo, tôm, mực, chả cá Hàn Quốc, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 6
            ],
            [
                'name' => 'Mì Sincay Đùi Gà', // id = 12
                'name_ascii' => 'mi sincay dui ga',
                'price' => '59000',
                'image' => 'mysincayduiga.webp',
                'description' => 'Mì Chinnoo, đùi gà, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 6
            ],
            [
                'name' => 'Mì Sincay Bò Mỹ', // id = 13
                'name_ascii' => 'mi sincay bo my',
                'price' => '59000',
                'image' => 'mysincaybomy.webp',
                'description' => 'Mì Chinnoo, bò Mỹ, xúc xích, cá viên, kim chi, cải thìa, nấm, bắp cải tím.',
                'category_id' => 6
            ],
            [
                'name' => 'Mì Trộn Tương Đen Bò Mỹ', // id = 14
                'name_ascii' => 'mi tron tuong den bo my',
                'price' => '65000',
                'image' => 'mitrontuongdenbomy.webp',
                'description' => 'Mì Chinnoo, bò, cá viên, hành tây, ớt chuông, cà rốt, hành baro.',
                'category_id' => 7
            ],
            [
                'name' => 'Lẩu Kim Chi Bò Mỹ (2 Người)', // id = 15
                'name_ascii' => 'lau kim chi bo my 2 nguoi',
                'price' => '199000',
                'image' => 'laukimbobomy.webp',
                'description' => 'Nước lẩu kim chi, Mì Chinnoo, thịt bò Mỹ, bò viên, cá viên, chả cá Hàn Quốc, chả cá sợi, kim chi, nấm, cải thìa, bắp cải tím.',
                'category_id' => 9
            ],
            [
                'name' => 'Lẩu Tokbokki Bò Mỹ (2 người)', // id = 16
                'name_ascii' => 'lau tokbokki bo my 2 nguoi',
                'price' => '199000',
                'image' => 'lautokbokkibomy.webp',
                'description' => 'Mì Chinnoo, tokbokki, bò Mỹ, heo, chả cá Hàn Quốc, cá viên, xúc xích, cải thìa, cải thảo, bắp, nấm.',
                'category_id' => 10
            ],
            [
                'name' => 'Phô Mai Viên', // id = 17
                'name_ascii' => 'pho mai vien',
                'price' => '29000',
                'image' => 'phomaivien.webp',
                'description' => 'Phô mai viên chiên giòn, thơm béo.',
                'category_id' => 11
            ],
            [
                'name' => 'Nước Gạo Hàn Quốc', // id = 18
                'name_ascii' => 'nuoc gao han quoc',
                'price' => '35000',
                'image' => 'nuocgaohanquoc.webp',
                'description' => 'Nước gạo ngọt dịu, thơm ngon, giải khát.',
                'category_id' => 12
            ],
            [
                'name' => 'Kim Chi', // id = 19
                'name_ascii' => 'kim chi',
                'price' => '9000',
                'image' => 'kimchi.webp',
                'description' => 'Kim chi Hàn Quốc cay nồng, giòn ngon, ăn kèm hoàn hảo.',
                'category_id' => 13
            ],
            [
                'name' => 'Coca Size L', // id = 20
                'name_ascii' => 'coca size l',
                'price' => '27000',
                'image' => 'coca.webp',
                'description' => 'Coca cola size L',
                'category_id' => 12
            ],
            [
                'name' => 'Kimbap Chiên', // id = 21
                'name_ascii' => 'kimbap chien',
                'price' => '45000',
                'image' => 'kimbapchien.webp',
                'description' => 'Kimbap Chiên Giòn',
                'category_id' => 11
            ],
            [
                'name' => 'Cải Vàng', // id = 22
                'name_ascii' => 'cai vang',
                'price' => '9000',
                'image' => 'caivang.webp',
                'description' => '단무지',
                'category_id' => 13
            ],
            [
                'name' => 'Rong Biển Xốt', // id = 23
                'name_ascii' => 'rong bien xot',
                'price' => '9000',
                'image' => 'rongbienxot.webp',
                'description' => '미역나물 무침',
                'category_id' => 13
            ]
        ]);
        

    }
}
