<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoArquivoRequest;
use App\Models\TipoArquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class TipoArquivoController extends Controller
{
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
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  string                     $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $id)
    {
        $this->authorize('tiposarquivo.viewAny');

        \UspTheme::activeUrl('tiposarquivo');
        if ($request->ajax())
            return TipoArquivo::find((int) $id);    // preenche os dados do form de edição de um tipo de arquivo
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
        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        $tipoarquivo = TipoArquivo::create($request->all());

        $request->session()->flash('alert-success', 'Dados adicionados com sucesso');
        \UspTheme::activeUrl('tiposarquivo');
        return view('tiposarquivo.tree', $this->monta_compact_index());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TipoArquivoRequest  $request
     * @param  string                                 $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoArquivoRequest $request, string $id)
    {
        $this->authorize('tiposarquivo.update');

        $validator = Validator::make($request->all(), TipoArquivoRequest::rules, TipoArquivoRequest::messages);
        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        $tipoarquivo = TipoArquivo::find((int) $id);
        $tipoarquivo->fill($request->all());
        $tipoarquivo->save();

        $request->session()->flash('alert-success', 'Dados editados com sucesso');
        \UspTheme::activeUrl('tiposarquivo');
        return view('tiposarquivo.tree', $this->monta_compact_index());
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
        $tiposarquivo = TipoArquivo::all()->sortBy('setor.sigla');
        $fields = TipoArquivo::getFields();
        $modal['url'] = 'tiposarquivo';
        $modal['title'] = 'Editar Tipo de Documento';
        $rules = TipoArquivoRequest::rules;

        return compact('tiposarquivo', 'fields', 'modal', 'rules');
    }
}
