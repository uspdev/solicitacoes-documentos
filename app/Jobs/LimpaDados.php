<?php

namespace App\Jobs;

use App\Models\Arquivo;
use App\Models\SolicitacaoDocumento;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LimpaDados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data_limite;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data_limite)
    {
        $this->data_limite = $data_limite;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data_limite = $this->data_limite;

        // transaction para não ter problema de inconsistência do DB
        $db_transaction = DB::transaction(function () use ($data_limite) {

            // apaga todas as solicitações de documentos gravadas no banco de dados até essa data
            foreach (SolicitacaoDocumento::where('created_at', '<=', $data_limite)->get() as $solicitacaodocumento) {
                $solicitacaodocumento->arquivos()->detach();
                $solicitacaodocumento->pessoas()->detach();
                $solicitacaodocumento->delete();
            }

            // apaga todos os arquivos gravados no banco de dados até essa data
            foreach (Arquivo::where('created_at', '<=', $data_limite)->get() as $arquivo)
                $arquivo->delete();
        });

        // apaga todos os arquivos gravados no servidor até essa data
        $pasta_base = storage_path('app/arquivos');
        if (File::exists($pasta_base))
            foreach (File::directories($pasta_base) as $subpasta)
                foreach (File::files($subpasta) as $arquivo)
                    if (Carbon::createFromTimestamp(File::lastModified($arquivo))->lte($data_limite))
                        File::delete($arquivo);
    }
}
