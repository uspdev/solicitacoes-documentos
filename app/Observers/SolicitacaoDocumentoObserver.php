<?php

namespace App\Observers;

use App\Mail\SolicitacaoDocumentoMail;
use App\Models\SolicitacaoDocumento;

class SolicitacaoDocumentoObserver
{
    /**
     * Handle the SolicitacaoDocumento "created" event.
     *
     * Ao criar uma solicitação de documento, ela deve ser enviada para o setor.
     *
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return void
     */
    public function created(SolicitacaoDocumento $solicitacaodocumento)
    {
        $user = $solicitacaodocumento->pessoas('Autor');

        // envia e-mail avisando o setor sobre a nova solicitação de documento
        $passo = 'nova solicitação';
        \Mail::to($solicitacaodocumento->setor->email)
            ->queue(new SolicitacaoDocumentoMail(compact('passo', 'solicitacaodocumento', 'user')));
    }

    /**
     * Listen to the SolicitacaoDocumento updating event.
     *
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return void
     */
    public function updating(SolicitacaoDocumento $solicitacaodocumento)
    {
        //
    }

    /**
     * Handle the SolicitacaoDocumento "updated" event.
     *
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return void
     */
    public function updated(SolicitacaoDocumento $solicitacaodocumento)
    {
        if ($solicitacaodocumento->isDirty('estado') &&                        // se a alteração na solicitação de documento foi somente no estado
            ($solicitacaodocumento->getOriginal('estado') == 'Pendente') &&    // se o estado anterior era Pendente
            ($solicitacaodocumento->estado == 'Atendida')) {                   // se o novo estado é Atendida

            // envia e-mail avisando o usuário sobre o atendimento da solicitação de documento
            $passo = 'solicitação atendida';
            $user = $solicitacaodocumento->pessoas('Autor');
            \Mail::to($user->email)
                ->queue(new SolicitacaoDocumentoMail(compact('passo', 'solicitacaodocumento', 'user')));
        }
    }

    /**
     * Handle the SolicitacaoDocumento "deleted" event.
     *
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return void
     */
    public function deleted(SolicitacaoDocumento $solicitacaodocumento)
    {
        //
    }

    /**
     * Handle the SolicitacaoDocumento "restored" event.
     *
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return void
     */
    public function restored(SolicitacaoDocumento $solicitacaodocumento)
    {
        //
    }

    /**
     * Handle the SolicitacaoDocumento "force deleted" event.
     *
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return void
     */
    public function forceDeleted(SolicitacaoDocumento $solicitacaodocumento)
    {
        //
    }
}
