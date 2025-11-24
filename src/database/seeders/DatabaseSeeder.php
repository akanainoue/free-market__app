<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ConditionsTableSeeder::class,
            ProductsTableSeeder::class,
            CategoriesTableSeeder::class,
            CategoryProductTableSeeder::class,
        ]);

        User::factory(10)->create();
        // 明示的なユーザー作成を優先し、Factory での作成を後にする
    }
}
