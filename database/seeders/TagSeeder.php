<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'urgente'],
            ['name' => 'bug'],
            ['name' => 'mejora'],
            ['name' => 'consulta'],
            ['name' => 'hardware'],
            ['name' => 'software'],
            ['name' => 'red'],
        ];
    
        DB::table('tags')->insert($tags);
    }
}
