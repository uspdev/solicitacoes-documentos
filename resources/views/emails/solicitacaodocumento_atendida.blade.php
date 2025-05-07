Olá {{ $user->name }},<br />
<br />
Seu documento do tipo {{ $solicitacaodocumento->tipoarquivo->nome }} ao setor {{ $solicitacaodocumento->setor->sigla }} - {{ $solicitacaodocumento->setor->nome }} foi atendida.<br />
Entre no <a href="{{ config('app.url') }}">sistema</a> para baixá-lo.<br />
<br />
@include('emails.rodape')
