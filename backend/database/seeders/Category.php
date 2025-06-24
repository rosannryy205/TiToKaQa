<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Category extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
 DB::table('categories')->insert([
            [//1
                'name' => 'Danh mục mặc định',
                'type' => 'food',
                'parent_id' => null,
                'images' => null,
                'default' => true
            ],
            [//2
                'name' => 'Mì cay',
                'type' => 'food',
                'parent_id' => null,
                'images' => 'mycay.png',
                'default' => false
            ],
            [//3
                'name' => 'Mì kim chi',
                'type' => 'food',
                'parent_id' => 2,
                'images' => null,
                'default' => false
            ],
            [//4
                'name' => 'Mì lẩu thái',
                'type' => 'food',
                'parent_id' => 2,
                'images' => null,
                'default' => false
            ],
            [//5
                'name' => 'Mì Soyum',
                'type' => 'food',
                'parent_id' => 2,
                'images' => null,
                'default' => false
            ],
            [//6
                'name' => 'Mì Sincay',
                'type' => 'food',
                'parent_id' => 2,
                'images' => null,
                'default' => false
            ],
            [//7
                'name' => 'Mì tương đen',
                'type' => 'food',
                'parent_id' => null,
                'images' => 'mytuongden.png',
                'default' => false
            ],
            [//8
                'name' => 'Lẩu',
                'type' => 'food',
                'parent_id' => null,
                'images' => 'lauhanquoc.png',
                'default' => false
            ],
            [//9
                'name' => 'Lẩu Hàn Quốc',
                'type' => 'food',
                'parent_id' => 8,
                'images' => null,
                'default' => false
            ],
            [//10
                'name' => 'Lẩu Tokbokki',
                'type' => 'food',
                'parent_id' => 8,
                'images' => 'lautok.png',
                'default' => false
            ],
            [//11
                'name' => 'Khai vị',
                'type' => 'food',
                'parent_id' => null,
                'images' => 'khaivi.png',
                'default' => false
            ],
            [//12
                'name' => 'Giải khát',
                'type' => 'food',
                'parent_id' => null,
                'images' => 'giaikhat.png',
                'default' => false
            ],
            [//13
                'name' => 'Panchan',
                'type' => 'food',
                'parent_id' => null,
                'images' => 'panchan.png',
                'default' => false
            ],
            [//14
                'name' => 'Combo Ưu Đãi',
                'type' => 'food',
                'parent_id' => null,
                'images' => 'cb1.png',
                'default' => false
            ],
            [//15
                'name' => 'Cấp độ',
                'type' => 'topping',
                'parent_id' => null,
                'images' => null,
                'default' => false
            ],
            [//16
                'name' => 'Topping',
                'type' => 'topping',
                'parent_id' => null,
                'images' => null,
                'default' => false
            ]
        ]);

    }
}
