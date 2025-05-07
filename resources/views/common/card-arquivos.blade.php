@section('styles')
@parent
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
  <link rel="stylesheet" href="css/arquivos.css">
  <style>
    #card-arquivos {
      border: 1px solid DarkGoldenRod;
      border-top: 3px solid DarkGoldenRod;
    }
  </style>
@endsection

{{ html()->form('post', $data->url . '/edit/' . $objeto->id)
  ->attribute('enctype', 'multipart/form-data')
  ->attribute('id', 'form_arquivos')
  ->open() }}
  @csrf
  @method('put')
  {{ html()->hidden('id') }}
  <a id="card_arquivos" name="card_arquivos"></a>
  <div class="card bg-light mb-3 w-100" id="card-arquivos">
    <div class="card-header form-inline">
      Documento(s)
      @if (Gate::allows('solicitacoesdocumentos.updateArquivos', $objeto))
        <label for="input_arquivo">
          <span class="btn btn-sm btn-light text-primary ml-2"> <i class="fas fa-plus"></i> Adicionar</span>
        </label>
      @endif
      <span data-toggle="tooltip" data-html="true" title="Tamanho máximo de cada arquivo: {{ $max_upload_size }}KB ">
        <i class="fas fa-question-circle text-secondary ml-2"></i>
      </span>
    </div>
    <div class="card-body">
      <input type="hidden" name="setor_id" value="{{ $solicitacaodocumento->setor_id }}">
      <input type="hidden" name="solicitacaodocumento_id" value="{{ $solicitacaodocumento->id }}">
      <input type="hidden" name="nome_arquivo" id="nome_arquivo">
      @php
        $tipoarquivo = $solicitacaodocumento->tipoarquivo;
      @endphp
      <div class="arquivos-lista">
        <input type="hidden" name="tipoarquivo" id="tipoarquivo" value="{{ $tipoarquivo['nome'] }}">
        <input type="file" name="arquivo[]" id="input_arquivo" accept="image/jpeg,image/png,application/pdf" class="d-none" multiple capture="environment">

        @if ($objeto->arquivos->where('pivot.tipo', $tipoarquivo['nome'])->count() > 0)
          <ul class="list-unstyled">
            @foreach ($objeto->arquivos->where('pivot.tipo', $tipoarquivo['nome']) as $arquivo)
              @if (preg_match('/^(application\/pdf|image\/png|image\/jpeg)$/i', $arquivo->mimeType))
                <li class="modo-visualizacao">
                  @if (Gate::allows('solicitacoesdocumentos.updateArquivos', $objeto))
                    <div class="arquivo-acoes d-inline-block">
                      <a onclick="excluir_arquivo({{ $arquivo->id }}, '{{ $arquivo->nome_original }}'); return false;" class="btn btn-outline-danger btn-sm btn-deletar btn-arquivo-acao">
                        <i class="far fa-trash-alt"></i>
                      </a>
                      <a onclick="toggle_modo_edicao(this, {{ $arquivo->id }});" class="btn btn-outline-warning btn-sm btn-editar btn-arquivo-acao">
                        <i class="far fa-edit"></i>
                      </a>
                    </div>
                  @endif
                  <a href="arquivos/{{ $arquivo->id }}" title="{{ $arquivo->nome_original }}" class="nome-arquivo-display"><i class="fas fa-file-pdf"></i>
                    <span>{{ $arquivo->nome_original }}</span>
                  </a>
                  <div class="editar-nome-arquivo-form">
                    <div class="input-wrapper">
                      <input type="hidden" id="nome_arquivo_original_{{ $arquivo->id }}" value="{{ pathinfo($arquivo->nome_original, PATHINFO_FILENAME) }}">
                      <input type="text" id="nome_arquivo_{{ $arquivo->id }}" class="input-nome-arquivo" value="{{ pathinfo($arquivo->nome_original, PATHINFO_FILENAME) }}">
                    </div>
                    <div class="btns-wrapper">
                      <a onclick="alterar_arquivo({{ $arquivo->id }}); return false;" class="btn btn-outline-success btn-sm ml-2 btn-arquivo-acao">
                        <i class="fas fa-check"></i>
                      </a>
                      <a onclick="toggle_modo_edicao(this, {{ $arquivo->id }});" class="btn btn-outline-danger btn-sm btn-arquivo-acao limpar-edicao-nome">
                        <i class="fas fa-times"></i>
                      </a>
                    </div>
                  </div>
                </li>
              @endif
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
{{ html()->form()->close() }}

@include('common.modal-processando')

@section('javascripts_bottom')
@parent
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script type="text/javascript">
    var max_upload_size = {{ $max_upload_size }};
  </script>
  <script src="js/arquivos.js"></script>
@endsection
