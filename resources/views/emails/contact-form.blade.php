<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $data['subject'] }} - Wizi-Learn</title>
    <style type="text/css">
        /* Base Styles */

        * {
            font-family: monospace;
            font-size: 12px;
        }

        body,
        #bodyTable,
        #bodyCell {
            height: 100% !important;
            margin: 0;
            padding: 0;
            width: 100% !important;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f9f9f9;
        }

        table {
            border-collapse: collapse;
        }

        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        p {
            margin: 0 0 15px 0;
            padding: 0;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        a {
            color: #333333;
            text-decoration: underline;
        }

        a:hover {
            color: #000000;
        }

        /* Container */
        .container {
            max-width: 600px;
            width: 100% !important;
            margin: 30px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Header */
        .header {
            background-color: #ffffff;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .header h1 {
            color: #000000;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        /* Content */
        .content {
            padding: 30px;
        }

        h2 {
            color: #000000;
            font-size: 15px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: bold;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Info Box */
        .info-box {
            background-color: #f5f5f5;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #808080;
        }

        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #000000;
            min-width: 140px;
            flex-shrink: 0;
        }

        .value {
            color: #333333;
        }

        /* Message Box */
        .message-box {
            background-color: #f5f5f5;
            padding: 20px;
            border-left: 4px solid #808080;
            white-space: pre-wrap;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 25px 0;
            font-size: 13px;
            color: #666666;
            background-color: #f5f5f5;
            border-top: 1px solid #e0e0e0;
        }

        .footer p {
            margin: 5px 0;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-online {
            background-color: #f0f0f0;
            color: #38a169;
            border: 1px solid #38a169;
        }

        .badge-offline {
            background-color: #f0f0f0;
            color: #e53e3e;
            border: 1px solid #e53e3e;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                margin: 0;
                border: none;
            }

            .content {
                padding: 20px !important;
            }

            .header h1 {
                font-size: 20px !important;
            }

            .header {
                padding: 20px 15px !important;
            }

            .info-item {
                flex-direction: column;
            }

            .label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <center>
        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
            <tr>
                <td align="center" valign="top" id="bodyCell">
                    <table border="0" cellpadding="0" cellspacing="0" class="container">
                        <!-- Header -->
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
                                    <tr>
                                        <td align="center">
                                            <h2 style="color: #cf2424; font-size: 24px; margin: 0; font-weight: bold;">
                                                Nouvelle demande d'aide</h2>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="content">
                                    <tr>
                                        <td valign="top">
                                            <h2 style="margin-left: 15px;">Détails de la demande</h2>

                                            <div class="info-box">
                                                <div class="info-item">
                                                    <span class="label">Expéditeur:</span>
                                                    <span class="value">{{ $data['email'] }}</span>
                                                </div>

                                                <div class="info-item">
                                                    <span class="label">Sujet:</span>
                                                    <span class="value">{{ $data['subject'] }}</span>
                                                </div>
                                                <div class="info-item">
                                                    <span class="label">Type de problème:</span>
                                                    <span class="value">{{ $data['problem_type'] }}</span>
                                                </div>
                                                <div class="info-item">
                                                    <span class="label">Date:</span>
                                                    <span class="value">{{ now()->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>

                                            @if($data['isRegisteredUser'])
                                                <h2>Informations utilisateur</h2>
                                                <div class="info-box">
                                                    <div class="info-item">
                                                        <span class="label">Nom:</span>
                                                        <span class="value">{{ $data['userInfo']['name'] }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="label">Rôle:</span>
                                                        <span class="value">{{ $data['userInfo']['role'] }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="label">Adresse:</span>
                                                        <span
                                                            class="value">{{ $data['userInfo']['adresse'] ?? 'Non renseignée' }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="label">Dernière connexion:</span>
                                                        <span class="value">{{ $data['userInfo']['last_login'] }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="label">Statut:</span>
                                                        <span class="value">
                                                            @if($data['userInfo']['is_online'] === 'En ligne')
                                                                <span class="badge badge-online">En ligne</span>
                                                            @else
                                                                <span class="badge badge-offline">Hors ligne</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            <h2 style="margin-left: 15px;">Message</h2>
                                            <div class="message-box">
                                                {{ $data['messageContent'] }}
                                            </div>

                                            @if (!empty($data['attachments']))
                                                <h2 class="">Pièces jointes</h2>
                                                <div class="info-box">
                                                    @foreach ($data['attachments'] as $attachment)
                                                        <div class="info-item">
                                                            <span class="label">Fichier:</span>
                                                            <span class="value">
                                                                <a href="{{ asset('storage/' . $attachment['path']) }}"
                                                                    target="_blank">
                                                                    {{ $attachment['name'] }} ({{ $attachment['size'] }} Ko)
                                                                </a>
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="footer">
                                    <tr>
                                        <td align="center">
                                            <p>Cet email a été envoyé depuis le formulaire de contact de Wizi-Learn.</p>
                                            <p>© {{ date('Y') }} Wizi-Learn. Tous droits réservés.</p>
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