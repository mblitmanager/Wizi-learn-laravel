<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Confirmation d'inscription par parrainage</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            color: #333333;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
        }

        .header {
            padding: 20px;
            background: #000000;
            color: white;
            text-align: center;
        }

        .header h1 {
            font-size: 20px;
            margin: 10px 0 0;
            color: #FFD700;
            font-weight: 600;
        }

        .content {
            padding: 25px;
        }

        .content p {
            margin-bottom: 20px;
            font-size: 15px;
        }

        .highlight-box {
            background: #fffae6;
            border-left: 4px solid #FFD700;
            padding: 15px;
            margin: 20px 0;
        }

        .highlight-box h2 {
            margin-top: 0;
            color: #000000;
            font-size: 16px;
        }

        ul {
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 10px;
        }

        .cta-button {
            display: block;
            background: #000000;
            color: #FFD700;
            text-align: center;
            padding: 12px;
            text-decoration: none;
            font-weight: 600;
            margin: 25px 0;
            border-radius: 4px;
            border: 2px solid #FFD700;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666666;
            background: #f5f5f5;
        }

        .footer a {
            color: #000000;
            text-decoration: none;
            font-weight: 600;
        }

        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ $message->embed($logo) }}" alt="Logo" width="100">
            <h1>Bienvenue {{ $filleul->stagiaire->prenom }} !</h1>
        </div>

        <div class="content">
            <p>Nous confirmons votre inscription à la formation <strong>{{ $formation->titre }}</strong> grâce au parrainage de {{ $parrain->name }}.</p>

            <div class="highlight-box">
                <h2>Votre parcours avec votre parrain</h2>
                <p>{{ $parrain->name }} sera votre guide tout au long de cette formation. En tant que membre expérimenté, il pourra :</p>
                <ul>
                    <li>Vous aider à bien démarrer la formation</li>
                    <li>Partager ses conseils pratiques</li>
                    <li>Répondre à vos questions</li>
                </ul>
            </div>

            <p><strong>Détails de votre formation :</strong></p>
            <ul>
                <li>Durée : {{ $formation->duree }}</li>
                @if($formation->certification)
                <li>Certification : {{ $formation->certification }}</li>
                @endif
                <li>Accès à la plateforme : sous 24 heures</li>
            </ul>

            <div class="divider"></div>

            <p><strong>Prochaines étapes :</strong></p>
            <ul>
                <li>Vous recevrez vos identifiants par email</li>
                <li>Connectez-vous pour accéder aux ressources</li>
                <li>Prenez contact avec votre parrain</li>
            </ul>

            <a href="{{ config('app.url') }}" class="cta-button">Accéder à votre espace</a>

            <p>Besoin d'aide ? Contactez-nous à <a href="mailto:support@example.com">support@example.com</a> ou au 01 23 45 67 89.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p>
                <a href="#">Mentions légales</a> |
                <a href="#">Confidentialité</a> |
                <a href="#">Désinscription</a>
            </p>
        </div>
    </div>
</body>

</html>