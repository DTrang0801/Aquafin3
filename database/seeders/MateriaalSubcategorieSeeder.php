<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaalSubcategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Bevestigingsmateriaal' => [
                'Bouten',
                'Moeren',
                'Ringen',
                'Ankerbouten',
                'Chemische ankers',
                'Keilbouten',
                'Draadstangen',
                'Inslagmoeren',
                'Tapbouten',
                'Zeskantkop- en inbusbouten',
                'Torx- en kruiskopschroeven',
                'Zelftappende vijzen',
                'Parkervijzen',
                'Spaanplaatschroeven',
                'Slangenklemmen',
            ],
            'Persoonlijke beschermingsmiddelen (PBM)' => [
                'Helmen',
                'Hoorbescherming',
                'Oogbescherming',
                'Adembescherming',
                'Handschoenen',
                'Veiligheidsschoenen',
                'Werklaarzen',
                'Regenkledij',
                'Signalisatiekledij',
                'Overalls',
                'Valbeveiliging',
                'Gasdetectie',
                'EHBO en hygiëne',
                'Klimuitrusting',
            ],
            'Gereedschap (manueel & elektrisch)' => [
                'Dopsleutelsets',
                'Sleutelsets',
                'Momentsleutels',
                'Inbussleutels',
                'Schroevendraaiers',
                'Tangen',
                'Krimptangen',
                'Kabeltools',
                'Hamers',
                'Breekijzers',
                'Slijpmachines',
                'Boor- & schroefmachines',
                'Slagmoersleutels',
                'Waterpassen',
                'Meetgereedschap',
                'Laskisten',
            ],
            'Technische onderhoudsmaterialen' => [
                'Smeermiddelen',
                'O-ringen',
                'Pakkingen',
                'Tapes en lijmen',
                'Slangen',
                'Fittingen',
                'Koppelingen',
                'V-snaren en kettingen',
                'Kabels en wartels',
                'Aansluitdozen',
                'Leidingsystemen',
                'Pneumatiek',
                'Trillingsdempers',
            ],
            'Specifieke Aquafin/riolering gerelateerde tools' => [
                'Putgereedschap',
                'Inspectieapparatuur',
                'Gasdetectie',
                'Reiniging en ontstoppen',
                'Slangenwagens',
                'Pompen',
                'Niveaumeting',
                'Monstername en staalname',
            ],
            'Diversen / Verbruiksgoederen' => [
                'Verbruiksartikelen',
                'Reserveonderdelen',
                'Gassen en perslucht',
            ],
        ];

        $now = now();
        $rows = [];

        foreach ($data as $category => $subcats) {
            $catId = DB::table('materiaal_categorieen')->where('naam', $category)->value('id');
            if (! $catId) {
                continue;
            }

            foreach ($subcats as $sub) {
                // avoid duplicates if already present
                $exists = DB::table('materiaal_subcategorieen')
                    ->where('materiaal_categorie_id', $catId)
                    ->where('naam', $sub)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $rows[] = [
                    'materiaal_categorie_id' => $catId,
                    'naam' => $sub,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($rows)) {
            DB::table('materiaal_subcategorieen')->insert($rows);
        }
    }
}
