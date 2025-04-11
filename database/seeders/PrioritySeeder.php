<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            'Crítica' => 'Impacto total en operaciones. Requiere solución inmediata (menos de 1 hora)',
            'Alta'    => 'Afecta procesos importantes. Resolver en menos de 4 horas',
            'Media'   => 'Problema con impacto parcial. Resolver en 24 horas',
            'Baja'    => 'Solicitud sin urgencia. Resolver cuando sea posible',
        ];

        foreach ($priorities as $name => $description) {
            Priority::create(compact('name','description'));
        }
    }
}
