<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LimpezaDadosController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\SolicitacaoDocumentoController;
use App\Http\Controllers\TipoArquivoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', [IndexController::class, 'index'])->name('home');

// SENHA ÚNICA
Route::get('login', [LoginController::class, 'redirectToProvider'])->name('login');
Route::get('callback', [LoginController::class, 'handleProviderCallback']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// SOLICITAÇÕES DE DOCUMENTOS
Route::get('solicitacoesdocumentos', [SolicitacaoDocumentoController::class, 'index'])->name('solicitacoesdocumentos.index');
Route::get('solicitacoesdocumentos/create', [SolicitacaoDocumentoController::class, 'listaSetoresParaSolicitacaoDocumento'])->name('solicitacoesdocumentos.create');
Route::get('solicitacoesdocumentos/create/{setor}', [SolicitacaoDocumentoController::class, 'create'])->name('solicitacoesdocumentos.create.setor');
Route::post('solicitacoesdocumentos/create', [SolicitacaoDocumentoController::class, 'store'])->name('solicitacoesdocumentos.store');
Route::get('solicitacoesdocumentos/edit/{solicitacaodocumento}', [SolicitacaoDocumentoController::class, 'edit'])->name('solicitacoesdocumentos.edit');

// ARQUIVOS
Route::resource('arquivos', ArquivoController::class);

// SETORES
Route::post('setores/{setor}/pessoas', [SetorController::class, 'storePessoa']);
Route::delete('setores/{setor}/pessoas/{id}', [SetorController::class, 'destroyPessoa']);
Route::resource('setores', SetorController::class);

// TIPOS DE ARQUIVO
Route::resource('tiposarquivo', TipoArquivoController::class);

// USERS
Route::get('search/partenome', [UserController::class, 'partenome']);
Route::get('search/codpes', [UserController::class, 'codpes']);
Route::get('users/perfil/{perfil}', [UserController::class, 'trocarPerfil']);
Route::get('users/meuperfil', [UserController::class, 'meuperfil']);
Route::resource('users', UserController::class);

// LIMPEZA DE DADOS
Route::get('limpezadados', [LimpezaDadosController::class, 'showForm'])->name('limpezadados.showForm');
Route::post('limpezadados', [LimpezaDadosController::class, 'run'])->name('limpezadados.run');

// ADMIN
Route::get('admin', [AdminController::class, 'index']);
Route::get('admin/get_oauth_file/{filename}', [AdminController::class, 'getOauthFile']);
