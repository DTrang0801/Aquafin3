<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Bevestigingsmateriaal' => [
                'Bouten' => ['M6', 'M8', 'M10', 'M12', 'M16', 'inox A2/A4', 'verzinkt'],
                'Moeren' => ['zeskantmoeren', 'borgmoeren', 'flensmoeren'],
                'Ringen' => ['sluitringen', 'veerringen', 'tandringen'],
                'Ankerbouten' => ['Ankerbouten'],
                'Chemische ankers' => ['Hilti HIT'],
                'Keilbouten' => ['Keilbouten'],
                'Draadstangen' => ['M6', 'M8', 'M10', 'M12', 'M14', 'M16'],
                'Inslagmoeren' => ['Inslagmoeren'],
                'Tapbouten' => ['Tapbouten'],
                'Zeskantkop- en inbusbouten' => ['Zeskantkop- en inbusbouten'],
                'Schroeven' => ['Torxschroeven', 'Kruiskopschroeven', 'Zelftappende vijzen', 'Parkervijzen', 'Spaanplaatschroeven'],
                'Slangenklemmen' => ['Slangenklemmen (div. diameters)'],
            ],
            'Persoonlijke beschermingsmiddelen (PBM)' => [
                'Helmen' => ['Veiligheidshelm (met kinband)'],
                'Hoorbescherming' => ['Oordoppen', 'Gehoorkappen'],
                'Oogbescherming' => ['Veiligheidsbril', 'Gelaatsscherm'],
                'Adembescherming' => ['Stofmaskers (FFP2)', 'Stofmaskers (FFP3)'],
                'Handschoenen' => ['snijvast', 'chemisch resistent', 'elektrisch geïsoleerd'],
                'Veiligheidsschoenen' => ['S3', 'antistatisch', 'stalen tip'],
                'Werklaarzen' => ['PVC', 'nitril', 'Stalen zool'],
                'Regenkledij' => ['jassen', 'broeken', 'capes)'],
                'Signalisatiekledij' => ['Fluovesten / signalisatiekledij (EN ISO 20471)'],
                'Overalls' => ['brandvertragend', 'antistatisch', 'waterafstotend'],
                'Valbeveiliging' => ['Valharnas en lijn', 'Harnas', 'lifeline', 'karabijnhaken'],
                'Gasdetectie' => ['Gasdetectiemeter (O₂, CH₄, H₂S, CO)'],
                'EHBO' => ['Handontsmetting', 'EHBO-kit'],
            ],
            'Gereedschap (manueel & elektrisch)' => [
                'Sleutels' => ['Dopsleutelset (Metrisch)', 'Dopsleutelset (Inch)', 'Ringsleutels', 'Steeksleutels', 'Momentsleutels'],
                'Inbussleutels' => ['Inbussleutels (los en in set)'],
                'Schroevendraaiers' => ['Schroevendraaiers (plat)', 'Schroevendraaiers (kruiskop)', 'Schroevendraaiers (Torx)', 'Schroevendraaiers (geïsoleerd)'],
                'Tangen' => ['Combinatie', 'Waterpomptang', 'Kniptang', 'Punttang'],
                'Krimptang' => ['Krimptang', 'Kabelschoentang'],
                'Stripper' => ['Kabelstripper'],
                'Hamers' => ['Hamer', 'Kunststofhamer', 'Moker'],
                'Breekijzer' => ['Breekijzer'],
                'Slijpmachines' => ['Slijpmachine (haakse slijper)'],
                'Boormachines' => ['Accuboormachine', 'Klopboormachine'],
                'Schroefmachines' => ['Schroefmachine'],
                'Slagmoersleutels' => ['Slagmoersleutel (pneumatisch)', 'Slagmoersleutel (Accu)'],
                'Meetgereedschap' => ['Waterpas', 'Laserwaterpas', 'Meetlint', 'Rolmeter'],
                'Elektrische testers' => ['Spanningstester', 'Multimeter'],
                'Lasapparatuur' => ['Laskist en lasmateriaal (indien van toepassing)'],
            ],
            'Technische onderhoudsmaterialen' => [
                'Smeermiddelen' => ['Smeervet (foodgrade)', 'Smeervet (EP2)', 'Smeervet (lithium)'],
                'O-ringen' => ['O-ringen (div. maten en types)'],
                'Pakkingen' => ['Pakking (papier)', 'Pakking (rubber)', 'Pakking (EPDM)'],
                'Tapes en lijmen' => ['PTFE tape', 'Loctite'],
                'Slangen' => ['PVC', 'PE', 'persslangen'],
                'Fittingen' => ['PVC-fittingen', 'Bochten', 'T-stukken'],
                'Koppelingen' => ['Geka', 'Gardena', 'Camlock)'],
                'Aandrijving' => ['V-snaren', 'Kettingen'],
                'Kabels en wartels' => ['Kabels en wartels (M16–M32)'],
                'Aansluitdozen' => ['Aansluitdozen'],
                'Leidingsystemen' => ['Leidingsystemen (druk/afvoer)'],
                'Pneumatiek' => ['Pneumatische koppelingen'],
                'Trillingsdempers' => ['Trillingsdempers'],
            ],
            'Specifieke Aquafin/riolering gerelateerde tools' => [
                'Putgereedschap' => ['Putdekselhaak / mangatopener'],
                'Inspectieapparatuur' => ['Rioolcamera / inspectiecamera'],
                'Gasdetectie' => ['Gasdetectietoestellen (H₂S, CO, O₂)'],
                'Reiniging en ontstoppen' => ['Ontstoppingsveer', 'Hogedrukreiniger'],
                'Slangenwagens' => ['Slangenwagens'],
                'Pompen' => ['Dompelpompen'],
                'Rioolstoppen' => ['Rioolstoppen'],
                'Vlotterschakelaars' => ['Vlotterschakelaars'],
                'Niveaumeting' => ['Niveaumeting (ultrasoon, radar)'],
                'Staalname' => ['Staalnamepotten'],
                'Monstername' => ['Monsternameapparatuur'],
            ],
            'Diversen / Verbruiksgoederen' => [
                'Tie-wraps' => ['Tie-wraps'],
                'Elektrische accessoires' => ['Kabelschoenen', 'Batterijen / accu\'s'],
                'Tapes en plakband' => ['Markeringstape', 'Duct tape', 'Isolatietape'],
                'Kit en lijm' => ['Siliconenkit / lijm'],
                'Reiniging' => ['Rags (reinigingsdoekjes)', 'WD-40', 'Contactspray', 'Kettingspray'],
                'Reserveonderdelen' => ['Motoren', 'PLC-onderdelen', 'relais'],
                'Gassen' => ['Flessen met perslucht'],
            ],
        ];

        $rows = [];
        $now = now();

        foreach ($data as $category => $subcats) {
            $catId = DB::table('materiaal_categorieen')->where('naam', $category)->value('id');
            if (! $catId) {
                continue;
            }

            foreach ($subcats as $subName => $items) {
                $subId = DB::table('materiaal_subcategorieen')
                    ->where('materiaal_categorie_id', $catId)
                    ->where('naam', $subName)
                    ->value('id');

                if (! $subId) {
                    $subId = DB::table('materiaal_subcategorieen')->insertGetId([
                        'materiaal_categorie_id' => $catId,
                        'naam' => $subName,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                foreach ($items as $item) {
                    $rows[] = [
                        'materiaal_subcategorie_id' => $subId,
                        'naam' => $item,
                        'beschrijving' => '',
                        'belangrijk' => false,
                        'foto' => 'placeholder.png',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (! empty($rows)) {
            DB::table('materialen')->insert($rows);
        }
    }
}
