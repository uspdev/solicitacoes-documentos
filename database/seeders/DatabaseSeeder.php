<?php

namespace Database\Seeders;

use App\Models\SolicitacaoDocumento;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // desativando eventos no seeder
        SolicitacaoDocumento::flushEventListeners();

        $this->call([
            PermissionSeeder::class,        // adiciona permissions
            SetorReplicadoSeeder::class,    // adiciona todos os setores da unidade do replicado
            SetorSeeder::class,             // configura e-mails dos setores
        ]);
    }
}
