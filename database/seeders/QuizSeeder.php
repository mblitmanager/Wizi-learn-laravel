<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Questions;
use App\Models\Quiz;
use App\Models\Reponse;
use Illuminate\Database\Seeder;

/**
 * 10 quiz (un par formation du catalogue), 5 questions chacun.
 *
 * Formats de réponses attendus par QuizController :
 *  - choix multiples / vrai/faux : is_correct sur les bonnes réponses
 *  - rearrangement : toutes les réponses is_correct, ordre donné par "position"
 */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->quizzes() as $data) {
            $formation = Formation::where('titre', $data['categorie'])->firstOrFail();

            $quiz = Quiz::updateOrCreate(
                ['titre' => $data['titre']],
                [
                    'description' => $data['description'],
                    'duree' => $data['duree'],
                    'niveau' => $data['niveau'],
                    'formation_id' => $formation->id,
                    'status' => 'actif',
                ]
            );

            // Réinitialise les questions pour garder le seeder rejouable
            $quiz->questions()->each(fn (Questions $q) => $q->delete());

            foreach ($data['questions'] as $q) {
                $question = Questions::create([
                    'quiz_id' => $quiz->id,
                    'text' => $q['text'],
                    'type' => $q['type'],
                    'explication' => $q['explication'] ?? null,
                    'astuce' => $q['astuce'] ?? null,
                    'points' => '2',
                ]);

                if ($q['type'] === 'rearrangement') {
                    foreach ($q['ordre'] as $position => $texte) {
                        Reponse::create([
                            'question_id' => $question->id,
                            'text' => $texte,
                            'is_correct' => true,
                            'position' => $position + 1,
                        ]);
                    }

                    continue;
                }

                $position = 1;
                foreach ($q['reponses'] as $texte => $correct) {
                    Reponse::create([
                        'question_id' => $question->id,
                        'text' => $texte,
                        'is_correct' => $correct,
                        'position' => $position++,
                    ]);
                }
            }

            // Le hook "saving" du modèle recalcule nb_points_total à partir des questions
            $quiz->save();
        }
    }

    private function vraiFaux(string $text, bool $vrai, string $explication): array
    {
        return [
            'type' => 'vrai/faux',
            'text' => $text,
            'reponses' => ['Vrai' => $vrai, 'Faux' => ! $vrai],
            'explication' => $explication,
        ];
    }

    private function quizzes(): array
    {
        return [
            [
                'categorie' => 'Intelligence Artificielle',
                'titre' => 'IA Générative : les fondamentaux',
                'description' => 'Testez vos connaissances sur l\'IA générative, le prompt engineering et le cadre réglementaire.',
                'duree' => '10',
                'niveau' => 'débutant',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Que signifie le terme « prompt » en IA générative ?',
                        'reponses' => [
                            'L\'instruction donnée à l\'IA pour générer un contenu' => true,
                            'Le nom du modèle d\'IA utilisé' => false,
                            'Le temps de réponse de l\'IA' => false,
                            'Le format du fichier généré' => false,
                        ],
                        'explication' => 'Le prompt est la consigne rédigée par l\'utilisateur pour guider la génération.',
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel règlement européen encadre spécifiquement l\'intelligence artificielle ?',
                        'reponses' => ['L\'AI Act' => true, 'Le Digital Markets Act' => false, 'La directive NIS 2' => false, 'Le Cloud Act' => false],
                        'explication' => 'L\'AI Act est le règlement européen sur l\'intelligence artificielle.',
                    ],
                    $this->vraiFaux('On peut saisir sans risque des données personnelles de clients dans un outil d\'IA publique.', false, 'Le RGPD impose de protéger les données personnelles : il faut les anonymiser ou utiliser un outil conforme.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quels éléments améliorent la qualité d\'un prompt ? (plusieurs réponses)',
                        'reponses' => ['Préciser le contexte' => true, 'Indiquer le format attendu' => true, 'Rester le plus vague possible' => false, 'Écrire uniquement en majuscules' => false],
                        'explication' => 'Contexte, rôle, format et contraintes rendent la réponse plus pertinente.',
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes de création d\'un contenu avec l\'IA.',
                        'ordre' => ['Définir l\'objectif', 'Rédiger le prompt', 'Générer le contenu', 'Vérifier et corriger le résultat'],
                        'explication' => 'Le contenu généré doit toujours être relu et vérifié avant diffusion.',
                    ],
                ],
            ],
            [
                'categorie' => 'Bureautique',
                'titre' => 'Word : mise en forme de documents',
                'description' => 'Vérifiez votre maîtrise de la mise en forme, des styles et de la mise en page dans Word.',
                'duree' => '10',
                'niveau' => 'débutant',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel raccourci clavier met un texte en gras dans Word ?',
                        'reponses' => ['Ctrl + G' => true, 'Ctrl + B' => false, 'Ctrl + S' => false, 'Ctrl + I' => false],
                        'explication' => 'Dans Word en français, Ctrl + G applique le gras.',
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quelle fonctionnalité permet de générer automatiquement une table des matières ?',
                        'reponses' => ['Les styles de titres' => true, 'Les tabulations' => false, 'Le correcteur orthographique' => false, 'Le zoom' => false],
                        'explication' => 'La table des matières s\'appuie sur les styles Titre 1, Titre 2, etc.',
                    ],
                    $this->vraiFaux('Le publipostage permet de personnaliser un même courrier pour plusieurs destinataires.', true, 'Le publipostage fusionne un document type avec une liste de destinataires.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Dans quel onglet trouve-t-on les réglages des marges ?',
                        'reponses' => ['Mise en page' => true, 'Accueil' => false, 'Révision' => false, 'Affichage' => false],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes pour insérer un tableau.',
                        'ordre' => ['Placer le curseur', 'Ouvrir l\'onglet Insertion', 'Cliquer sur Tableau', 'Choisir le nombre de lignes et colonnes'],
                    ],
                ],
            ],
            [
                'categorie' => 'Bureautique',
                'titre' => 'Excel : formules et analyse de données',
                'description' => 'Formules, références et graphiques : évaluez vos compétences sur Excel.',
                'duree' => '15',
                'niveau' => 'intermédiaire',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quelle formule calcule la somme des cellules A1 à A10 ?',
                        'reponses' => ['=SOMME(A1:A10)' => true, '=TOTAL(A1-A10)' => false, '=ADDITION(A1;A10)' => false, '=SOMME(A1+A10)' => false],
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Que signifie le symbole $ dans la référence $A$1 ?',
                        'reponses' => ['Une référence absolue' => true, 'Une valeur monétaire' => false, 'Une cellule masquée' => false, 'Une erreur de formule' => false],
                        'explication' => 'Le $ fige la colonne et/ou la ligne lors de la recopie de la formule.',
                    ],
                    $this->vraiFaux('La fonction RECHERCHEV recherche une valeur dans la première colonne d\'un tableau.', true, 'RECHERCHEV cherche dans la première colonne puis renvoie la valeur d\'une colonne indiquée.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel outil permet de synthétiser rapidement un grand volume de données ?',
                        'reponses' => ['Le tableau croisé dynamique' => true, 'La mise en forme conditionnelle' => false, 'Le correcteur' => false, 'Le filtre avancé' => false],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes de création d\'un graphique.',
                        'ordre' => ['Sélectionner les données', 'Ouvrir l\'onglet Insertion', 'Choisir le type de graphique', 'Personnaliser titres et légende'],
                    ],
                ],
            ],
            [
                'categorie' => 'Bureautique',
                'titre' => 'PowerPoint : réussir ses présentations',
                'description' => 'Masques, animations et diffusion : testez vos connaissances PowerPoint.',
                'duree' => '10',
                'niveau' => 'débutant',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quelle touche lance le diaporama depuis la première diapositive ?',
                        'reponses' => ['F5' => true, 'F1' => false, 'F12' => false, 'Échap' => false],
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'À quoi sert le masque des diapositives ?',
                        'reponses' => [
                            'Appliquer une mise en forme commune à toutes les diapositives' => true,
                            'Cacher des diapositives' => false,
                            'Protéger la présentation par mot de passe' => false,
                            'Enregistrer la présentation en PDF' => false,
                        ],
                    ],
                    $this->vraiFaux('Une transition s\'applique entre deux diapositives, une animation s\'applique à un objet.', true, 'Transitions = passage d\'une diapositive à l\'autre ; animations = objets.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel mode d\'affichage permet de réorganiser facilement l\'ordre des diapositives ?',
                        'reponses' => ['La trieuse de diapositives' => true, 'Le mode Lecture' => false, 'Le mode Page de notes' => false, 'Le mode Plan' => false],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes de préparation d\'une présentation.',
                        'ordre' => ['Définir le message clé', 'Construire le plan', 'Créer les diapositives', 'Répéter la présentation'],
                    ],
                ],
            ],
            [
                'categorie' => 'Internet',
                'titre' => 'WordPress : créer son site',
                'description' => 'Pages, articles, thèmes et extensions : évaluez vos connaissances WordPress.',
                'duree' => '15',
                'niveau' => 'intermédiaire',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quelle est la différence principale entre une page et un article dans WordPress ?',
                        'reponses' => [
                            'L\'article est daté et classé par catégories, la page est statique' => true,
                            'La page est obligatoirement payante' => false,
                            'L\'article ne peut pas contenir d\'image' => false,
                            'Il n\'y a aucune différence' => false,
                        ],
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Comment ajoute-t-on une fonctionnalité (formulaire, SEO…) à WordPress ?',
                        'reponses' => ['En installant une extension' => true, 'En changeant de navigateur' => false, 'En modifiant le nom de domaine' => false, 'En vidant le cache' => false],
                    ],
                    $this->vraiFaux('Mettre à jour régulièrement WordPress et ses extensions améliore la sécurité du site.', true, 'Les mises à jour corrigent les failles de sécurité connues.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel élément définit l\'apparence générale d\'un site WordPress ?',
                        'reponses' => ['Le thème' => true, 'Le widget' => false, 'Le permalien' => false, 'La taxonomie' => false],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes de mise en ligne d\'un site WordPress.',
                        'ordre' => ['Choisir un hébergement', 'Installer WordPress', 'Choisir et configurer un thème', 'Publier les contenus'],
                    ],
                ],
            ],
            [
                'categorie' => 'Création',
                'titre' => 'Photoshop : retouche et calques',
                'description' => 'Sélections, calques et export : testez vos bases sur Photoshop.',
                'duree' => '15',
                'niveau' => 'intermédiaire',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel format conserve les calques d\'un document Photoshop ?',
                        'reponses' => ['PSD' => true, 'JPG' => false, 'GIF' => false, 'BMP' => false],
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel outil est adapté pour supprimer une petite imperfection sur un visage ?',
                        'reponses' => ['Le correcteur localisé' => true, 'Le pot de peinture' => false, 'L\'outil texte' => false, 'Le recadrage' => false],
                    ],
                    $this->vraiFaux('Un masque de fusion permet de masquer une partie d\'un calque sans la supprimer définitivement.', true, 'Le masque est non destructif : on peut toujours revenir en arrière.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel mode colorimétrique utilise-t-on pour une image destinée au web ?',
                        'reponses' => ['RVB' => true, 'CMJN' => false, 'Niveaux de gris uniquement' => false, 'Bichromie' => false],
                        'explication' => 'Le RVB est adapté aux écrans ; le CMJN est réservé à l\'impression.',
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes d\'un détourage.',
                        'ordre' => ['Ouvrir l\'image', 'Sélectionner le sujet', 'Affiner la sélection', 'Ajouter un masque de fusion'],
                    ],
                ],
            ],
            [
                'categorie' => 'Création',
                'titre' => 'Illustrator : bases du dessin vectoriel',
                'description' => 'Outil plume, formes et couleurs : vérifiez vos connaissances Illustrator.',
                'duree' => '10',
                'niveau' => 'débutant',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel est l\'avantage principal d\'une image vectorielle ?',
                        'reponses' => [
                            'Elle peut être agrandie sans perte de qualité' => true,
                            'Elle pèse toujours plus lourd qu\'une photo' => false,
                            'Elle ne peut contenir qu\'une seule couleur' => false,
                            'Elle est uniquement destinée au web' => false,
                        ],
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quel outil permet de tracer des courbes précises avec des points d\'ancrage ?',
                        'reponses' => ['L\'outil Plume' => true, 'Le Pinceau' => false, 'La Pipette' => false, 'La Gomme' => false],
                    ],
                    $this->vraiFaux('Un logo doit de préférence être créé en vectoriel.', true, 'Le vectoriel garantit un rendu net sur tous les supports, du badge à l\'affiche.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Que permet la fonction « Vectorisation dynamique » ?',
                        'reponses' => ['Transformer une image matricielle en tracés vectoriels' => true, 'Animer un dessin' => false, 'Exporter en vidéo' => false, 'Ajouter un filtre flou' => false],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes de création d\'un logo.',
                        'ordre' => ['Réaliser un croquis', 'Tracer les formes', 'Appliquer les couleurs', 'Exporter les fichiers'],
                    ],
                ],
            ],
            [
                'categorie' => 'Langues',
                'titre' => 'Anglais professionnel : vocabulaire et grammaire',
                'description' => 'Évaluez votre anglais dans des situations courantes et professionnelles.',
                'duree' => '10',
                'niveau' => 'intermédiaire',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Choisissez la bonne traduction de « Je vous écris au sujet de votre commande ».',
                        'reponses' => [
                            'I am writing regarding your order.' => true,
                            'I write you about your command.' => false,
                            'I am wrote about your order.' => false,
                            'I writing for your commande.' => false,
                        ],
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Complétez : « She ___ in this company since 2020. »',
                        'reponses' => ['has worked' => true, 'works' => false, 'is working' => false, 'worked' => false],
                        'explication' => 'Avec « since », on utilise le present perfect.',
                    ],
                    $this->vraiFaux('« Actually » signifie « actuellement » en français.', false, '« Actually » signifie « en fait » ; « actuellement » se dit « currently ».'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quelle formule convient pour conclure un e-mail professionnel ?',
                        'reponses' => ['Best regards,' => true, 'See ya!' => false, 'Bye bye,' => false, 'Cheers mate,' => false],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez les mots dans l\'ordre pour former une phrase correcte.',
                        'ordre' => ['Could', 'you', 'send', 'me', 'the', 'report?'],
                    ],
                ],
            ],
            [
                'categorie' => 'Langues',
                'titre' => 'Français : orthographe et écrits professionnels',
                'description' => 'Accords, conjugaison et formules de politesse : fiabilisez vos écrits.',
                'duree' => '10',
                'niveau' => 'avancé',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quelle phrase est correctement orthographiée ?',
                        'reponses' => [
                            'Les documents que j\'ai envoyés sont arrivés.' => true,
                            'Les documents que j\'ai envoyé sont arrivé.' => false,
                            'Les documents que j\'ai envoyer sont arrivés.' => false,
                            'Les document que j\'ai envoyés sont arrivés.' => false,
                        ],
                        'explication' => 'Le participe passé avec « avoir » s\'accorde avec le COD placé avant (« que » = les documents).',
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Complétez : « Je vous saurais gré de bien vouloir me ___ votre réponse. »',
                        'reponses' => ['faire parvenir' => true, 'faire parvenu' => false, 'faire parvient' => false, 'fais parvenir' => false],
                    ],
                    $this->vraiFaux('On écrit « quoique » en un mot lorsqu\'il signifie « bien que ».', true, '« Quoique » = bien que ; « quoi que » = quelle que soit la chose que.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Quelle formule de politesse est adaptée à un courrier formel ?',
                        'reponses' => [
                            'Veuillez agréer, Madame, l\'expression de mes salutations distinguées.' => true,
                            'Bisous et à bientôt.' => false,
                            'Salut, bonne journée !' => false,
                            'À plus.' => false,
                        ],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les parties d\'un courriel professionnel.',
                        'ordre' => ['Objet', 'Formule d\'appel', 'Corps du message', 'Formule de politesse', 'Signature'],
                    ],
                ],
            ],
            [
                'categorie' => 'Les Bases du Digital',
                'titre' => 'DigComp : compétences numériques essentielles',
                'description' => 'Recherche d\'information, communication et sécurité : faites le point sur vos bases numériques.',
                'duree' => '10',
                'niveau' => 'débutant',
                'questions' => [
                    [
                        'type' => 'choix multiples',
                        'text' => 'Qu\'est-ce qu\'un mot de passe robuste ?',
                        'reponses' => [
                            'Un mot de passe long mêlant lettres, chiffres et caractères spéciaux' => true,
                            'Sa date de naissance' => false,
                            'Le même mot de passe pour tous ses comptes' => false,
                            '123456' => false,
                        ],
                    ],
                    [
                        'type' => 'choix multiples',
                        'text' => 'Comment reconnaître un e-mail d\'hameçonnage (phishing) ? (plusieurs réponses)',
                        'reponses' => [
                            'Une adresse d\'expéditeur suspecte' => true,
                            'Une demande urgente d\'informations personnelles' => true,
                            'Un message de votre collègue habituel sans lien' => false,
                            'Un e-mail sans pièce jointe' => false,
                        ],
                    ],
                    $this->vraiFaux('Toutes les informations trouvées sur Internet sont fiables.', false, 'Il faut vérifier la source, la date et croiser les informations.'),
                    [
                        'type' => 'choix multiples',
                        'text' => 'Que signifie le cadenas affiché dans la barre d\'adresse du navigateur ?',
                        'reponses' => ['La connexion au site est chiffrée (HTTPS)' => true, 'Le site est gratuit' => false, 'Le site est hors ligne' => false, 'Le site est officiel du gouvernement' => false],
                    ],
                    [
                        'type' => 'rearrangement',
                        'text' => 'Remettez dans l\'ordre les étapes pour partager un document en ligne.',
                        'ordre' => ['Enregistrer le document dans le cloud', 'Cliquer sur Partager', 'Saisir l\'adresse du destinataire', 'Définir les droits d\'accès'],
                    ],
                ],
            ],
        ];
    }
}
