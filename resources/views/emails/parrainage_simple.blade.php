<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Parrainage - Wizi Learn</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
            background-color: #ffffff;
            color: #4a5568;
            line-height: 1.6;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
        }

        .header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 2px solid #feb823;
        }

        .logo {
            height: 45px;
            width: 100px;
        }

        .content {
            padding: 35px 25px;
        }

        .greeting {
            font-size: 17px;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .message {
            font-size: 15px;
            color: #4a5568;
            margin-bottom: 20px;
        }

        .highlight {
            color: #2d3748;
            font-weight: 600;
        }

        .confirmation-box {
            background: #f8f9fa;
            border-left: 3px solid #feb823;
            padding: 18px;
            margin: 20px 0;
        }

        .confirmation-title {
            font-size: 15px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .footer {
            padding: 25px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            background: #f8f9fa;
        }

        .signature {
            font-size: 15px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 12px;
        }

        .contact-info {
            font-size: 13px;
            color: #718096;
            line-height: 1.6;
        }

        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 20px 0;
        }

        @media only screen and (max-width: 600px) {
            .content {
                padding: 25px 20px;
            }

            .header {
                padding: 25px 20px;
            }

            .greeting {
                font-size: 16px;
            }

            .message {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- En-tête sobre -->
        <div style="text-align:center; margin-bottom: 20px;">
            <img style="width:auto; height: 50px;" src="{{ $message->embed(public_path('assets/logo_wizi.png')) }}"
                alt="logo Wizi Learn">
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <div class="greeting">
                Bonjour <span class="highlight">{{ $civilite }} {{ $prenom }}</span>,
            </div>

            <div class="message">
                Nous vous confirmons que votre demande de parrainage a bien été prise en compte et nous vous en
                remercions sincèrement.
            </div>

            <div class="confirmation-box">
                <div class="confirmation-title">Prochaines étapes</div>
                Votre conseiller dédié recontactera votre filleul dans les plus brefs délais pour l'accompagner dans son
                projet de formation.
            </div>

            <div class="message">
                Votre participation à notre programme de parrainage est précieuse et contribue à développer notre
                communauté d'apprentissage.
            </div>
        </div>

        <!-- Séparateur -->
        <div class="divider"></div>

        <!-- Pied de page -->
        <div class="footer">
            <div class="signature">À très bientôt</div>
            <div class="contact-info">
                <strong>Wizi Learn</strong><br>
                Tél. : 09 72 51 29 04<br>
                Email : contact@wizi-learn.com<br>
                Site : www.wizi-learn.com
            </div>
        </div>
    </div>
</body>

</html>
