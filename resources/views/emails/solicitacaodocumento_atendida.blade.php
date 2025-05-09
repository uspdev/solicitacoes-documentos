Olá {{ $user->name }},<br />
<br />
Sua solicitação do documento {{ $solicitacaodocumento->tipoarquivo->nome }} para {{ $solicitacaodocumento->setor->sigla }} - {{ $solicitacaodocumento->setor->nome }} foi atendida.<br />
Entre no <a href="{{ config('app.url') }}">sistema</a> para baixá-lo.<br />
<br />
@include('emails.rodape')
