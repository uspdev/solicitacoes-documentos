<?php

namespace Database\Seeders;

use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Seeder;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SVGRAD
        //     configura e-mail do setor
        $setor = Setor::where('sigla', 'SVGRAD')->first();
        $setor->email = 'makf00@usp.br';
        $setor->save();
   }
}
