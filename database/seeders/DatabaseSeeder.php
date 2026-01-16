<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ExpenseCategorySeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            SecuritySeeder::class,
            UserSeeder::class,
            ExpenseCategorySeeder::class,
            TypeDocumentSeeder::class,
            ProductCategorySeeder::class,
            WarehouseSeeder::class,
        ]);
    }
}
