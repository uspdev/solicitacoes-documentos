Ao setor {{ $solicitacaodocumento->setor->sigla }} - {{ $solicitacaodocumento->setor->nome }}:<br />
<br />
Foi solicitado um documento do tipo {{ $solicitacaodocumento->tipoarquivo->nome }}.<br />
Favor subir no <a href="{{ config('app.url') }}">sistema</a> esse documento do usuário {{ $user->name }} de número USP {{ $user->codpes }}.<br />
<br />
@include('emails.rodape')
