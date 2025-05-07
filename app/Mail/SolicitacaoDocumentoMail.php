<?php

namespace App\Mail;

use App\Models\SolicitacaoDocumento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitacaoDocumentoMail extends Mailable
{
    use Queueable, SerializesModels;

    // campos gerais
    protected $passo;
    protected $solicitacaodocumento;
    protected $user;

    // campos adicionais para nova solicitação de documento

    // campos adicionais para solicitação de documento atendida

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->passo = $data['passo'];
        $this->solicitacaodocumento = $data['solicitacaodocumento'];
        $this->user = $data['user'];

        switch ($this->passo) {
            case 'nova solicitação':
                break;

            case 'solicitação atendida':
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch ($this->passo) {
            case 'nova solicitação':
                return $this
                    ->subject('[' . config('app.name') . '] Documento Solicitado')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->view('emails.solicitacaodocumento_nova')
                    ->with([
                        'solicitacaodocumento' => $this->solicitacaodocumento,
                        'user' => $this->user,
                    ]);

                case 'solicitação atendida':
                    return $this
                        ->subject('[' . config('app.name') . '] Solicitação de Documento Atendida')
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->view('emails.solicitacaodocumento_atendida')
                        ->with([
                            'solicitacaodocumento' => $this->solicitacaodocumento,
                            'user' => $this->user,
                        ]);
        }
    }
}
