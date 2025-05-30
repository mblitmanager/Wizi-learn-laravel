<!DOCTYPE html>
<html>

<head>
    <title>Confirmation d'inscription</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .highlight {
            color: #3498db;
            font-weight: 600;
        }

        .formation-card {
            background-color: #f8fafc;
            border-left: 4px solid #3498db;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .formation-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white !important;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #7f8c8d;
        }

        .parrain-info {
            background-color: #e8f4fc;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- Remplacez par votre logo -->
            <img src={{ asset('assets/logo_wizi.png') }} alt="Logo QuizPro" class="logo" />

            <h1>Bienvenue dans notre communauté, <span class="highlight">{{ $filleul->stagiaire->prenom }}</span> !</h1>
        </div>

        <p>Félicitations pour votre inscription à notre formation. Vous avez fait le premier pas vers l'acquisition de compétences qui boosteront votre carrière.</p>

        <div class="formation-card">
            <div class="formation-title">{{ $formation->titre }}</div>
            <p>{!! Str::limit($formation->description, 150) !!}}</p>
            <p><strong>Durée:</strong> {{ $formation->duree }}</p>
            @if($formation->certification)
            <p><strong>Certification:</strong> {{ $formation->certification }}</p>
            @endif
        </div>

        <div class="parrain-info">
            <h3>Votre parrain</h3>
            <p>Vous avez été parrainé par <strong>{{ $parrain->name }}</strong>, qui fait déjà partie de notre communauté.</p>
            <p>N'hésitez pas à le contacter pour échanger sur votre formation.</p>
        </div>

        <p>Voici les prochaines étapes :</p>
        <ul>
            <li>Vous recevrez sous peu un email avec vos identifiants de connexion</li>
            <li>Notre équipe prendra contact avec vous pour finaliser votre inscription</li>
            <li>Préparez-vous pour le début de votre formation</li>
        </ul>

        <a href="{{ config('app.url') }}" class="btn">Accéder à votre espace</a>

        <p>Si vous avez des questions, notre équipe est à votre disposition à <a href="mailto:support@example.com">support@example.com</a> ou au 01 23 45 67 89.</p>

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