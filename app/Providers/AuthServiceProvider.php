<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });

        Gate::define('gerente', function ($user) {
            return $user->is_admin;
        });

        Gate::define('usuario', function ($user) {
            return $user;
        });

        # perfis
        # o perfil é o modo como o usuário se apresenta
        # ideal para mostrar os menus e a lista de categorias
        Gate::define('perfiladmin', function ($user) {
            return (session('perfil') == 'admin');
        });

        Gate::define('perfilgerente', function ($user) {
            return (session('perfil') == 'gerente');
        });

        Gate::define('perfilusuario', function ($user) {
            return ((session('perfil') == 'usuario') || empty(session('perfil')));
        });

        Gate::define('trocarPerfil', function ($user) {
            return Gate::any(['admin', 'gerente']);
        });

        # se o admin assumir identidade de outro usuário, permite retornar
        Gate::define('desassumir', function ($user) {
            return session('adminCodpes');
        });

        # policies
        Gate::resource('admin', 'App\Policies\AdminPolicy');
        Gate::resource('arquivos', 'App\Policies\ArquivoPolicy');
        Gate::define('limpezadados.showForm', 'App\Policies\LimpezaDadosPolicy@showForm');
        Gate::define('limpezadados.run', 'App\Policies\LimpezaDadosPolicy@run');
        Gate::resource('setores', 'App\Policies\SetorPolicy');
        Gate::resource('solicitacoesdocumentos', 'App\Policies\SolicitacaoDocumentoPolicy');
        Gate::define('solicitacoesdocumentos.viewTheir', 'App\Policies\SolicitacaoDocumentoPolicy@viewTheir');
        Gate::define('solicitacoesdocumentos.updateStatus', 'App\Policies\SolicitacaoDocumentoPolicy@updateStatus');
        Gate::define('solicitacoesdocumentos.updateArquivos', 'App\Policies\SolicitacaoDocumentoPolicy@updateArquivos');
        Gate::resource('tiposarquivo', 'App\Policies\TipoArquivoPolicy');
        Gate::resource('users', 'App\Policies\UserPolicy');
    }
}
