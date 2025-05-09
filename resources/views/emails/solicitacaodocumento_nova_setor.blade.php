Para {{ $solicitacaodocumento->setor->sigla }} - {{ $solicitacaodocumento->setor->nome }}:<br />
<br />
O usuário {{ $user->name }} de número USP {{ $user->codpes }} solicitou através do <a href="{{ config('app.url') }}">sistema</a>, o documento {{ $solicitacaodocumento->tipoarquivo->nome }}.
<br />
@include('emails.rodape')
