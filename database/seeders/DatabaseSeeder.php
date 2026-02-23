<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Vendedor',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $catAccion = Category::create(['name' => 'Acción', 'description' => 'Juegos de acción']);
        $catRpg = Category::create(['name' => 'RPG', 'description' => 'Juegos de rol y aventura']);
        $catDeportes = Category::create(['name' => 'Deportes', 'description' => 'Juegos de Deportes']);

        Product::create([
            'category_id' => $catRpg->id,
            'name' => 'God of War',
            'description' => 'Kratos tras su venganza contra los dioses griegos, vive como un mortal en el hostil mundo de la mitología nórdica.
            Acompañado por su hijo Atreus, Kratos debe dominar su ira, protegerlo y sobrevivir a feroces monstruos y deidades nórdicas.',
            'price' => 700.00,
            'stock' => 10,
            'image_url' => 'https://m.media-amazon.com/images/I/81fBLI9vSQL._AC_UF1000,1000_QL80_.jpg'
        ]);

    }
}
