@extends('master')

@section('styles')
@parent
  <style>
    #card-principal {
      border: 1px solid blue;
    }
    .bg-principal {
      background-color: LightBlue;
      border-top: 3px solid blue;
    }
    .disable-links {
      pointer-events: none;
    }
  </style>
@endsection

@section('content')
@parent
  @php
    $solicitacaodocumento = $objeto;
    $condicao_ativa = true;
  @endphp
  <div class="row">
    <div class="col-md-12">
      <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-top">
          <div class="card-title my-0">
            @if ($modo == 'edit')
              <div style="display: flex; align-items: center; white-space: nowrap;">
                <a href="solicitacoesdocumentos">Solicitações de Documentos</a>
                &nbsp; | &nbsp;
                @include('solicitacoesdocumentos.partials.btn-enable-disable')
              </div>
            @else
              Nova Solicitação de Documento
            @endif
            para {{ $solicitacaodocumento->setor->sigla }} - {{ $solicitacaodocumento->setor->nome }}<br />
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-7">
              @include('solicitacoesdocumentos.show.card-principal', [       {{-- Principal --}}
                'setor' => $solicitacaodocumento->setor
              ])
            </div>
            <div class="col-md-5">
              @if ($modo == 'edit')
                @include('common.card-arquivos', [                           {{-- Arquivos --}}
                  'setor' => $solicitacaodocumento->setor,
                ])
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
