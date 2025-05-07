@if ($solicitacaodocumento->estado == 'Pendente')
  <span class="badge badge-light text-secondary"> {{ $solicitacaodocumento->estado }} </span>
@elseif ($solicitacaodocumento->estado == 'Atendida')
  <span class="badge badge-light text-secondary"> {{ $solicitacaodocumento->estado }} </span>
@endif
