<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitacaoDocumentoRequest;
use App\Models\Setor;
use App\Models\SolicitacaoDocumento;
use App\Models\TipoArquivo;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Uspdev\Replicado\Pessoa;

class SolicitacaoDocumentoController extends Controller
{
    // crud generico
    public static $data = [
        'title' => 'Solicitações de Documentos',
        'url' => 'solicitacoesdocumentos',     // caminho da rota do resource
        'modal' => true,
        'showId' => false,
        'viewBtn' => true,
        'editBtn' => false,
        'model' => 'App\Models\SolicitacaoDocumento',
    ];

    public function __construct()
    {
        $this->middleware('auth')->except([
            'listaSetoresParaSolicitacaoDocumento',
            'create',
            'store'
        ]);    // exige que o usuário esteja logado, exceto para estes métodos listados
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perfil_admin_ou_gerente =  in_array(session('perfil'), ['admin', 'gerente']);
        $this->authorize('solicitacoesdocumentos.view' . ($perfil_admin_ou_gerente ? 'Any' : 'Their'));

        \UspTheme::activeUrl('solicitacoesdocumentos');
        return view('solicitacoesdocumentos.index', $this->monta_compact_index());
    }

    /**
     * Mostra lista de setores
     * para solicitar documentos
     *
     * @param  \Illuminate\Http\Request   $request
     * @return \Illuminate\Http\Response
     */
    public function listaSetoresParaSolicitacaoDocumento(Request $request)
    {
        $this->authorize('solicitacoesdocumentos.create');

        $request->validate(['filtro' => 'nullable|string']);

        \UspTheme::activeUrl('solicitacoesdocumentos/create');
        $setores = Setor::listarSetoresParaSolicitacaoDocumento();          // obtém os setores
        return view('solicitacoesdocumentos.listasetoresparasolicitacaodocumento', compact('setores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Setor          $setor
     * @return \Illuminate\Http\Response
     */
    public function create(Setor $setor)
    {
        $this->authorize('solicitacoesdocumentos.create', $setor);

        $solicitacaodocumento = new SolicitacaoDocumento;
        $solicitacaodocumento->setor = $setor;
        $user = Auth::user();

        \UspTheme::activeUrl('solicitacoesdocumentos/create');
        return view('solicitacoesdocumentos.edit', $this->monta_compact($solicitacaodocumento, 'create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request        $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $setor = Setor::find($request->setor_id);
        $this->authorize('solicitacoesdocumentos.create', $setor);

        $validator = Validator::make($request->all(), SolicitacaoDocumentoRequest::rules, SolicitacaoDocumentoRequest::messages);
        if ($validator->fails()) {
            \UspTheme::activeUrl('solicitacoesdocumentos');
            return back()->withErrors($validator)->withInput();
        }

        $user = \Auth::user();

        // transaction para não ter problema de inconsistência do DB
        $solicitacaodocumento = DB::transaction(function () use ($request, $user) {

            // grava a solicitação de documento
            $solicitacaodocumento = new SolicitacaoDocumento;
            $solicitacaodocumento->setor_id = $request->setor_id;
            $solicitacaodocumento->tipoarquivo_id = $request->tipoarquivo_id;
            $solicitacaodocumento->estado = 'Pendente';
            $solicitacaodocumento->saveQuietly();      // vamos salvar sem evento pois o autor ainda não está cadastrado
            $solicitacaodocumento->users()->attach($user, ['papel' => 'Autor']);

            return $solicitacaodocumento;
        });

        // agora sim vamos disparar o evento (necessário porque acima salvamos com saveQuietly)
        event('eloquent.created: App\Models\SolicitacaoDocumento', $solicitacaodocumento);

        \UspTheme::activeUrl('solicitacoesdocumentos');
        return view('solicitacoesdocumentos.index', $this->monta_compact_index());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  \App\Models\SolicitacaoDocumento  $solicitacaodocumento
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, SolicitacaoDocumento $solicitacaodocumento)
    {
        $this->authorize('solicitacoesdocumentos.view', $solicitacaodocumento);    // este 1o passo da edição é somente um show, não chega a haver um update

        \UspTheme::activeUrl('solicitacoesdocumentos');
        return view('solicitacoesdocumentos.edit', $this->monta_compact($solicitacaodocumento, 'edit'));
    }

    private function processa_erro_store(string|array $msgs, Setor $setor, Request $request)
    {
        if (is_array($msgs))
            $msgs = implode('<br />', $msgs);
        $request->session()->flash('alert-danger', $msgs);

        \UspTheme::activeUrl('solicitacoesdocumentos/create');
        $solicitacaodocumento = new SolicitacaoDocumento;
        $solicitacaodocumento->setor = $setor;
        return view('solicitacoesdocumentos.edit', $this->monta_compact($solicitacaodocumento, 'create'));
    }

    public function monta_compact_index()
    {
        $data = self::$data;
        $solicitacoesdocumentos = SolicitacaoDocumento::listarSolicitacoesDocumentos();
        $max_upload_size = config('solicitacoes-documentos.upload_max_filesize');

        return compact('data', 'solicitacoesdocumentos', 'max_upload_size');
    }

    public function monta_compact(SolicitacaoDocumento $solicitacaodocumento, string $modo)
    {
        $data = (object) self::$data;
        $objeto = $solicitacaodocumento;
        $rules = SolicitacaoDocumentoRequest::rules;
        $max_upload_size = config('solicitacoes-documentos.upload_max_filesize');

        return compact('data', 'objeto', 'rules', 'modo', 'max_upload_size');
    }
}
