<?php

namespace App\Mail;

use App\Models\FuncionariosFolha;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NovaFolhaDisponivel extends Mailable
{
    use Queueable, SerializesModels;

    public $folha;

    public function __construct(FuncionariosFolha $folha)
    {
        $this->folha = $folha;
    }

    public function build()
    {
        return $this->subject('Novo Contracheque Disponível')
            ->view('emails.nova_folha');
    }
}
