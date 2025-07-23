<!DOCTYPE html>
<html>
<head>
    <title>Confirmação de Cadastro</title>
</head>
<body>
<h2>Confirmação de Cadastro</h2>
<p>Olá {{ $user->email }},</p>

<p>Obrigado por se cadastrar em nosso sistema. Por favor, clique no link abaixo para confirmar seu email:</p>

<a href="{{ $verificationUrl }}">Confirmar meu email</a>

<p>Este link de confirmação expirará em 24 horas.</p>

<p>Se você não se cadastrou em nosso sistema, por favor ignore este email.</p>

<p>Atenciosamente,<br>Equipe da VivAlfaiataria</p>
</body>
</html>
