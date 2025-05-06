<div class="form-group row">
  {{ html()->label($col['name'], $col['label'] ?? $col['name'])->class('col-form-label col-sm-2') }}
  <div class="col-sm-10">
    @php
      $table = substr($col['name'], 0, -3);
    @endphp
    {{ html()->select($col['name'], $col['data'])->class('form-control')->placeholder('Selecione...') }}
  </div>
</div>
