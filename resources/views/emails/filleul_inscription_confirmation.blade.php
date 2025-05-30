<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Confirmation d'inscription</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@400;600&display=swap');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Poppins', sans-serif;
            background: #f1f3f6;
            color: #2c3e50;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(to bottom right, #f9f9ff, #eef1fa);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            background: linear-gradient(to right, #8e44ad, #3498db);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .header img {
            max-width: 120px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 22px;
            margin: 0;
            font-weight: 600;
        }

        .section {
            padding: 30px 25px;
            background-color: #ffffff;
        }

        .section p {
            margin: 10px 0;
            font-size: 15px;
            line-height: 1.6;
        }

        .highlight {
            color: #3498db;
            font-weight: 600;
        }

        .formation-card {
            background-color: #f0f4f8;
            border-left: 4px solid #3498db;
            border-radius: 6px;
            padding: 15px 20px;
            margin: 20px 0;
        }

        .formation-card h2 {
            margin: 0 0 8px;
            font-size: 17px;
            font-weight: 600;
            color: #2c3e50;
        }

        .parrain-card {
            background-color: #eaf6fd;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .parrain-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .btn {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin: 25px 0;
            text-align: center;
        }

        .footer {
            background-color: #2d3748;
            text-align: center;
            padding: 20px 10px;
            font-size: 12px;
            color: #7f8c8d;
        }

        .footer a {
            color: #7f8c8d;
            text-decoration: underline;
            margin: 0 5px;
        }

        ul {
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('assets/logo_wizi.png') }}" alt="Logo QuizPro">
            <h1>Bienvenue dans notre communauté, <span class="highlight">{{ $filleul->stagiaire->prenom }}</span> !</h1>
        </div>

        <div class="section">
            <p>Félicitations pour votre inscription à notre formation. Vous avez fait le premier pas vers l'acquisition de compétences qui boosteront votre carrière.</p>

            <div class="formation-card">
                <h2>{{ $formation->titre }}</h2>
                <p>{!! Str::limit($formation->description, 150) !!}</p>
                <p><strong>Durée :</strong> {{ $formation->duree }}</p>
                @if($formation->certification)
                <p><strong>Certification :</strong> {{ $formation->certification }}</p>
                @endif
            </div>

            <div class="parrain-card">
                <h3>🎓 Votre parrain</h3>
                <p>Vous avez été parrainé par <strong>{{ $parrain->name }}</strong>, déjà membre de notre communauté.</p>
                <p>N'hésitez pas à le contacter pour échanger sur votre parcours de formation.</p>
            </div>

            <p><strong>Voici les prochaines étapes :</strong></p>
            <ul>
                <li>Vous recevrez bientôt un email avec vos identifiants de connexion</li>
                <li>Notre équipe vous contactera pour finaliser votre inscription</li>
                <li>Préparez-vous à démarrer votre formation</li>
            </ul>

            <a href="{{ config('app.url') }}" class="btn">Accéder à votre espace</a>

            <p>Besoin d'aide ? Contactez-nous à <a href="mailto:support@example.com">support@example.com</a> ou au 01 23 45 67 89.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p>
                <a href="#">Mentions légales</a> |
                <a href="#">Politique de confidentialité</a>
            </p>
        </div>
    </div>
</body>

</html>