<?php

namespace App\Listeners;

use App\Models\Setor;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Uspdev\Replicado\Pessoa;

class LoginListener
{
    public function __construct()
    {
        //
    }

    public function handle(Login $event)
    {
        $user = $event->user;
        if (!is_null($user->codpes)) {    // no caso de usuários sem codpes, tudo o que precisa ser feito já foi feito no LocalUserController

            $vinculos = Pessoa::listarVinculosAtivos($user->codpes, false);
            $log = 'login listener:';

            // vincula a pessoa ao setor
            foreach ($vinculos as $vinculo)
                if ($setor = Setor::where('cod_set_replicado', $vinculo['codset'])->first())
                    Setor::vincularPessoa($setor, $user, mb_convert_case($vinculo['tipvin'], MB_CASE_TITLE));

            // vamos manter a configuracao antiga para compatibilidade retroativa
            // mas deverá ser ajustado e removido as referências a "is_admin"
            // vamos verificar no config se o usuário é admin
            if (in_array($user->codpes, config('senhaunica.admins')))
                $user->is_admin = true;

            // atualiza o last login do usuário
            $user->last_login_at = now();
            $user->save();

            if ($user->is_admin)
                session(['perfil' => 'admin']);
            elseif ($user->setores()->wherePivot('funcao', 'Gerente')->count())
                session(['perfil' => 'gerente']);
            else
                session(['perfil' => 'usuario']);

            config('app.debug') && Log::info($log);
        }
    }
}
