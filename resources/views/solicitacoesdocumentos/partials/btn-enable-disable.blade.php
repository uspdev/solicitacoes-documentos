@section('styles')
@parent
  {{-- https://stackoverflow.com/questions/50349017/how-can-i-change-cursor-for-disabled-button-or-a-in-bootstrap-4 --}}
  <style>
    button:disabled {
      cursor: not-allowed;
      pointer-events: all !important;
    }
</style>
@endsection

{{ html()->form('post', 'solicitacoesdocumentos/edit/' . $solicitacaodocumento->id)->open() }}
  @method('put')
  @csrf
  <div class="btn-group btn-enable-disable">
    <button type="submit" class="btn btn-sm {{ ($solicitacaodocumento->estado == 'Pendente') ? 'btn-warning' : 'btn-secondary' }}" disabled name="estado" value="Pendente">
      Pendente
    </button>
    <button type="submit" class="btn btn-sm {{ ($solicitacaodocumento->estado == 'Providenciado') ? 'btn-success' : 'btn-secondary' }}" disabled name="estado" value="Providenciado">
      Providenciado
    </button>
  </div>
{{ html()->form()->close() }}
