@if ($isPoleRelation)
    <div style="font-family: 'Arial', sans-serif; max-width: 600px; margin: 0 auto; color: #333; line-height: 1.6;">
        <div style="background-color: #2c3e50; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
            <h2 style="color: #fff; margin: 0;">Demande d'inscription à une formation</h2>
        </div>

        <div style="padding: 20px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 5px 5px;">
            <p style="margin-bottom: 20px;">Bonjour,</p>

            <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; margin-bottom: 20px;">
                <p style="margin: 0;">Merci de contacter <strong style="color: #2c3e50;">{{ $stagiaire->prenom }}
                        {{ $stagiaire->user->name }}</strong> (email : <a href="mailto:{{ $stagiaire->user->email }}"
                        style="color: #3498db; text-decoration: none;">{{ $stagiaire->user->email }}</a>) car il souhaite
                    s'inscrire à la formation suivante :</p>
            </div>

            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h3 style="color: #2c3e50; margin-top: 0;">{{ $catalogueFormation->titre }}</h3>
                <p><strong style="color: #2c3e50;">Description :</strong> {{ $catalogueFormation->description }}</p>
                <p><strong style="color: #2c3e50;">Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
            </div>

            <p style="margin-bottom: 20px;">Merci de prendre contact avec le stagiaire pour finaliser l'inscription.</p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="#"
                    style="background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Accéder
                    à la plateforme</a>
            </div>
        </div>
    </div>
@else
    <div style="font-family: 'Arial', sans-serif; max-width: 600px; margin: 0 auto; color: #333; line-height: 1.6;">
        <div style="background-color: #2c3e50; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
            <h2 style="color: #fff; margin: 0;">Confirmation de votre demande d'inscription</h2>
        </div>

        <div style="padding: 20px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 5px 5px;">
            <p style="margin-bottom: 20px;">Bonjour <strong style="color: #2c3e50;">{{ $stagiaire->prenom }}</strong>,
            </p>

            <p style="margin-bottom: 20px;">Nous accusons réception de votre demande d'inscription à la formation
                suivante :</p>

            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h3 style="color: #2c3e50; margin-top: 0;">{{ $catalogueFormation->titre }}</h3>
                <p><strong style="color: #2c3e50;">Description :</strong> {{ $catalogueFormation->description }}</p>
                <p><strong style="color: #2c3e50;">Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
            </div>

            <p style="margin-bottom: 20px;">Un conseiller de votre pôle Relation Client vous contactera prochainement
                pour finaliser votre inscription.</p>

            <div style="background-color: #e8f4fc; padding: 15px; border-left: 4px solid #3498db; margin-bottom: 20px;">
                <p style="margin: 0;">Si vous avez des questions, n'hésitez pas à nous contacter à l'adresse <a
                        href="mailto:contact@formations.com"
                        style="color: #3498db; text-decoration: none;">contact@formations.com</a>.</p>
            </div>

            <p style="margin-bottom: 20px;">Vous pouvez consulter notre catalogue de formations en téléchargeant la
                brochure PDF :</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="#"
                    style="background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px;">Télécharger
                    la brochure</a>
                <a href="#"
                    style="background-color: #2c3e50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Accéder
                    à la plateforme</a>
            </div>

            <p style="margin-bottom: 0;">Nous vous remercions de l'intérêt que vous portez à nos formations et de votre
                confiance.</p>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #7f8c8d; font-size: 12px;">
            <p>© {{ date('Y') }} Nom de votre entreprise. Tous droits réservés.</p>
        </div>
    </div>
@endif
