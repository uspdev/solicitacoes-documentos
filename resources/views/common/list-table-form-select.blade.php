<div class="form-group row">
  @php
    $col['label'] .= collect($rules[$col['name']] ?? [])->first(fn($rule) => str_contains($rule, 'required')) ? ' <small class="text-required">(*)</small>' : '';
    $ordenar_por_valor = $ordenar_por_valor ?? false;
    if ($ordenar_por_valor && isset($col['data']) && is_array($col['data'])) {
      $sortedData = $col['data'];
      asort($sortedData);
      $col['data'] = $sortedData;
    }
  @endphp
  {{ html()->label($col['label'] ?? $col['name'])->for($col['name'])->class('col-form-label col-sm-3') }}
  <div class="col-sm-9">
    {{ html()->select($col['name'], $col['data'])
      ->value(old($col['name'], $modo == 'edit' ? $objeto->{$col['name']} : ''))
      ->class('form-control')
      ->placeholder('Selecione...')
    }}
  </div>
</div>
