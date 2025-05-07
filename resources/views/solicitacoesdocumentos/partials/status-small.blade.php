@if ($solicitacaodocumento->estado == 'Pendente')
  <span class="text-warning" data-toggle="tooltip" title="{{ $solicitacaodocumento->estado }}"> <i class="fas fa-circle"></i> </span>
@elseif ($solicitacaodocumento->estado == 'Providenciado')
  <span class="text-success" data-toggle="tooltip" title="{{ $solicitacaodocumento->estado }}"> <i class="fas fa-circle"></i> </span>
@endif
