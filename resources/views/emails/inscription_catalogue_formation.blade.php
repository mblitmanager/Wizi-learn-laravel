@if ($isPoleRelation)
    <!DOCTYPE html>
    <html>

    <head>
        <style>
            * {
                font-family: monospace;
                font-size: 12px;
            }

            body {
                font-family: 'Helvetica Neue', Arial, sans-serif;
                line-height: 1.6;
                color: #333333;
                background-color: #ffffff;
                margin: 0;
                padding: 20px;
            }

            .email-content {
                max-width: 600px;
                margin: 0 auto;
            }

            .header {
                margin-bottom: 25px;
            }

            .header h1 {
                color: #2c3e50;
                font-size: 22px;
                margin-bottom: 5px;
            }

            .divider {
                border-top: 1px solid #e0e0e0;
                margin: 20px 0;
            }

            .formation-title {
                color: #2c3e50;
                font-size: 18px;
                margin: 15px 0 10px 0;
            }

            .btn {
                display: inline-block;
                background-color: #3498db;
                color: white;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 4px;
                font-size: 14px;
                margin-top: 15px;
            }

            .footer {
                margin-top: 30px;
                font-size: 12px;
                color: #7f8c8d;
                text-align: center;
            }

            .bullet-points {
                margin: 15px 0;
                padding-left: 5px;
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
                color: #3498db;
            }

            .fiche-demande {
                background: #f8f9fa;
                padding: 15px;
                margin: 15px 0;
                border-radius: 4px;
            }

            .fiche-demande b {
                display: inline-block;
                width: 120px;
            }

            .fiche-demande span {
                display: inline-block;
                width: calc(100% - 130px);
            }
        </style>
    </head>

    <body>
        <div class="email-content">
            <!-- Logo centré -->
            <div style="text-align:center; margin-bottom: 20px;">
                <img style="width:auto; height: 50px;" src="{{ $message->embed(public_path('assets/logo_wizi.png')) }}"
                    alt="logo Wizi Learn">
            </div>

            <p
                style="color:#f9922b;font-family:'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;font-size: 14px;text-align:center;line-height:15px;margin-top:5px;">
                <strong>FICHE DE DEMANDE DE FORMATION</strong>
            </p>

            <div class="header">
                <h1>Nouvelle demande d'inscription</h1>
            </div>

            <p>Bonjour,</p>

            <p><strong>{{ $stagiaire->prenom }} {{ $stagiaire->user->name }}</strong> souhaite s'inscrire à une
                formation.</p>

            <!-- Fiche de demande -->
            <div class="fiche-demande">
                <b>Civilité:</b> <span>{{ $stagiaire->civilite ?? 'Non renseigné' }}</span><br>
                <b>Nom:</b> <span>{{ $stagiaire->nom ?? 'Non renseigné' }}</span><br>
                <b>Prénom:</b> <span>{{ $stagiaire->prenom ?? 'Non renseigné' }}</span><br>
                <b>Téléphone:</b> <span>{{ $stagiaire->telephone ?? 'Non renseigné' }}</span><br>
                <b>Email:</b> <span>{{ $stagiaire->user->email ?? 'Non renseigné' }}</span><br>
                <b>Formation:</b> <span>{{ $catalogueFormation->titre }}</span><br>
            </div>

            <div class="divider"></div>

            <h2 class="formation-title">{{ strtoupper($catalogueFormation->titre) }}</h2>

            <p><strong>Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
            @if ($catalogueFormation->tarif)
                <p><strong>Tarif :</strong> {{ number_format($catalogueFormation->tarif, 0, ',', ' ') }} €</p>
            @endif

            <div class="bullet-points">
                <div>Profitez d'une formation accessible à tous les niveaux, de l'initiation des débutants jusqu'au
                    perfectionnement</div>
                <div>Assurez-vous d'avoir à disposition le matériel informatique approprié</div>
                <div>Utilisez l'environnement Windows avec des connaissances de base</div>
            </div>

            <div class="divider"></div>

            <p>Merci de prendre contact avec le stagiaire pour finaliser cette inscription.</p>

            <div class="footer">
                <p>Wizi Learn - Tél. : 09 72 51 29 04 | Email: contact@wizi-learn.com</p>
                <p>© {{ date('Y') }} Wizi Learn. Tous droits réservés.</p>
            </div>
        </div>
    </body>

    </html>
@else
    <!DOCTYPE html>
    <html>

    <head>
        <style>
            body {
                font-family: 'Helvetica Neue', Arial, sans-serif;
                line-height: 1.6;
                color: #333333;
                background-color: #ffffff;
                margin: 0;
                padding: 20px;
            }

            .email-content {
                max-width: 600px;
                margin: 0 auto;
            }

            .header {
                margin-bottom: 25px;
            }

            .header h1 {
                color: #2c3e50;
                font-size: 22px;
                margin-bottom: 5px;
            }

            .divider {
                border-top: 1px solid #e0e0e0;
                margin: 20px 0;
            }

            .formation-title {
                color: #2c3e50;
                font-size: 18px;
                margin: 15px 0 10px 0;
            }

            .btn {
                display: inline-block;
                background-color: #3498db;
                color: white;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 4px;
                font-size: 14px;
                margin: 5px;
            }

            .btn-secondary {
                background-color: #2c3e50;
            }

            .footer {
                margin-top: 30px;
                font-size: 12px;
                color: #7f8c8d;
                text-align: center;
            }

            .contact-box {
                background-color: #f5f9ff;
                border-left: 3px solid #3498db;
                padding: 12px;
                margin: 20px 0;
            }

            .bullet-points {
                margin: 15px 0;
                padding-left: 5px;
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
                color: #3498db;
            }
        </style>
    </head>

    <body>
        <div class="email-content">
            <!-- Logo centré -->
            <div style="text-align:center; margin-bottom: 20px;">
                <img style="width:auto; height: 50px;" src="{{ $message->embed(public_path('assets/logo_wizi.png')) }}"
                    alt="logo Wizi Learn">
            </div>

            <div class="header">
                <h1>Confirmation de votre demande</h1>
            </div>

            <p>Bonjour <strong>{{ $stagiaire->prenom }}</strong>,</p>

            <p>Nous avons bien reçu votre demande d'inscription à la formation suivante :</p>

            <div class="divider"></div>

            <h2 class="formation-title">{{ strtoupper($catalogueFormation->titre) }}</h2>

            <p><strong>Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
            @if ($catalogueFormation->tarif)
                <p><strong>Tarif :</strong> {{ number_format($catalogueFormation->tarif, 0, ',', ' ') }} €</p>
            @endif

            <div class="bullet-points">
                <div>Profitez d'une formation accessible à tous les niveaux, de l'initiation des débutants jusqu'au
                    perfectionnement</div>
                <div>Assurez-vous d'avoir à disposition le matériel informatique approprié</div>
                <div>Utilisez l'environnement Windows avec des connaissances de base</div>
            </div>

            <div class="divider"></div>

            <p>Un conseiller de votre pôle Relation Client vous contactera prochainement pour finaliser votre
                inscription.</p>

            <div class="contact-box">
                <p>Pour toute question : <a href="mailto:contact@wizi-learn.com"
                        style="color: #3498db; text-decoration: none;">contact@wizi-learn.com</a></p>
            </div>

            <div class="footer">
                <p>Nous vous remercions de votre confiance.</p>
                <p>Wizi Learn - Tél. : 09 72 51 29 04 | Email: contact@wizi-learn.com</p>
                <p>© {{ date('Y') }} Wizi Learn. Tous droits réservés.</p>
            </div>
        </div>
    </body>

    </html>
@endif
