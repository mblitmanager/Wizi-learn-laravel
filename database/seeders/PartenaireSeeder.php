<?php

namespace Database\Seeders;

use App\Models\Partenaire;
use Illuminate\Database\Seeder;

class PartenaireSeeder extends Seeder
{
    public function run(): void
    {
        $partenaires = [
            ['identifiant' => 'AGENCE-EMPLOI-LYON', 'adresse' => '12 rue de la République', 'ville' => 'Lyon', 'departement' => 'Rhône', 'code_postal' => '69002', 'type' => 'Agence pour l\'emploi', 'contact' => ['Claire', 'Bertrand', 'Conseillère emploi']],
            ['identifiant' => 'MISSION-LOCALE-NANTES', 'adresse' => '5 quai de la Fosse', 'ville' => 'Nantes', 'departement' => 'Loire-Atlantique', 'code_postal' => '44000', 'type' => 'Mission locale', 'contact' => ['Julien', 'Moreau', 'Chargé d\'insertion']],
            ['identifiant' => 'CCI-BORDEAUX', 'adresse' => '17 place de la Bourse', 'ville' => 'Bordeaux', 'departement' => 'Gironde', 'code_postal' => '33000', 'type' => 'Chambre de commerce', 'contact' => ['Sophie', 'Lefèvre', 'Responsable formation']],
            ['identifiant' => 'TECHNOSOFT-SAS', 'adresse' => '48 avenue des Champs-Élysées', 'ville' => 'Paris', 'departement' => 'Paris', 'code_postal' => '75008', 'type' => 'Entreprise', 'contact' => ['Marc', 'Dupont', 'DRH']],
            ['identifiant' => 'ASSO-NUMERIQUE-POUR-TOUS', 'adresse' => '3 rue Paradis', 'ville' => 'Marseille', 'departement' => 'Bouches-du-Rhône', 'code_postal' => '13001', 'type' => 'Association', 'contact' => ['Nadia', 'Benali', 'Présidente']],
            ['identifiant' => 'MAIRIE-TOULOUSE-EMPLOI', 'adresse' => '1 place du Capitole', 'ville' => 'Toulouse', 'departement' => 'Haute-Garonne', 'code_postal' => '31000', 'type' => 'Collectivité', 'contact' => ['Thomas', 'Garcia', 'Chef de service']],
            ['identifiant' => 'BATI-OUEST', 'adresse' => '22 rue du Port', 'ville' => 'Rennes', 'departement' => 'Ille-et-Vilaine', 'code_postal' => '35000', 'type' => 'Entreprise', 'contact' => ['Élodie', 'Le Gall', 'Assistante RH']],
            ['identifiant' => 'CAP-EMPLOI-LILLE', 'adresse' => '80 rue Nationale', 'ville' => 'Lille', 'departement' => 'Nord', 'code_postal' => '59000', 'type' => 'Cap emploi', 'contact' => ['Karim', 'Haddad', 'Conseiller']],
            ['identifiant' => 'OPCO-GRAND-EST', 'adresse' => '9 place Kléber', 'ville' => 'Strasbourg', 'departement' => 'Bas-Rhin', 'code_postal' => '67000', 'type' => 'OPCO', 'contact' => ['Anne', 'Muller', 'Chargée de mission']],
            ['identifiant' => 'NICE-COMMERCE-GROUPE', 'adresse' => '30 avenue Jean Médecin', 'ville' => 'Nice', 'departement' => 'Alpes-Maritimes', 'code_postal' => '06000', 'type' => 'Entreprise', 'contact' => ['Laurent', 'Rossi', 'Gérant']],
        ];

        foreach ($partenaires as $i => $data) {
            [$prenom, $nom, $fonction] = $data['contact'];
            unset($data['contact']);

            Partenaire::updateOrCreate(
                ['identifiant' => $data['identifiant']],
                $data + [
                    'logo' => null,
                    'actif' => $i !== 9,
                    'contacts' => [[
                        'prenom' => $prenom,
                        'nom' => $nom,
                        'fonction' => $fonction,
                        'email' => strtolower(\Illuminate\Support\Str::ascii($prenom.'.'.str_replace(' ', '', $nom))).'@example.fr',
                        'tel' => '06'.str_pad((string) (12345670 + $i), 8, '0', STR_PAD_LEFT),
                    ]],
                ]
            );
        }
    }
}
