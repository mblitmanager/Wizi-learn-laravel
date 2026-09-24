<?php

namespace Database\Seeders;

use App\Models\CatalogueFormation;
use App\Models\Formation;
use Illuminate\Database\Seeder;

/**
 * Formations du catalogue, inspirées de https://likeformation.fr/nos-formations/
 */
class CatalogueFormationSeeder extends Seeder
{
    public function run(): void
    {
        $modalites = 'Formation individuelle et personnalisée à distance : accès à la plateforme e-learning 7j/7 et 24h/24 pendant 1 an, réunion de lancement (environ 2h) puis 2 à 3 suivis par mois avec le formateur. Finançable par le CPF.';
        $accompagnement = 'Suivis individuels à distance (visioconférence ou téléphone) avec un formateur dédié, 2 à 3 fois par mois.';
        $moyens = 'Plateforme e-learning, vidéos, documents supports téléchargeables, exercices pratiques et cas concrets.';
        $suivi = 'Feuilles d\'émargement des sessions, suivi de la progression sur la plateforme, attestation de fin de formation.';
        $prerequis = 'Ouvert à tous niveaux. Matériel informatique adapté et connexion internet haut débit.';

        $catalogue = [
            [
                'categorie' => 'Intelligence Artificielle',
                'titre' => 'IA Générative - Création de contenus rédactionnels et visuels',
                'description' => 'Utiliser l\'IA générative de manière responsable pour produire des contenus rédactionnels et visuels professionnels.',
                'certification' => 'RS 6776 - Création de contenus par l\'usage responsable de l\'IA générative',
                'duree' => '17',
                'tarif' => 1650.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Salariés et demandeurs d\'emploi du tertiaire produisant des contenus (assistanat, marketing, communication, RH, juridique).',
                'objectifs' => "Définir une stratégie d'intégration des outils d'IA générative selon son contexte professionnel.\nOptimiser l'usage de l'IA pour créer des contenus accessibles et inclusifs en protégeant la confidentialité des données.\nIdentifier et traiter les enjeux éthiques et réglementaires (AI Act, RGPD).",
                'programme' => "Module 1 : Comprendre l'IA générative\nModule 2 : Les bases du prompt engineering\nModule 3 : Étude de cas - intégration stratégique de l'IA\nModule 4 : Sécurité et confidentialité\nModule 5 : RGPD et AI Act\nModule 6 : Identification des risques\nModule 7 : Générer des contenus professionnels\nModule 8 : Accessibilité et adaptation inclusive\nModule 9 : Mise en pratique sous contraintes",
                'evaluation' => 'Test de positionnement, évaluations continues, examen écrit (6 cas pratiques) et soutenance orale de 20 minutes.',
            ],
            [
                'categorie' => 'Bureautique',
                'titre' => 'Word - Créer et mettre en forme des documents (TOSA)',
                'description' => 'Concevoir des documents professionnels structurés avec Microsoft Word : mise en forme, tableaux, publipostage.',
                'certification' => 'TOSA Word',
                'duree' => '10',
                'tarif' => 1290.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Tout public souhaitant produire des documents professionnels.',
                'objectifs' => "Maîtriser l'environnement Word.\nMettre en forme et mettre en page des documents.\nInsérer des tableaux, images et objets graphiques.\nUtiliser les styles et le publipostage.",
                'programme' => "Module 1 : Prise en main de Word\nModule 2 : Mise en forme du texte et des paragraphes\nModule 3 : Tableaux et objets graphiques\nModule 4 : Styles, mise en page et publipostage",
                'evaluation' => 'Test de positionnement initial, questions orales régulières et passage de la certification TOSA.',
            ],
            [
                'categorie' => 'Bureautique',
                'titre' => 'Excel - Gestion et analyse des données (TOSA)',
                'description' => 'Exploiter les fonctionnalités de Microsoft Excel pour la gestion et l\'analyse des données.',
                'certification' => 'TOSA Excel - RS 7256',
                'duree' => '10',
                'tarif' => 1290.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Tout public souhaitant gérer et analyser des données avec Excel.',
                'objectifs' => "Maîtriser les bases d'Excel pour la gestion et l'analyse de données.\nConcevoir des tableaux, calculs et mises en forme.\nCréer des graphiques pour illustrer les données.",
                'programme' => "Module 1 : Introduction et prise en main d'Excel\nModule 2 : Calculs et formules\nModule 3 : Mise en forme et organisation des données\nModule 4 : Intégration de graphiques",
                'evaluation' => 'Test de positionnement initial, questions orales régulières et passage de la certification TOSA.',
            ],
            [
                'categorie' => 'Bureautique',
                'titre' => 'PowerPoint - Concevoir des présentations (TOSA)',
                'description' => 'Réaliser des présentations claires et percutantes avec Microsoft PowerPoint.',
                'certification' => 'TOSA PowerPoint',
                'duree' => '10',
                'tarif' => 1290.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Tout public amené à présenter des projets ou des résultats.',
                'objectifs' => "Créer une présentation structurée.\nUtiliser les masques et thèmes.\nIntégrer images, tableaux, graphiques et vidéos.\nAnimer et diffuser un diaporama.",
                'programme' => "Module 1 : Prise en main de PowerPoint\nModule 2 : Thèmes, masques et mise en page\nModule 3 : Objets graphiques et multimédia\nModule 4 : Transitions, animations et diffusion",
                'evaluation' => 'Test de positionnement initial, questions orales régulières et passage de la certification TOSA.',
            ],
            [
                'categorie' => 'Internet',
                'titre' => 'WordPress - Créer et gérer un site web (TOSA)',
                'description' => 'Concevoir, administrer et faire évoluer un site internet avec WordPress.',
                'certification' => 'TOSA WordPress',
                'duree' => '20',
                'tarif' => 1690.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Entrepreneurs, indépendants et salariés souhaitant gérer un site web.',
                'objectifs' => "Installer et configurer WordPress.\nCréer des pages et des articles.\nPersonnaliser l'apparence avec les thèmes et extensions.\nAdministrer et sécuriser son site.",
                'programme' => "Module 1 : Découverte et installation de WordPress\nModule 2 : Pages, articles et médias\nModule 3 : Thèmes, menus et extensions\nModule 4 : Administration, référencement et sécurité",
                'evaluation' => 'Test de positionnement initial, cas pratiques et passage de la certification TOSA.',
            ],
            [
                'categorie' => 'Création',
                'titre' => 'Photoshop - Retouches et compositions d\'images (TOSA)',
                'description' => 'Réaliser des retouches et des compositions d\'images avec Adobe Photoshop.',
                'certification' => 'TOSA Photoshop - RS 6959',
                'duree' => '20',
                'tarif' => 1690.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Tout public souhaitant retoucher et composer des images.',
                'objectifs' => "Comprendre les fondamentaux de l'image numérique.\nGérer et préparer des fichiers image.\nRetoucher, détourer et composer des images.\nExporter pour l'impression et le web.",
                'programme' => "Séquence 1 : Bases de l'image, interface et sélections\nSéquence 2 : Retouche, filtres, couleurs et netteté\nSéquence 3 : Montage, calques, détourage et texte\nSéquence 4 : Export, impression et optimisation web",
                'evaluation' => 'Test de positionnement initial, évaluation continue (questions orales, cas pratiques) et certification TOSA.',
            ],
            [
                'categorie' => 'Création',
                'titre' => 'Illustrator - Dessin vectoriel (TOSA)',
                'description' => 'Créer logos, illustrations et supports de communication vectoriels avec Adobe Illustrator.',
                'certification' => 'TOSA Illustrator',
                'duree' => '20',
                'tarif' => 1690.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Tout public souhaitant réaliser des créations graphiques vectorielles.',
                'objectifs' => "Maîtriser l'interface d'Illustrator.\nDessiner avec les outils plume et formes.\nGérer couleurs, calques et typographie.\nPréparer des fichiers pour l'impression et le web.",
                'programme' => "Module 1 : Découverte de l'interface et du vectoriel\nModule 2 : Tracés, formes et outil plume\nModule 3 : Couleurs, calques et texte\nModule 4 : Export et préparation des fichiers",
                'evaluation' => 'Test de positionnement initial, cas pratiques et passage de la certification TOSA.',
            ],
            [
                'categorie' => 'Langues',
                'titre' => 'Anglais - Comprendre et se faire comprendre (TOEIC)',
                'description' => 'Développer la compréhension et l\'expression orale et écrite en anglais, dans la vie courante et professionnelle.',
                'certification' => 'TOEIC 4-Skills - RS 7229',
                'duree' => '20',
                'tarif' => 1490.00,
                'niveau' => 'Débutant à avancé',
                'public_cible' => 'Tout public disposant de bases en anglais.',
                'objectifs' => "Développer la compréhension orale et écrite.\nDévelopper l'expression orale et écrite.\nEnrichir le vocabulaire de la vie courante et professionnelle.\nPréparer et réussir le TOEIC.",
                'programme' => "Module 1 : Compréhension orale et écrite\nModule 2 : Expression orale (prononciation) et écrite\nModule 3 : Grammaire\nModule 4 : Vocabulaire courant et professionnel\nModule 5 : Jeux de rôle et mises en situation",
                'evaluation' => 'Test de positionnement initial, évaluation continue et passage de la certification TOEIC 4-Skills.',
            ],
            [
                'categorie' => 'Langues',
                'titre' => 'Français - Améliorer ses écrits professionnels (Voltaire)',
                'description' => 'Renforcer sa maîtrise de l\'orthographe, de la grammaire et de la rédaction professionnelle en français.',
                'certification' => 'Certificat Voltaire',
                'duree' => '15',
                'tarif' => 1290.00,
                'niveau' => 'Tous niveaux',
                'public_cible' => 'Salariés et demandeurs d\'emploi souhaitant fiabiliser leurs écrits.',
                'objectifs' => "Maîtriser les règles d'orthographe et de grammaire.\nRédiger des écrits professionnels clairs.\nÉviter les erreurs courantes.\nObtenir la certification Voltaire.",
                'programme' => "Module 1 : Orthographe lexicale et grammaticale\nModule 2 : Conjugaison et accords\nModule 3 : Ponctuation et syntaxe\nModule 4 : Rédaction de courriels et documents professionnels",
                'evaluation' => 'Test de positionnement initial, exercices d\'entraînement et passage du Certificat Voltaire.',
            ],
            [
                'categorie' => 'Les Bases du Digital',
                'titre' => 'DigComp - Les compétences numériques essentielles',
                'description' => 'Acquérir les compétences numériques du référentiel européen DigComp : information, communication, création, sécurité.',
                'certification' => 'Certification DigComp',
                'duree' => '15',
                'tarif' => 1190.00,
                'niveau' => 'Débutant',
                'public_cible' => 'Toute personne souhaitant gagner en autonomie avec les outils numériques.',
                'objectifs' => "Rechercher et évaluer l'information en ligne.\nCommuniquer et collaborer avec les outils numériques.\nCréer des contenus numériques simples.\nProtéger ses données et ses équipements.",
                'programme' => "Module 1 : Information et données\nModule 2 : Communication et collaboration\nModule 3 : Création de contenus numériques\nModule 4 : Sécurité\nModule 5 : Résolution de problèmes",
                'evaluation' => 'Test de positionnement initial, exercices pratiques et passage de la certification DigComp.',
            ],
        ];

        foreach ($catalogue as $item) {
            $formation = Formation::where('titre', $item['categorie'])->firstOrFail();
            unset($item['categorie']);

            CatalogueFormation::updateOrCreate(
                ['titre' => $item['titre']],
                $item + [
                    'formation_id' => $formation->id,
                    'prerequis' => $prerequis,
                    'image_url' => null,
                    'cursus_pdf' => null,
                    'statut' => true,
                    'lieu' => 'À distance',
                    'modalites' => $modalites,
                    'modalites_accompagnement' => $accompagnement,
                    'moyens_pedagogiques' => $moyens,
                    'modalites_suivi' => $suivi,
                    'nombre_participants' => 1,
                ]
            );
        }
    }
}
