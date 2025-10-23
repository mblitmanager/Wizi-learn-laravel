<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirmation de Parrainage - Wizi Learn</title>
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
            margin: 0 25px;
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
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
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
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        }

        .contact-info {
            font-size: 13px;
            color: #718096;
            line-height: 1.6;
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        }

        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 20px 0;
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
                margin: 10px 25px !important;
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
                                    <!-- Logo Wizi Learn -->
                                    <img src="{{ $message->embed(public_path('assets/aopia.png')) }}"
                                        alt="Logo Wizi Learn" class="logo"
                                        style="height: 50px; width: auto; margin: 0 15px; display: inline-block; vertical-align: middle;" />

                                    <!-- Logo NS Conseil -->
                                    <img src="{{ $message->embed(public_path('assets/like.png')) }}"
                                        alt="Logo NS Conseil" class="logo"
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
                                                style="color: #2d3748; font-weight: 600;">{{ $nomComplet }}</span>,
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="message"
                                            style="font-size: 15px; color: #4a5568; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                            Nous vous confirmons que votre demande de parrainage a bien été prise en
                                            compte et nous vous en
                                            remercions sincèrement.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding: 10px; !important;">
                                            <table class="confirmation-box" border="0" cellpadding="0"
                                                cellspacing="0" width="100%"
                                                style="background: #f8f9fa; border-left: 3px solid #feb823; padding: 18px; margin: 20px 0;">
                                                <tr>
                                                    <td>
                                                        <div class="confirmation-title"
                                                            style="font-size: 15px; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                                            Prochaines étapes
                                                        </div>
                                                        Votre conseiller dédié recontactera votre filleul dans les plus
                                                        brefs délais pour l'accompagner dans son
                                                        projet de formation.
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="message"
                                            style="font-size: 15px; color: #4a5568; margin-bottom: 20px; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">
                                            Votre participation à notre programme de parrainage est précieuse et
                                            contribue à développer notre
                                            communauté d'apprentissage.
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Divider -->
                        <tr>
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td class="divider" style="height: 1px; background: #e2e8f0; margin: 20px 0;">
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
                                            style="width: 100%; text-align: center; padding-top: 15px;" colspan="2">
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
