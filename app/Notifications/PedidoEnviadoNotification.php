<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PedidoEnviadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pedido;

    public function __construct($pedido)
    {
        $this->pedido = $pedido;
    }

    public function via($notifiable)
    {
        return $notifiable->email ? ['mail'] : [];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('📦 Seu pedido #' . $this->pedido->codigo . ' foi enviado!')
            ->greeting('Olá ' . $notifiable->nome . ',')
            ->line('Seu pedido foi enviado e está a caminho!')
            ->line('**Código de rastreio:** ' . $this->pedido->codigo_rastreio)
            ->action('🔍 Acompanhar Pedido', $this->getTrackingUrl())
            ->line('Acompanhe seu pedido usando o código acima no site dos Correios.')
            ->line('Obrigado por comprar conosco!');
    }

    protected function getTrackingUrl()
    {
        return 'https://www.linkcorreios.com.br/?id=' . $this->pedido->codigo_rastreio;
    }
}
