Olá {{ $user->name }},<br />
<br />
Sua solicitação do documento {{ $solicitacaodocumento->tipoarquivo->nome }} foi encaminhada para {{ $solicitacaodocumento->setor->sigla }} - {{ $solicitacaodocumento->setor->nome }}.<br />
Aguarde ele ser providenciado.<br />
Caso tenha dúvidas, entre em contato conosco no endereço {{ $solicitacaodocumento->setor->email }}.<br />
<br />
@include('emails.rodape')
