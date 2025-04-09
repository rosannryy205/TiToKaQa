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
                'parent_id' => null,
                'default' => true
            ],
            [//2
                'name' => 'Mì cay',
                'parent_id' => null,
                'default' => false
            ],
            [//3
                'name' => 'Mì kim chi',
                'parent_id' => 2,
                'default' => false
            ],
            [//4
                'name' => 'Mì lẩu thái',
                'parent_id' => 2,
                'default' => false
            ],
            [//5
                'name' => 'Mì Soyum',
                'parent_id' => 2,
                'default' => false
            ],
            [//6
                'name' => 'Mì Sincay',
                'parent_id' => 2,
                'default' => false
            ],
            [//7
                'name' => 'Mì tương đen',
                'parent_id' => null,
                'default' => false
            ],
            [//8
                'name' => 'Lẩu',
                'parent_id' => null,
                'default' => false
            ],
            [//9
                'name' => 'Lẩu Hàn Quốc',
                'parent_id' => 8,
                'default' => false
            ],
            [//10
                'name' => 'Lẩu Tokbokki',
                'parent_id' => 8,
                'default' => false
            ],
            [//11
                'name' => 'Khai vị',
                'parent_id' => null,
                'default' => false
            ],
            [//12
                'name' => 'Giải khát',
                'parent_id' => null,
                'default' => false
            ],
            [//13
                'name' => 'Panchan',
                'parent_id' => null,
                'default' => false
            ],
            [//14
                'name' => 'Combo Ưu Đãi',
                'parent_id' => null,
                'default' => false
            ],
        ]);

    }
}
