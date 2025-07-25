<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pedido Enviado - {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Pedido Enviado!</h2>
    </div>

    <div class="content">
        <p>Olá <strong>{{ $usuario->nome }}</strong>,</p>

        <p>Seu pedido <strong>#{{ $pedido->codigo }}</strong> foi enviado e está a caminho!</p>

        <p><strong>Código de Rastreio:</strong> {{ $pedido->codigo_rastreio }}</p>

        <a href="https://www.linkcorreios.com.br/?id={{ $pedido->codigo_rastreio }}" class="button">
            🔍 Acompanhar Pedido
        </a>

        <p> Acompanhe seu pedido usando o código acima no site dos Correios.</p>

        <h3>Detalhes do Pedido:</h3>
        <ul>
            <li><strong>Data do Pedido:</strong> {{ $pedido->data_pedido->format('d/m/Y H:i') }}</li>
            <li><strong>Total:</strong> R$ {{ number_format($pedido->total, 2, ',', '.') }}</li>
        </ul>

        <p> Se você estiver com problemas para clicar no botão "🔍 Acompanhar Pedido", copie e cole o URL abaixo em seu navegador
            da web: <a href="https://www.linkcorreios.com.br/?id=AC851553861BR">https://www.linkcorreios.com.br/?id=AC851553861BR</a>
        </p>

        <p>Obrigado por comprar conosco!</p>
    </div>

    <div class="footer">
        <p>Atenciosamente,<br>
            Equipe {{ config('app.name') }}</p>

        <p>Se você não reconhece esta compra, por favor entre em contato conosco.</p>
    </div>
</div>
</body>
</html>

