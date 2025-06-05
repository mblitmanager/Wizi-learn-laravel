<!-- <!DOCTYPE html>
<html>

<head>
    <title>Nouveau quiz disponible</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #3D9BE9;
            padding: 25px;
            text-align: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        .tagline {
            font-size: 16px;
            color: white;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-image {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-bottom: 4px solid #FEB823;
        }

        .content {
            padding: 25px;
        }

        .main-title {
            font-size: 22px;
            color: #A55E6E;
            text-align: center;
            margin: 0 0 20px 0;
        }

        .cta-button {
            display: block;
            background-color: #FEB823;
            color: white !important;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            text-align: center;
            margin: 30px auto;
            width: fit-content;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(254, 184, 35, 0.3);
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background-color: #e6a520;
            transform: translateY(-2px);
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            font-size: 14px;
            color: #9392BE;
        }

        .social {
            color: #3D9BE9;
            font-weight: bold;
            margin: 15px 0;
        }

        .address {
            font-size: 12px;
            margin-top: 15px;
            line-height: 1.4;
        }

        .unsubscribe {
            font-size: 12px;
            margin-top: 20px;
        }

        .unsubscribe a {
            color: #9392BE;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">QuizFormation</div>
            <div class="tagline">Apprendre en s'amusant</div>
        </div>

        <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80"
            alt="Étudiante souriante devant son ordinateur"
            class="hero-image">

        <div class="content">
            <h2 class="main-title">Nouveau défi disponible !</h2>

            <p>Bonjour,</p>

            <p>Nous venons de publier un nouveau quiz passionnant :</p>

            <h3 style="color: #A55E6E; text-align: center; margin: 20px 0;">« {{ $quiz->titre }} »</h3>

            <p style="text-align: center; font-style: italic;">"{{ $quiz->description }}"</p>

            <p>Comme notre étudiante en photo, relevez le défi et testez vos connaissances !</p>

            <a href="{{ route('quiz.show', $quiz->id) }}" class="cta-button">
                ▶ Commencer le quiz maintenant
            </a>

            <p style="text-align: center;">
                <small>Temps estimé : seulement 5 minutes</small>
            </p>
        </div>

        <div class="footer">
            <div class="social">Rejoignez 10,000 apprenants satisfaits</div>
            <div>#QuizFormation #Apprentissage</div>

            <div class="address">
                © 2023 QuizFormation<br>
                123 Rue du Savoir, Paris 75000
            </div>

            <div class="unsubscribe">
                <a href="#">Se désabonner</a> | <a href="#">Préférences</a>
            </div>
        </div>
    </div>
</body>

</html> -->

<!DOCTYPE html>
<html>

<head>
    <title>Nouveau quiz disponible</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            border-radius: 4px;
            overflow: hidden;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            position: relative;
            overflow: hidden;
            color: white;
            padding: 40px;
            box-sizing: border-box;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .container::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .container::after {
            content: "";
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .header {
            padding: 0;
            text-align: left;
        }

        .logo-img {
            height: 50px;
            width: auto;
            margin-bottom: 10px;
        }

        .tagline {
            font-size: 3rem;
            color: #f9f9f9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transform: rotate(-5deg);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            padding: 5px 10px;
            font-weight: bold;
            text-align: center;
        }

        .hero-image {
            width: 60%;
            object-fit: cover;
        }

        .content {
            padding: 25px;
        }

        .main-title {
            font-size: 20px;
            color: #fff;
            text-align: center;
            margin: 0 0 20px 0;
            font-weight: 600;
        }

        .cta-button {
            display: block;
            background-color: #ffc100;
            color: white !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            text-align: center;
            margin: 30px auto;
            width: fit-content;
            font-size: 15px;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: #d3a007;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            font-size: 12px;
            color: #666666;
            border-top: 1px solid #eeeeee;
        }

        .social {
            color: #3d9be9;
            margin: 15px 0;
            font-size: 13px;
        }

        .address {
            margin-top: 15px;
            line-height: 1.5;
            color: #9392be;
        }

        .unsubscribe {
            margin-top: 20px;
            font-size: 11px;
        }

        .unsubscribe a {
            color: #9392be;
            text-decoration: none;
        }

        .quiz-title {
            color: #f5a904;
            text-align: center;
            margin: 10px 0;
            font-size: 50px;
            font-weight: 800;
        }

        .img-container {
            height: 330px;
            width: 530px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- Logo du site -->
            <img src="data:image/png;base64,{{ $logoData }}" alt="Logo" class="logo-img" />

        </div>

        <div class="tagline">Développez vos compétences</div>

        <!-- Image professionnelle -->
        <div class="img-container">
            <img src="cid:online" alt="Professionnels en formation" class="hero-image" />
        </div>

        <div class="content">
            <h2 class="main-title">Nouveau Quiz disponible</h2>

            <p>Bonjour,</p>

            <p>
                Une nouvelle évaluation de compétences vient d'être publiée dans votre
                espace de formation :
            </p>

            <h3 class="quiz-title">{{ $quiz->titre }}</h3>

            <p style="text-align: center; color: #ffc100">
                {{ $quiz->description }}
            </p>

            <p>
                Cette évaluation vous permettra de mesurer vos acquis et progresser
                dans votre parcours professionnel.
            </p>

            <a href="{{ route('quiz.show', $quiz->id) }}" class="cta-button">
                Accéder à l'évaluation
            </a>

            <p style="text-align: center; font-size: 13px; color: #d3a007">
                Durée estimée : {{ $quiz->duree }}
            </p>
        </div>

        <div class="footer">
            <div class="social">Développez vos compétences avec QuizPro</div>

            <div class="address">
                © 2025 Wizi-learn - Tous droits réservés<br />
                123 Avenue des Professionnels, 75000 Paris<br />
                <a href="https://votre-domaine.com" style="color: #3d9be9">www.votre-domaine.com</a>
            </div>

            <div class="unsubscribe">
                <a href="#">Gérer vos préférences</a> | <a href="#">Se désabonner</a>
            </div>
        </div>
    </div>
</body>

</html>