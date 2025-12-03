<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #FF6B35;
        }
        .message {
            background: #f9f9f9;
            padding: 20px;
            border-left: 4px solid #F7931E;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #FF6B35;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Wizi Learn</h1>
        </div>
        <div class="content">
            <div class="greeting">
                Bonjour {{ $recipientName }},
            </div>
            <div class="message">
                {!! nl2br(e($messageContent)) !!}
            </div>
            <p style="margin-top: 30px; color: #666;">
                Cordialement,<br>
                <strong>L'équipe Wizi Learn</strong>
            </p>
        </div>
        <div class="footer">
            <p>
                Ceci est un email automatique, merci de ne pas y répondre.<br>
                <a href="https://www.wizi-learn.com">www.wizi-learn.com</a>
            </p>
        </div>
    </div>
</body>
</html>
