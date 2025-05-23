<?php

namespace App\Policies;

use App\Models\Setor;
use App\Models\SolicitacaoDocumento;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class SolicitacaoDocumentoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view their solicitações de documentos.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewTheir(User $user)
    {
        return Gate::allows('perfilusuario');
    }

    /**
     * Determine whether the user can view all solicitações de documentos.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return Gate::any(['perfiladmin', 'perfilgerente']);
    }

    /**
     * Determine whether the user can view the solicitação de documento.
     *
     * @param  \App\Models\User                  $user
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return mixed
     */
    public function view(User $user, SolicitacaoDocumento $solicitacaodocumento)
    {
        if (Gate::allows('perfiladmin'))
            return true;
        elseif (Gate::allows('perfilgerente'))
            return ($solicitacaodocumento->setor_id == \Auth::user()->obterSetorMaisRecente()->id);    // permite que o gerente visualize as solicitações de documentos do seu setor
        else
            return ($solicitacaodocumento->pessoas('Autor')->id == $user->id);           // permite que o usuário autor da solicitação de documento a visualize
    }

    /**
     * Determine whether the user can create solicitações de documentos.
     *
     * @param  \App\User           $user
     * @param  ?\App\Models\Setor  $setor
     * @return mixed
     */
    public function create(User $user, ?Setor $setor = null)
    {
        return Gate::allows('perfilusuario');
    }

    /**
     * Determine whether the user can update the solicitação de documento.
     *
     * @param  \App\Models\User                  $user
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return mixed
     */
    public function update(User $user, SolicitacaoDocumento $solicitacaodocumento)
    {
        return false;
    }

    /**
     * Determine whether the user can update the solicitação de documento status.
     *
     * @param  \App\Models\User                  $user
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return mixed
     */
    public function updateStatus(User $user, SolicitacaoDocumento $solicitacaodocumento)
    {
        if (Gate::allows('perfiladmin'))
            return true;
        elseif (Gate::allows('perfilgerente'))
            return ($solicitacaodocumento->setor_id == \Auth::user()->obterSetorMaisRecente()->id);    // permite que o gerente visualize as solicitações de documentos do seu setor
        else
            return false;
    }

    /**
     * Determine whether the user can update the solicitação de documento arquivos.
     *
     * @param  \App\Models\User                  $user
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return mixed
     */
    public function updateArquivos(User $user, SolicitacaoDocumento $solicitacaodocumento)
    {
        if (Gate::allows('perfiladmin'))
            return true;
        elseif (Gate::allows('perfilgerente'))
            return ($solicitacaodocumento->setor_id == \Auth::user()->obterSetorMaisRecente()->id);    // permite que o gerente visualize as solicitações de documentos do seu setor
        elseif (Gate::allows('perfilusuario'))
            return false;
    }
}
