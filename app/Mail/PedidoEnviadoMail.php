<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoEnviadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $usuario;

    public function __construct($pedido, $usuario)
    {
        $this->pedido = $pedido;
        $this->usuario = $usuario;
    }

    public function build()
    {
        return $this->subject('📦 Seu pedido #'.$this->pedido->codigo.' foi enviado!')
            ->view('emails.pedido_enviado')
            ->with([
                'pedido' => $this->pedido,
                'usuario' => $this->usuario
            ]);
    }
}
