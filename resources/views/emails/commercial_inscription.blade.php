<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Nouvelle inscription - Filleul à contacter</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: monospace;
            font-size: 12px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: #333333;
            line-height: 1.6;
        }

        .email-container {
            max-width: 620px;
            margin: 40px auto;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border-radius: 0;
            overflow: hidden;
            border: 1px solid #eaeaea;
        }

        .header {
            padding: 40px 20px 30px;
            background: #ffffff;
            color: #222222;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .logo {
            height: 48px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 22px;
            margin: 10px 0 0;
            color: #222222;
            font-weight: 600;
            letter-spacing: -0.2px;
        }

        .content {
            padding: 40px;
        }

        .content p {
            margin-bottom: 20px;
            font-size: 15px;
            color: #555555;
        }

        .highlight-box {
            background: #f9f9f9;
            border-left: 3px solid #2c7be5;
            padding: 24px;
            margin: 30px 0;
            border-radius: 0 4px 4px 0;
        }

        .highlight-box h2 {
            margin-top: 0;
            color: #222222;
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        ul {
            padding-left: 20px;
            margin: 18px 0;
        }

        ul li {
            margin-bottom: 8px;
            color: #555555;
            line-height: 1.5;
        }

        .cta-button {
            display: inline-block;
            background: #2c7be5;
            color: #ffffff;
            text-align: center;
            padding: 14px 28px;
            text-decoration: none;
            font-weight: 500;
            margin: 25px 0;
            border-radius: 4px;
            transition: background 0.2s ease;
            font-size: 15px;
        }

        .cta-button:hover {
            background: #1a68d1;
        }

        .footer {
            padding: 25px;
            text-align: center;
            font-size: 12px;
            color: #999999;
            background: #ffffff;
            border-top: 1px solid #f0f0f0;
        }

        .footer a {
            color: #666666;
            text-decoration: none;
            font-weight: 500;
            margin: 0 10px;
        }

        .footer a:hover {
            color: #2c7be5;
        }

        .divider {
            border-top: 1px solid #f0f0f0;
            margin: 30px 0;
            opacity: 0.5;
        }

        .info-card {
            background: #f9f9f9;
            border-radius: 0;
            padding: 20px;
            margin: 25px 0;
            border-left: 3px solid #e0e0e0;
        }

        .info-card h3 {
            margin-top: 0;
            color: #222222;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .text-accent {
            color: #2c7be5;
            font-weight: 500;
        }

        .text-center {
            text-align: center;
        }

        .signature {
            margin-top: 30px;
            color: #666666;
            font-style: italic;
        }

        .contact-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }

        .contact-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .contact-item strong {
            min-width: 120px;
            color: #333;
        }

        .action-box {
            background: #e8f4fd;
            border: 1px solid #b6e0fe;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }

        .action-box h3 {
            color: #1a68d1;
            margin-top: 0;
        }
    </style>
</head>

<body>
<div class="email-container">
    <div class="header">
        <img src="{{ $message->embed($logo) }}" alt="Logo" class="logo">
        <h1>Nouvelle inscription - Filleul à contacter</h1>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $commercial->user->name }}</strong>,</p>
        <p>Une nouvelle personne souhaite s'inscrire via le parrainage de <strong>{{ $parrain->name }}</strong>.</p>

        <div class="highlight-box">
            <h2>Informations du prospect</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <strong>Nom :</strong> {{ $filleul->name }}
                </div>
                <div class="contact-item">
                    <strong>Email :</strong> {{ $filleul->email }}
                </div>
                <div class="contact-item">
                    <strong>Téléphone :</strong> {{ $formData['telephone'] ?? 'Non renseigné' }}
                </div>
                @if(isset($formData['ville']))
                    <div class="contact-item">
                        <strong>Ville :</strong> {{ $formData['ville'] }}
                    </div>
                @endif
            </div>
        </div>

        <div class="action-box">
            <h3>📞 Action recommandée</h3>
            <p>Contactez rapidement cette personne pour :</p>
            <ul>
                <li>Présenter nos formations adaptées à son profil</li>
                <li>L'accompagner dans son projet professionnel</li>
                <li>Répondre à ses questions sur les parcours disponibles</li>
            </ul>
        </div>

        <div class="text-center">
            <a href="{{ url('/commercial/parrainage') }}" class="cta-button">Voir le dossier complet</a>
        </div>

        <p class="signature">L'équipe {{ config('app.name') }}</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
        <p>
            <a href="#">Mentions légales</a>
            <a href="#">Confidentialité</a>
            <a href="#">Préférences</a>
        </p>
    </div>
</div>
</body>

</html>
