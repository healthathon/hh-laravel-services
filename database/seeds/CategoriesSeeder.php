<?php

use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoriesName = [
            'Physics',
            'Mental',
            'Nutrition',
            'Lifestyle'
        ];
        foreach ($categoriesName as $value) {
            $category = new \App\Model\Category();
            $category->name = $value;
            $category->save();
        }
    }
}
