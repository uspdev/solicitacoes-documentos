<?php

namespace App\Policies;

use App\Models\Arquivo;
use App\Models\SolicitacaoDocumento;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class ArquivoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\?User    $user
     * @param  \App\Models\Arquivo  $arquivo
     * @return mixed
     */
    public function view(?User $user, Arquivo $arquivo)    // se não colocarmos a interrogação, esta policy não é invocada no caso de usuário não logado
    {
        if (Gate::allows('perfiladmin'))
            return true;                                           // permite que admins baixem todos os arquivos
        elseif (Gate::allows('perfilgerente'))
            return ($solicitacaodocumento->setor_id == \Auth::user()->obterSetorMaisRecente()->id);    // permite que o gerente visualize as solicitações de documentos do seu setor
        elseif (Gate::allows('perfilusuario'))
            foreach ($arquivo->solicitacoesdocumentos as $solicitacaodocumento) {
                $autor_solicitacaodocumento = $solicitacaodocumento->pessoas('Autor');
                if ($autor_solicitacaodocumento && ($autor_solicitacaodocumento->id == $user->id))
                    return true;                                   // permite que usuários baixem arquivos de suas solicitações de documentos
            }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User                  $user
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return mixed
     */
    public function create(User $user, SolicitacaoDocumento $solicitacaodocumento)
    {
        if (Gate::allows('perfiladmin'))
            return true;
        elseif (Gate::allows('perfilgerente'))
            return ($solicitacaodocumento->setor_id == \Auth::user()->obterSetorMaisRecente()->id);    // permite que o gerente visualize as solicitações de documentos do seu setor
        elseif (Gate::allows('perfilusuario'))
            return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User                  $user
     * @param  \App\Models\Arquivo               $arquivo
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return mixed
     */
    public function update(User $user, Arquivo $arquivo, SolicitacaoDocumento $solicitacaodocumento)
    {
        return $this->authorize_update_delete($user, $arquivo, $solicitacaodocumento);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User                  $user
     * @param  \App\Models\Arquivo               $arquivo
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return mixed
     */
    public function delete(User $user, Arquivo $arquivo, SolicitacaoDocumento $solicitacaodocumento)
    {
        return $this->authorize_update_delete($user, $arquivo, $solicitacaodocumento);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function restore(User $user)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        //
    }

    private function authorize_update_delete(User $user, Arquivo $arquivo, SolicitacaoDocumento $solicitacaodocumento)
    {
        if (Gate::allows('perfiladmin'))
            return true;
        elseif (Gate::allows('perfilgerente'))
            return ($solicitacaodocumento->setor_id == \Auth::user()->obterSetorMaisRecente()->id);    // permite que o gerente visualize as solicitações de documentos do seu setor
        elseif (Gate::allows('perfilusuario'))
            return false;
    }
}
