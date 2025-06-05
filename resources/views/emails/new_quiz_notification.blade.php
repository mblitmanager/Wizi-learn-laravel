<!DOCTYPE html>
<html>
<head>
    <title>NOUVEAU QUIZ EXCLUSIF</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@800;900&family=Poppins:wght@600;700&display=swap");

        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .container {
            max-width: 650px;
            margin: 20px auto;
            overflow: hidden;
            position: relative;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .sale-banner {
            background: linear-gradient(135deg, #fb3402, #f9790c);
            padding: 31px 15px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .sale-banner::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #ffcc00, #ff4e4e, #f96c0a);
        }

        .sale-sticker {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #011f43;
            color: #ffcc00;
            padding: 10px 15px;
            font-size: 1.2rem;
            font-weight: 900;
            transform: rotate(15deg);
            border-radius: 5px;
            font-family: "Montserrat", sans-serif;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .main-headline {
            font-family: "Montserrat", sans-serif;
            font-size: 4.5rem;
            font-weight: 900;
            color: #fff;
            margin: 10px 0;
            line-height: 1;
            text-transform: uppercase;
            text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.2);
            letter-spacing: -2px;
        }

        .sub-headline {
            font-size: 2.8rem;
            font-weight: 800;
            color: #ffcc00;
            margin: 5px 0 20px;
            text-transform: uppercase;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.2);
        }

        .offer-text {
            font-size: 1.8rem;
            color: #fff;
            font-weight: 700;
            margin: 20px 0;
            padding: 15px;
            background: rgba(0, 0, 0, 0.2);
            display: inline-block;
            border-radius: 8px;
        }

        .quiz-image {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-bottom: 8px solid #ffcc00;
        }

        .content {
            padding: 30px;
            background: #fff;
            text-align: center;
        }

        .quiz-title {
            font-size: 3rem;
            color: #f60707;
            font-weight: 800;
            margin: 0 0 15px;
            text-transform: uppercase;
            line-height: 1;
        }

        .quiz-description {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(to right, #ff4e4e, #f60707);
            color: white !important;
            padding: 6px 15px;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 50px;
            margin: 20px 0;
            box-shadow: 0 8px 20px rgba(246, 7, 7, 0.4);
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(246, 7, 7, 0.5);
        }

        .duration-badge {
            display: inline-block;
            background: #011f43;
            color: #ffcc00;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 700;
            margin-top: 15px;
            font-size: 1.1rem;
        }

        .footer {
            padding: 20px;
            background: #000;
            color: #fff;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer a {
            color: #ffcc00;
            text-decoration: none;
            margin: 0 10px;
        }

        .burst {
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(255, 204, 0, 0.2);
            border-radius: 50%;
            top: -75px;
            right: -75px;
        }

        .burst::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 204, 0, 0.15);
            border-radius: 50%;
            transform: scale(0.7);
        }
    </style>
</head>

<body>
<div class="container">
    <div class="burst"></div>

    <div class="sale-banner">
        <div class="sale-sticker">EXCLUSIF</div>
        <div class="main-headline">Nouveau</div>
        <div class="sub-headline">Quiz Disponible</div>
    </div>

    <img src="{{$message->embed($onlineImagePath)}}" alt="Illustration du quiz" class="quiz-image"/>

    <div class="content">
        <h2 class="quiz-title">{{ $quiz->titre }}</h2>

        <p class="quiz-description">
            {{ $quiz->description }}<br/>
            Un défi passionnant vous attend pour évaluer vos compétences et
            connaissances.
        </p>

        <a href="{{ route('quiz.show', $quiz->id) }}" class="cta-button">
            Commencer le quiz
        </a>

        <div class="duration-badge">Durée: {{ $quiz->duree }}</div>
    </div>

    <div class="footer">
        <a href="#">Conditions</a> | <a href="#">Confidentialité</a> |
        <a href="#">Désabonnement</a>
    </div>
</div>
</body>
</html>
