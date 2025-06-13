<!DOCTYPE html>
<html>

<head>
    <title>NOUVEAU QUIZ EXCLUSIF</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f0f2f5;">
    <!-- Container principal -->
    <div style="max-width: 650px; margin: 20px auto; overflow: hidden; position: relative; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); background: #fff;">
        <!-- Bannière de vente -->
        <div style="background: linear-gradient(135deg, #fb3402, #f9790c); padding: 31px 15px; text-align: center; position: relative; overflow: hidden; border-top: 8px solid #ffcc00;">

            <!-- Titres -->
            <div style="font-family: Arial, sans-serif; font-size: 2.5rem; font-weight: bold; color: #fff; margin: 10px 0; line-height: 1; text-transform: uppercase; letter-spacing: -1px;">
                Nouveau
            </div>
            <div style="font-size: 1.8rem; font-weight: bold; color: #ffcc00; margin: 5px 0 20px; text-transform: uppercase;">
                Quiz Disponible
            </div>
        </div>

        <!-- Image du quiz -->
        <img src="{{ $message->embed($onlineImagePath) }}" alt="Illustration du quiz" style="width: 100%; max-height: 300px; object-fit: contain; border-bottom: 8px solid #ffcc00;" />

        <!-- Contenu -->
        <div style="padding: 30px; text-align: center;">
            <h2 style="font-size: 1.8rem; color: #f60707; font-weight: bold; margin: 0 0 15px; text-transform: uppercase; line-height: 1;">
                {{ $quiz->titre }}
            </h2>

            <p style="font-size: 1rem; color: #555; margin-bottom: 30px; line-height: 1.6;">
                {{ $quiz->description }}<br />
                Un défi passionnant vous attend pour évaluer vos compétences et
                connaissances.
            </p>

            <!-- Bouton CTA -->
            <a href="{{ route('quiz.show', $quiz->id) }}" style="display: inline-block; background: #f60707; color: white !important; padding: 12px 30px; font-size: 1rem; font-weight: bold; text-decoration: none; border-radius: 50px; margin: 20px 0; border: none; cursor: pointer; text-transform: uppercase;">
                Commencer le quiz
            </a>

            <!-- Badge durée -->
            <div style="display: inline-block; background: #011f43; color: #ffcc00; padding: 8px 25px; border-radius: 50px; font-weight: bold; margin-top: 15px; font-size: 1.1rem;">
                Durée: {{ $quiz->duree }}
            </div>
        </div>

        <!-- Pied de page -->
        <div style="padding: 20px; background: #000; color: #fff; text-align: center; font-size: 0.9rem;">
            <a href="#" style="color: #ffcc00; text-decoration: none; margin: 0 10px;">Conditions</a> |
            <a href="#" style="color: #ffcc00; text-decoration: none; margin: 0 10px;">Confidentialité</a> |
            <a href="#" style="color: #ffcc00; text-decoration: none; margin: 0 10px;">Désabonnement</a>
        </div>
    </div>
</body>

</html>