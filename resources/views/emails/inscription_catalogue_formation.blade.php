@if($isPoleRelation)
    <h2>Demande d'inscription à une formation</h2>
    <p>Bonjour,</p>
    <p>Merci de contacter <strong>{{ $stagiaire->prenom }} {{ $stagiaire->user->name }}</strong> (email : {{ $stagiaire->user->email }}) car il souhaite s'inscrire à la formation <strong>{{ $catalogueFormation->titre }}</strong>.</p>
    <p><strong>Description :</strong> {{ $catalogueFormation->description }}</p>
    <p><strong>Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
    <p>Merci de prendre contact avec le stagiaire pour finaliser l'inscription.</p>
@else
    <h2>Nouvelle inscription à une formation</h2>
    <p>Bonjour {{ $stagiaire->prenom }},</p>
    <p>Votre demande d'inscription à la formation <strong>{{ $catalogueFormation->titre }}</strong> a bien été soumise.</p>
    <p><strong>Description :</strong> {{ $catalogueFormation->description }}</p>
    <p><strong>Durée :</strong> {{ $catalogueFormation->duree }} heures</p>
    <p>Un conseiller de votre pôle Relation Client vous contactera prochainement pour finaliser votre inscription.</p>
    <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
    <p>Vous pouvez consulter notre catalogue de formations en téléchargeant la brochure PDF.</p>
    <p>Nous vous remercions de l'intérêt que vous portez à nos formations.</p>
    <p>Merci de votre confiance.</p>
@endif
