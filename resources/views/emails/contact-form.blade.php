<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $data['subject'] }} - Wizi-Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style type="text/css">
        /* Base Styles */
        body, #bodyTable, #bodyCell {
            height: 100% !important;
            margin: 0;
            padding: 0;
            width: 100% !important;
            font-family: 'Poppins', 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #feb823;
            background-color: #f7fafc;
        }
        table {
            border-collapse: collapse;
        }
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        p {
            margin: 0;
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
            color: #feb823;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        a:hover {
            color: #fe8223;
        }

        /* Container */
        .container {
            max-width: 600px;
            width: 100% !important;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #feb823 0%, rgba(254, 184, 35, 0.78) 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            display: inline-block;
        }
        .logo-icon {
            vertical-align: middle;
            margin-right: 8px;
        }

        /* Content */
        .content {
            padding: 40px;
        }
        h2 {
            color: #2d3748;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 25px;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #feb823, #feb823);
            border-radius: 3px;
        }
        h3 {
            color: #feb823;
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        /* Info Box */
        .info-box {
            background-color: #f8fafc;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #fc6310;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #edf2f7;
            display: flex;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 500;
            color: #4a5568;
            min-width: 140px;
            flex-shrink: 0;
        }
        .value {
            color: #2d3748;
            font-weight: 400;
        }
        .user-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .registered {
            background-color: #feb823;
            color: #fff5f5;
        }
        .unregistered {
            background-color: #fff5f5;
            color: #e53e3e;
        }

        /* Message Box */
        .message-box {
            background-color: #f8fafc;
            border-left: 4px solid #feb823;
            padding: 20px;
            border-radius: 8px;
            white-space: pre-wrap;
            line-height: 1.7;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        /* Attachments */
        .attachments-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .attachments-list li {
            background-color: #f8fafc;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .attachments-list li:hover {
            border-color: #c3dafe;
            transform: translateY(-2px);
        }
        .attachment-link {
            display: flex;
            align-items: center;
            color: #4a5568;
            font-weight: 500;
        }
        .attachment-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            color: #feb823;
        }
        .attachment-link span {
            flex-grow: 1;
        }
        .attachment-size {
            color: #718096;
            font-size: 13px;
            font-weight: 400;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 25px 0;
            font-size: 13px;
            color: #718096;
            background-color: #f8fafc;
        }
        .footer p {
            margin: 5px 0;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-link {
            display: inline-block;
            margin: 0 8px;
            color: #718096;
            font-size: 16px;
        }
        .social-link:hover {
            color: #feb823;
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
            background-color: #f0fff4;
            color: #38a169;
        }
        .badge-offline {
            background-color: #fff5f5;
            color: #e53e3e;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                border-radius: 0;
            }
            .content {
                padding: 25px !important;
            }
            .header h1 {
                font-size: 24px !important;
            }
            .header {
                padding: 30px 20px !important;
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
                                        <div class="logo">
                                            <span class="logo-icon">✨</span>Wizi-Learn
                                        </div>
                                        <h1>Nouvelle demande de contact</h1>
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
                                        <h2>Détails de la demande</h2>

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
                                            <h3>Informations utilisateur</h3>
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
                                                    <span class="value">{{ $data['userInfo']['adresse'] ?? 'Non renseignée' }}</span>
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

                                        <h3>Message</h3>
                                        <div class="message-box">
                                            {{ $data['messageContent'] }}
                                        </div>

                                        @if (!empty($data['attachments']))
                                            <h3>Pièces jointes</h3>
                                            <ul class="attachments-list">
                                                @foreach ($data['attachments'] as $attachment)
                                                    <li>
                                                        @php
                                                            $isImage = in_array(strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                                                        @endphp

                                                        @if ($isImage)
                                                            <div style="margin-bottom: 15px; border-radius: 6px; overflow: hidden;">
                                                                <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}" style="max-width: 100%; height: auto; display: block; border-radius: 6px;">
                                                            </div>
                                                        @endif

                                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="attachment-link">
                                                            <svg class="attachment-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                            </svg>
                                                            <span>{{ $attachment['name'] }}</span>
                                                            <span class="attachment-size">({{ $attachment['size'] }} Ko)</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
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
                                        <div class="social-links">
                                            <a href="#" class="social-link">Facebook</a>
                                            <a href="#" class="social-link">Twitter</a>
                                            <a href="#" class="social-link">LinkedIn</a>
                                        </div>
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
