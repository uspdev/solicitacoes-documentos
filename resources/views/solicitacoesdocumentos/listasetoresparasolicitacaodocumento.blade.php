@extends('master')

@section('content')
@parent
  <div class="row">
    <div class="col-md-12 form-inline">
      <span class="h4 mt-2">Solicitação de Documento</span>
      @include('partials.datatable-filter-box', ['otable'=>'oTable'])
    </div>
  </div>

  @php
    $existem_setores = false;
    foreach ($setores as $setor)
      if ($setor->tiposarquivo->count() > 0) {
        $existem_setores = true;
        break;
      }
  @endphp

  @if ($existem_setores)
    <br />
    Para qual setor você deseja solicitar documento?<br />
    <table class="table table-sm table-hover solicitacao-documento display responsive" style="width: 100%;">
      <thead>
        <tr>
          <th style="border: none;"><span class="d-none">Setores</span></td>
        </tr>
      </thead>
      <tbody>
        @foreach ($setores as $setor)
          @if ($setor->tiposarquivo->count())
            <tr>
              <td>
                <a href="solicitacoesdocumentos/create/{{ $setor['id'] }}">
                  {{ $setor->sigla }} - {{ $setor->nome }}
                </a>
              </td>
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>
  @else
    <br />
    No momento, não há setores para os quais solicitar documento.
  @endif
@endsection

@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(document).ready(function() {
      oTable = $('.solicitacao-documento').DataTable({
        dom:
          't',
          'paging': false,
          'sort': false
      });
    });
  </script>
@endsection
