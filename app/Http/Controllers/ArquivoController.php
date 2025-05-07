<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\SolicitacaoDocumento;
use App\Models\TipoArquivo;
use App\Models\User;
use App\Utils\JSONForms;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ArquivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('show');
    }

    public function index()
    {
        // pelo fato de eu ter definido as rotas do ArquivoController com Route::resource, o Laravel espera que exista esta action, mesmo que eu nunca a invoque
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Arquivo        $arquivo
     * @return \Illuminate\Http\Response
     */
    public function show(Arquivo $arquivo)
    {
        $this->authorize('arquivos.view', $arquivo);

        ob_end_clean();    // https://stackoverflow.com/questions/39329299/laravel-file-downloaded-from-storage-folder-gets-corrupted

        return Storage::download($arquivo->caminho, $arquivo->nome_original);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request   $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $solicitacaodocumento = SolicitacaoDocumento::find($request->solicitacaodocumento_id);

        $validator = \Validator::make($request->all(), [
            'arquivo.*' => 'required|mimes:jpeg,jpg,png,pdf|max:' . config('solicitacoes-documentos.upload_max_filesize'),
            'solicitacaodocumento_id' => 'required|integer|exists:solicitacoesdocumentos,id',
        ]);
        if ($validator->fails()) {
            \UspTheme::activeUrl('solicitacoesdocumentos');
            return view('solicitacoesdocumentos.edit', array_merge($this->monta_compact($solicitacaodocumento, 'edit'), ['errors' => $validator->errors()]));
        }
        $this->authorize('arquivos.create', $solicitacaodocumento);

        // transaction para não ter problema de inconsistência do DB
        $solicitacaodocumento = DB::transaction(function () use ($request, $solicitacaodocumento) {

            foreach ($request->arquivo as $arq) {
                $arquivo = new Arquivo;
                $arquivo->user_id = \Auth::user()->id;
                $arquivo->nome_original = $arq->getClientOriginalName();
                $arquivo->caminho = $arq->store('./arquivos/' . $solicitacaodocumento->created_at->year);
                $arquivo->mimeType = $arq->getClientMimeType();
                $arquivo->tipoarquivo_id = TipoArquivo::where('setor_id', $request->setor_id)->where('nome', $request->tipoarquivo)->first()->id;
                $arquivo->save();

                $arquivo->solicitacoesdocumentos()->attach($solicitacaodocumento->id, ['tipo' => $request->tipoarquivo]);
            }

            $request->session()->flash('alert-success', 'Documento(s) adicionado(s) com sucesso');

            return $solicitacaodocumento;
        });

        \UspTheme::activeUrl('solicitacoesdocumentos');
        return view('solicitacoesdocumentos.edit', $this->monta_compact($solicitacaodocumento, 'edit', 'arquivos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  \App\Models\Arquivo        $arquivo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Arquivo $arquivo)
    {
        $solicitacaodocumento = SolicitacaoDocumento::find($request->solicitacaodocumento_id);

        $request->validate(
            ['nome_arquivo' => 'required'],
            ['nome_arquivo.required' => 'O nome do arquivo é obrigatório!']
        );
        $this->authorize('arquivos.update', [$arquivo, $solicitacaodocumento]);

        $nome_antigo = $arquivo->nome_original;
        $extensao = pathinfo($nome_antigo, PATHINFO_EXTENSION);
        $arquivo->nome_original = $request->nome_arquivo . '.' . $extensao;
        $arquivo->update();

        $request->session()->flash('alert-success', 'Documento renomeado com sucesso');
        \UspTheme::activeUrl('solicitacoesdocumentos');
        return view('solicitacoesdocumentos.edit', $this->monta_compact($solicitacaodocumento, 'edit', 'arquivos'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  \App\Models\Arquivo        $arquivo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Arquivo $arquivo)
    {
        $solicitacaodocumento = SolicitacaoDocumento::find($request->solicitacaodocumento_id);

        $this->authorize('arquivos.delete', [$arquivo, $solicitacaodocumento]);

        if (Storage::exists($arquivo->caminho))
            Storage::delete($arquivo->caminho);

        // transaction para não ter problema de inconsistência do DB
        $solicitacaodocumento = DB::transaction(function () use ($request, $arquivo, $solicitacaodocumento) {

            $arquivo->solicitacoesdocumentos()->detach($solicitacaodocumento->id, ['tipo' => $request->tipoarquivo]);
            $arquivo->delete();

            return $solicitacaodocumento;
        });

        $request->session()->flash('alert-success', 'Documento removido com sucesso');
        \UspTheme::activeUrl('solicitacoesdocumentos');
        return view('solicitacoesdocumentos.edit', $this->monta_compact($solicitacaodocumento, 'edit', 'arquivos'));
    }

    private function monta_compact(SolicitacaoDocumento $solicitacaodocumento, string $modo)
    {
        $data = (object) ('App\\Http\\Controllers\\SolicitacaoDocumentoController')::$data;
        $objeto = $solicitacaodocumento;
        $max_upload_size = config('solicitacoes-documentos.upload_max_filesize');

        return compact('data', 'objeto', 'modo', 'max_upload_size');
    }
}
