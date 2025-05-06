<?php

$solicitacaodocumento = [
    [
        'text' => '<i class="far fa-plus-square"></i> Nova Solicitação de Documento',
        'url' => 'solicitacoesdocumentos/create',
        'can' => 'solicitacoesdocumentos.create',
    ],
    [
        'text' => '<i class="far fa-list-alt"></i> Minhas Solicitações de Documentos',
        'url' => 'solicitacoesdocumentos',
        'can' => 'solicitacoesdocumentos.viewTheir',
    ],
];

$admin = [
    [
        'text' => '<i class="fa fa-file"></i> Tipos de Documento',
        'url' => 'tiposarquivo',
        'can' => 'tiposarquivo.viewAny',
    ],
];

$menu = [
    [
        'text' => '<i class="fa fa-user-cog" aria-hidden="true"></i> Solicitações de Documentos',
        'submenu' => $solicitacaodocumento,
        'can' => 'solicitacoesdocumentos.create',
    ],
    [
        'text' => '<i class="far fa-list-alt"></i> Solicitações de Documentos',
        'url' => 'solicitacoesdocumentos',
        'can' => 'solicitacoesdocumentos.viewAny',
    ],
    [
        'text' => '<i class="fas fa-sitemap"></i> Setores',
        'url' => 'setores',
        'can' => 'setores.viewAny',
    ],
    [
        'text' => '<i class="fa fa-user-cog" aria-hidden="true"></i> Administração',
        'submenu' => $admin,
        'can' => 'admin.viewAny',
    ],
];

$trocarPerfil = [
    [
        'text' => '<i class="fas fa-users ml-2"></i> Meu Perfil',
        'url' => 'users/meuperfil',
        'can' => 'usuario',
    ],
    [
        'type' => 'divider',
        'can' => 'trocarPerfil',
    ],
    [
        'type' => 'header',
        'text' => '<b><i class="fas fa-id-badge"></i>  Trocar perfil</b>',
        'can' => 'trocarPerfil',
    ],
    [
        'text' => '&nbsp; Admin',
        'url' => 'users/perfil/admin',
        'can' => 'admin',
    ],
    [
        'text' => '&nbsp; Gerente',
        'url' => 'users/perfil/gerente',
        'can' => 'gerente',
    ],
    [
        'text' => '&nbsp; Usuário',
        'url' => 'users/perfil/usuario',
        'can' => 'trocarPerfil',
    ],
];

$right_menu = [
    [
        'key' => 'laravel-tools',
        'can' => 'perfiladmin',
    ],
    [
        'key' => 'senhaunica-socialite',
        'can' => 'perfiladmin',
    ],
    [
        'text' => '<span class="badge badge-danger">Admin</span>',
        'url' => '#',
        'can' => 'perfiladmin',
    ],
    [
        'text' => '<span class="badge badge-warning">Gerente</span>',
        'url' => '#',
        'can' => 'perfilgerente',
    ],
    [
        'text' => '<i class="fas fa-cog"></i>',
        'title' => 'Configurações',
        'submenu' => $trocarPerfil,
        'align' => 'right',
        'can' => 'usuario',
    ],
];

return [
    # valor default para a tag title, dentro da section title.
    # valor pode ser substituido pela aplicação.
    'title' => config('app.name'),

    # USP_THEME_SKIN deve ser colocado no .env da aplicação
    'skin' => env('USP_THEME_SKIN', 'uspdev'),

    # chave da sessão. Troque em caso de colisão com outra variável de sessão.
    'session_key' => 'laravel-usp-theme',

    # usado na tag base, permite usar caminhos relativos nos menus e demais elementos html
    # na versão 1 era dashboard_url
    'app_url' => config('app.url'),

    # login e logout
    'logout_method' => 'POST',
    'logout_url' => 'logout',
    'login_url' => 'login',

    # menus
    'menu' => $menu,
    'right_menu' => $right_menu,

    # mensagens flash - https://uspdev.github.io/laravel#31-mensagens-flash
    'mensagensFlash' => false,

    # container ou container-fluid
    'container' => 'container-fluid',
];
