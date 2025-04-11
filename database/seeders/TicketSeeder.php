<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets= Ticket::factory(35)->create();
        $randomTagId=Tag::pluck('id')->toArray();

        foreach($tickets as $ticket){
            shuffle($randomTagId);
            $ticket->tags()->attach($this->getTagRandomIds($randomTagId));
        }
    }

    private function getTagRandomIds(array $randomTagId){
        return array_slice($randomTagId,0,random_int(1,count($randomTagId)-1));
    }
}