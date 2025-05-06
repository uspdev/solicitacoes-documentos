<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoArquivoRequest;
use App\Models\TipoArquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class TipoArquivoController extends Controller
{
    // crud generico
    public static $data = [
        'title' => 'Tipos de Documento',
        'url' => 'tiposarquivo',     // caminho da rota do resource
        'modal' => true,
        'showId' => false,
        'viewBtn' => true,
        'editBtn' => false,
        'model' => 'App\Models\TipoArquivo',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request   $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('tiposarquivo.viewAny');

        \UspTheme::activeUrl('tiposarquivo');
        if (!$request->ajax())
            return view('tiposarquivo.tree', $this->monta_compact_index());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('tiposarquivo.create');

        \UspTheme::activeUrl('tiposarquivo');
        return view('tiposarquivo.edit', $this->monta_compact(new TipoArquivo, 'create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TipoArquivoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoArquivoRequest $request)
    {
        $this->authorize('tiposarquivo.create');

        $validator = Validator::make($request->all(), TipoArquivoRequest::rules, TipoArquivoRequest::messages);
        if ($validator->fails()) {
            \UspTheme::activeUrl('tiposarquivo');
            return back()->withErrors($validator)->withInput();
        }

        $tipoarquivo = TipoArquivo::create($request->all());

        $request->session()->flash('alert-success', 'Tipo de documento cadastrado com sucesso');
        \UspTheme::activeUrl('tiposarquivo');
        return view('tiposarquivo.edit', $this->monta_compact($tipoarquivo, 'edit'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  \App\Models\TipoArquivo    $tipoarquivo
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, TipoArquivo $tipoarquivo)
    {
        $this->authorize('tiposarquivo.update');

        \UspTheme::activeUrl('tiposarquivo');
        return view('tiposarquivo.edit', $this->monta_compact($tipoarquivo, 'edit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TipoArquivoRequest  $request
     * @param  \App\Models\TipoArquivo                $tipoarquivo
     * @return \Illuminate\Http\Response
     */
    public function update(TipoArquivoRequest $request, TipoArquivo $tipoarquivo)
    {
        $this->authorize('tiposarquivo.update');

        $validator = Validator::make($request->all(), TipoArquivoRequest::rules, TipoArquivoRequest::messages);
        if ($validator->fails()) {
            \UspTheme::activeUrl('tiposarquivo');
            return back()->withErrors($validator)->withInput();
        }

        $tipoarquivo->nome = $request->nome;
        $tipoarquivo->save();

        $request->session()->flash('alert-success', 'Tipo de documento alterado com sucesso');
        \UspTheme::activeUrl('tiposarquivo');
        return view('tiposarquivo.edit', $this->monta_compact($tipoarquivo, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\TipoArquivoRequest  $request
     * @param  string                                 $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoArquivoRequest $request, string $id)
    {
        $this->authorize('tiposarquivo.delete');

        $tipoarquivo = TipoArquivo::find((int) $id);
        if ($tipoarquivo->arquivos()->exists())
            $request->session()->flash('alert-danger', 'Há arquivos armazenados deste tipo!');
        else {
            $tipoarquivo->delete();
            $request->session()->flash('alert-success', 'Dados removidos com sucesso!');
        }
        \UspTheme::activeUrl('tiposarquivo');
        return view('tiposarquivo.tree', $this->monta_compact_index());
    }

    private function monta_compact_index()
    {
        $tiposarquivo = TipoArquivo::listarTiposArquivo()->orderBy('id')->get();
        $fields = TipoArquivo::getFields();
        $modal['url'] = 'tiposarquivo';
        $modal['title'] = 'Editar Tipo de Documento';
        $rules = TipoArquivoRequest::rules;

        return compact('tiposarquivo', 'fields', 'modal', 'rules');
    }

    private function monta_compact(TipoArquivo $tipoarquivo, string $modo)
    {
        $data = (object) self::$data;
        $objeto = $tipoarquivo;
        $rules = TipoArquivoRequest::rules;

        return compact('data', 'objeto', 'rules', 'modo');
    }
}
