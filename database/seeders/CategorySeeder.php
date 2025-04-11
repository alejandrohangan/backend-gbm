<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Hardware' => 'Problemas relacionados con equipos físicos (PCs, servidores, impresoras, etc.)',
            'Software' => 'Incidencias en aplicaciones, sistemas operativos o programas',
            'Redes'    => 'Conexiones lentas, caídas de red o configuración de dispositivos',
            'Usuarios' => 'Solicitudes de asistencia para accesos, contraseñas o capacitación',
        ];
        foreach($categories as $name=>$description){
            Category::create(compact('name','description'));
        }
    }
}
