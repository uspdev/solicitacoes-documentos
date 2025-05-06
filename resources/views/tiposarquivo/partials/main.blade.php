<div class="row">
  <div class="col-md-12 form-inline">
    <span class="h4 mt-2">Tipos de Documento</span>
    @can('tiposarquivo.create')
      &nbsp; &nbsp;
      <button type="button" class="btn btn-sm btn-success" onclick="add_form()">
        <i class="fas fa-plus"></i> Novo
      </button>
    @endcan
  </div>
</div>

<table class="table table-sm my-0 ml-3">
  @php
    $setor_id_anterior = '';
  @endphp
  @foreach ($tiposarquivo as $tipoarquivo)
    @if ($tipoarquivo->setor_id != $setor_id_anterior)
      <tr>
        <td colspan="2">
          {{ $tipoarquivo->setor->sigla }} - {{ $tipoarquivo->setor->nome }}
        </td>
      </tr>
      @php
        $setor_id_anterior = $tipoarquivo->setor_id;
      @endphp
    @endif
    {{-- Mostra o conteúdo de um tipo de arquivo --}}
    <tr>
      <td nowrap>&nbsp; &nbsp;</td>
      <td width="100%">
        <div>
          <a name="{{ \Str::lower($tipoarquivo->id) }}" class="font-weight-bold" style="text-decoration: none;">{{ $tipoarquivo->nome }}</a>
          @can('tiposarquivo.update')
            @include('tiposarquivo.partials.btn-edit')
          @endcan
          @can('tiposarquivo.delete')
            @include('tiposarquivo.partials.btn-delete')
          @endcan
        </div>
      </td>
    </tr>
  @endforeach
</table>
