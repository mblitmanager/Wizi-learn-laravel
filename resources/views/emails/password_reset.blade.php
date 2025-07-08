<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation de mot de passe</title>
    <style>
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<h2>Réinitialisation de votre mot de passe</h2>
<p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour procéder :</p>

<a href="{{ $resetLink }}" class="button">
    Réinitialiser mon mot de passe
</a>

<p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>

<p>Cordialement,<br>L'équipe {{ config('app.name') }}</p>
</body>
</html>
