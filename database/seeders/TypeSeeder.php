<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('type')->insert([
            [
                'id' => 1,
                'libelle' => 'TRANSFERT_SIMPLE',
                'description' => 'Transfert direct entre deux utilisateurs',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'libelle' => 'TRANSFERT_MULTIPLE',
                'description' => 'Transfert vers plusieurs destinataires',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'libelle' => 'TRANSFERT_PLANIFIE',
                'description' => 'Transfert programmé pour une date ultérieure',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'libelle' => 'PAIEMENT_MARCHAND',
                'description' => 'Paiement à un marchand',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}