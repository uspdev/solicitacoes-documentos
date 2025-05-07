@section('styles')
@parent
  <style>
    #card-solicitacaodocumento-principal {
      border: 1px solid coral;
      border-top: 3px solid coral;
    }
  </style>
@endsection

{{ html()->form('post', 'solicitacoesdocumentos' . (($modo == 'edit') ? ('/edit/' . $solicitacaodocumento->id) : '/create'))
  ->attribute('id', 'form_principal')
  ->open() }}
  @csrf
  @method($modo == 'edit' ? 'put' : 'post')
  {{ html()->hidden('id') }}
  <input type="hidden" id="setor_id" name="setor_id" value="{{ $solicitacaodocumento->setor->id }}">
  <div class="card mb-3 w-100" id="card-solicitacaodocumento-principal">
    <div class="card-header">
      Informações básicas
    </div>
    <div class="card-body">
      <div class="list_table_div_form">
        @include('common.list-table-form-contents', [
          'setor_id' => $solicitacaodocumento->setor->id,
        ])
      </div>
      @if (($modo != 'edit') && (session('perfil') == 'usuario'))
        <div class="text-right">
          <button type="submit" class="btn btn-primary">Solicitar</button>
        </div>
      @endif
    </div>
  </div>
{{ html()->form()->close() }}

@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(document).ready(function() {
      $('#form_principal').find(':input:visible:first').focus();
    });
  </script>
@endsection
