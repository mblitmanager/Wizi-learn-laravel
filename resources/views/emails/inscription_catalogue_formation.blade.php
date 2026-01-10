@if ($isPoleRelation)
    <!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Nouvelle demande d'inscription - Wizi Learn</title>
        <!--[if !mso]><!-->
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        </style>
        <!--<![endif]-->
        <style type="text/css">
            /* Styles inline pour la compatibilité email */
            body {
                margin: 0;
                padding: 0;
                width: 100% !important;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
                background-color: #ffffff;
                color: #4a5568;
                line-height: 1.6;
            }

            table {
                border-collapse: collapse;
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
            }

            img {
                border: 0;
                height: auto;
                line-height: 100%;
                outline: none;
                text-decoration: none;
                -ms-interpolation-mode: bicubic;
            }

            .container {
                max-width: 600px;
                width: 100%;
                margin: 0 auto;
            }

            .header {
                padding: 30px 20px;
                text-align: center;
                border-bottom: 2px solid #feb823;
            }

            .logo-container {
                display: inline-block;
                text-align: center;
            }

            .logo {
                height: 50px;
                width: auto;
                margin: 0 15px;
                display: inline-block;
                vertical-align: middle;
            }

            .content {
                padding: 35px 25px;
            }

            .greeting {
                font-size: 17px;
                color: #2d3748;
                margin-bottom: 20px;
                font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
            }

            .message {
                font-size: 15px;
                color: #4a5568;
                margin-bottom: 20px;
                font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
            }

            .highlight {
                color: #2d3748;
                font-weight: 600;
            }

            .fiche-demande {
                background: #f8f9fa;
                border-left: 3px solid #feb823;
                padding: 18px;
                margin: 20px 0;
            }

            .fiche-demande b {
                display: inline-block;
                width: 120px;
            }

            .fiche-demande span {
                display: inline-block;
                width: calc(100% - 130px);
            }

            .formation-title {
                font-size: 16px;
                font-weight: 600;
                color: #2d3748;
                margin: 15px 0 10px 0;
            }

            .bullet-points {
                margin: 15px 0;
            }

            .bullet-points div {
                margin-bottom: 8px;
                position: relative;
                padding-left: 15px;
            }

            .bullet-points div:before {
                content: "•";
                position: absolute;
                left: 0;
                color: #feb823;
            }

            .footer {
                padding: 25px;
                text-align: center;
                border-top: 1px solid #e2e8f0;
                background: #f8f9fa;
            }

            .footer-container {
                width: 100%;
                max-width: 600px;
            }

            .footer-left,
            .footer-right {
                width: 50%;
                vertical-align: top;
            }

            .footer-center {
                width: 100%;
                text-align: center;
                padding-top: 15px;
            }

            @media only screen and (max-width: 600px) {
                .container {
                    width: 100% !important;
                }

                .content {
                    padding: 25px 20px !important;
                }

                .header {
                    padding: 25px 20px !important;
                }

                .greeting {
                    font-size: 16px !important;
                }

                .message {
                    font-size: 14px !important;
                }

                .footer-left,
                .footer-right {
                    width: 100% !important;
                    display: block !important;
                    text-align: center !important;
                    padding-bottom: 15px;
                }

                .logo {
                    margin: 10px 15px !important;
                    display: block !important;
                }
            }
        </style>
    </head>

    <body style="margin: 0; padding: 0; background-color: #ffffff;">
        <center>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                <tr>
                    <td align="center">
                        <table class="container" border="0" cellpadding="0" cellspacing="0" width="600">
                            <!-- Header avec deux logos -->
                            <tr>
                                <td class="header"
                                    style="padding: 30px 20px; text-align: center; border-bottom: 2px solid #feb823;">
                                    <div class="logo-container">
                                        <!-- Logo AOPIA -->
                                        <img src="{{ $message->embed(public_path('assets/aopia.png')) }}"
                                            alt="Logo AOPIA" class="logo"
                                            style="height: 50px; width: auto; margin: 0 15px; display: inline-block; vertical-align: middle;" />

                                        <!-- Logo Like Formation -->
                                        <img src="{{ $message->embed(public_path('assets/like.png')) }}"
                                            alt="Logo Like Formation" class="logo"
                                            style="height: 50px; width: auto; margin: 0 15px; display: inline-block; vertical-align: middle;" />
                                    </div>
                                </td>
                            </tr>

                            <!-- Content -->
                            <tr>
                                <td class="content" style="padding: 35px 25px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td class="greeting"
                                                style="font-size: 17px; color: #2d3748; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                                Bonjour,
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="message"
                                                style="font-size: 15px; color: #4a5568; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                                <strong>{{ $stagiaire->prenom }} {{ $stagiaire->user->name }}</strong>
                                                souhaite s'inscrire à une formation.
                                            </td>
                                        </tr>

                                        <!-- Fiche de demande -->
                                        <tr>
                                            <td style="padding: 10px; !important;">
                                                <table class="fiche-demande" border="0" cellpadding="0"
                                                    cellspacing="0" width="100%">
                                                    <tr>
                                                        <td><b>Civilité:</b>
                                                            <span>{{ $stagiaire->civilite ?? 'Non renseigné' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Nom:</b>
                                                            <span>{{ $stagiaire->nom ?? 'Non renseigné' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Prénom:</b>
                                                            <span>{{ $stagiaire->prenom ?? 'Non renseigné' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Téléphone:</b>
                                                            <span>{{ $stagiaire->telephone ?? 'Non renseigné' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Email:</b>
                                                            <span>{{ $stagiaire->user->email ?? 'Non renseigné' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Formation:</b>
                                                            <span>{{ $catalogueFormation->titre }}</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="formation-title">
                                                    {{ strtoupper($catalogueFormation->titre) }}</div>
                                                <p><strong>Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
                                                @if ($catalogueFormation->tarif)
                                                    <p><strong>Tarif :</strong>
                                                        {{ number_format($catalogueFormation->tarif, 0, ',', ' ') }} €
                                                    </p>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="bullet-points">
                                                    <div>Profitez d'une formation accessible à tous les niveaux, de
                                                        l'initiation des débutants jusqu'au perfectionnement</div>
                                                    <div>Assurez-vous d'avoir à disposition le matériel informatique
                                                        approprié</div>
                                                    <div>Utilisez l'environnement Windows avec des connaissances de base
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="message"
                                                style="font-size: 15px; color: #4a5568; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                                Merci de prendre contact avec le stagiaire pour finaliser cette
                                                inscription.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td class="footer"
                                    style="padding: 25px; text-align: center; border-top: 1px solid #e2e8f0; background: #f8f9fa;">
                                    <table class="footer-container" border="0" cellpadding="0" cellspacing="0"
                                        width="100%">
                                        <tr>
                                            <td class="footer-left"
                                                style="width: 50%; vertical-align: top; text-align: left;">
                                                <div style="font-size: 13px; color: #718096; line-height: 1.6;">
                                                    <strong>Contact AOPIA</strong><br />
                                                    contact@aopia.fr<br />
                                                    Tél : 09 72 51 29 04
                                                </div>
                                            </td>
                                            <td class="footer-right"
                                                style="width: 50%; vertical-align: top; text-align: right;">
                                                <div style="font-size: 13px; color: #718096; line-height: 1.6;">
                                                    <strong>Contact Like Formation</strong><br />
                                                    likeformation@ns-conseil.com<br />
                                                    Tél : 09 74 77 59 20
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="footer-center"
                                                style="width: 100%; text-align: center; padding-top: 15px;"
                                                colspan="2">
                                                <div style="font-size: 12px; color: #718096; line-height: 1.6;">
                                                    AOPIA et Like Formation sont des marques de NS-CONSEIL<br />
                                                    SIREN : 519 408 140 - Siège Social : 73 Av. du Château d'Eau 33700
                                                    MERIGNAC
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </center>
    </body>

    </html>
@else
    <!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Confirmation de demande - Wizi Learn</title>
        <!--[if !mso]><!-->
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        </style>
        <!--<![endif]-->
        <style type="text/css">
            /* Styles inline pour la compatibilité email */
            body {
                margin: 0;
                padding: 0;
                width: 100% !important;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
                background-color: #ffffff;
                color: #4a5568;
                line-height: 1.6;
            }

            table {
                border-collapse: collapse;
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
            }

            img {
                border: 0;
                height: auto;
                line-height: 100%;
                outline: none;
                text-decoration: none;
                -ms-interpolation-mode: bicubic;
            }

            .container {
                max-width: 600px;
                width: 100%;
                margin: 0 auto;
            }

            .header {
                padding: 30px 20px;
                text-align: center;
                border-bottom: 2px solid #feb823;
            }

            .logo-container {
                display: inline-block;
                text-align: center;
            }

            .logo {
                height: 50px;
                width: auto;
                margin: 0 15px;
                display: inline-block;
                vertical-align: middle;
            }

            .content {
                padding: 35px 25px;
            }

            .greeting {
                font-size: 17px;
                color: #2d3748;
                margin-bottom: 20px;
                font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
            }

            .message {
                font-size: 15px;
                color: #4a5568;
                margin-bottom: 20px;
                font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
            }

            .highlight {
                color: #2d3748;
                font-weight: 600;
            }

            .formation-title {
                font-size: 16px;
                font-weight: 600;
                color: #2d3748;
                margin: 15px 0 10px 0;
            }

            .bullet-points {
                margin: 15px 0;
            }

            .bullet-points div {
                margin-bottom: 8px;
                position: relative;
                padding-left: 15px;
            }

            .bullet-points div:before {
                content: "•";
                position: absolute;
                left: 0;
                color: #feb823;
            }

            .contact-box {
                background: #f8f9fa;
                border-left: 3px solid #feb823;
                padding: 18px;
                margin: 20px 0;
                font-size: 14px;
            }

            .footer {
                padding: 25px;
                text-align: center;
                border-top: 1px solid #e2e8f0;
                background: #f8f9fa;
            }

            .footer-container {
                width: 100%;
                max-width: 600px;
            }

            .footer-left,
            .footer-right {
                width: 50%;
                vertical-align: top;
            }

            .footer-center {
                width: 100%;
                text-align: center;
                padding-top: 15px;
            }

            @media only screen and (max-width: 600px) {
                .container {
                    width: 100% !important;
                }

                .content {
                    padding: 25px 20px !important;
                }

                .header {
                    padding: 25px 20px !important;
                }

                .greeting {
                    font-size: 16px !important;
                }

                .message {
                    font-size: 14px !important;
                }

                .footer-left,
                .footer-right {
                    width: 100% !important;
                    display: block !important;
                    text-align: center !important;
                    padding-bottom: 15px;
                }

                .logo {
                    margin: 10px 15px !important;
                    display: block !important;
                }
            }
        </style>
    </head>

    <body style="margin: 0; padding: 0; background-color: #ffffff;">
        <center>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                <tr>
                    <td align="center">
                        <table class="container" border="0" cellpadding="0" cellspacing="0" width="600">
                            <!-- Header avec deux logos -->
                            <tr>
                                <td class="header"
                                    style="padding: 30px 20px; text-align: center; border-bottom: 2px solid #feb823;">
                                    <div class="logo-container">
                                        <!-- Logo AOPIA -->
                                        <img src="{{ $message->embed(public_path('assets/aopia.png')) }}"
                                            alt="Logo AOPIA" class="logo"
                                            style="height: 50px; width: auto; margin: 0 15px; display: inline-block; vertical-align: middle;" />

                                        <!-- Logo Like Formation -->
                                        <img src="{{ $message->embed(public_path('assets/like.png')) }}"
                                            alt="Logo Like Formation" class="logo"
                                            style="height: 50px; width: auto; margin: 0 15px; display: inline-block; vertical-align: middle;" />
                                    </div>
                                </td>
                            </tr>

                            <!-- Content -->
                            <tr>
                                <td class="content" style="padding: 35px 25px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td class="greeting"
                                                style="font-size: 17px; color: #2d3748; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                                Bonjour <span class="highlight"
                                                    style="color: #2d3748; font-weight: 600;">{{ $stagiaire->prenom }} {{ $stagiaire->user->name }}</span>,
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="message"
                                                style="font-size: 15px; color: #4a5568; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                                Nous avons bien reçu votre demande d'inscription à la formation suivante
                                                :
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="formation-title">
                                                    {{ strtoupper($catalogueFormation->titre) }}</div>
                                                <p><strong>Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
                                                @if ($catalogueFormation->tarif)
                                                    <p><strong>Tarif :</strong>
                                                        {{ number_format($catalogueFormation->tarif, 0, ',', ' ') }} €
                                                    </p>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="bullet-points">
                                                    <div>Profitez d'une formation accessible à tous les niveaux, de
                                                        l'initiation des débutants jusqu'au perfectionnement</div>
                                                    <div>Assurez-vous d'avoir à disposition le matériel informatique
                                                        approprié</div>
                                                    <div>Utilisez l'environnement Windows avec des connaissances de base
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <table class="contact-box" border="0" cellpadding="0"
                                                    cellspacing="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            Pour toute question : <a
                                                                href="mailto:contact@wizi-learn.com"
                                                                style="color: #feb823; text-decoration: none;">contact@wizi-learn.com</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="message"
                                                style="font-size: 15px; color: #4a5568; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                                Un conseiller de votre pôle Relation Client vous contactera
                                                prochainement pour finaliser votre inscription.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td class="footer"
                                    style="padding: 25px; text-align: center; border-top: 1px solid #e2e8f0; background: #f8f9fa;">
                                    <table class="footer-container" border="0" cellpadding="0" cellspacing="0"
                                        width="100%">
                                        <tr>
                                            <td class="footer-left"
                                                style="width: 50%; vertical-align: top; text-align: left;">
                                                <div style="font-size: 13px; color: #718096; line-height: 1.6;">
                                                    <strong>Contact AOPIA</strong><br />
                                                    contact@aopia.fr<br />
                                                    Tél : 09 72 51 29 04
                                                </div>
                                            </td>
                                            <td class="footer-right"
                                                style="width: 50%; vertical-align: top; text-align: right;">
                                                <div style="font-size: 13px; color: #718096; line-height: 1.6;">
                                                    <strong>Contact Like Formation</strong><br />
                                                    likeformation@ns-conseil.com<br />
                                                    Tél : 09 74 77 59 20
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="footer-center"
                                                style="width: 100%; text-align: center; padding-top: 15px;"
                                                colspan="2">
                                                <div style="font-size: 12px; color: #718096; line-height: 1.6;">
                                                    AOPIA et Like Formation sont des marques de NS-CONSEIL<br />
                                                    SIREN : 519 408 140 - Siège Social : 73 Av. du Château d'Eau 33700
                                                    MERIGNAC
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </center>
    </body>

    </html>
@endif
