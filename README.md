# Aquafin | Programming project | Groep 3
Dit is een tool voor Aquafin waarin techniekers materiaal kunnen bestellen op basis van de voorspelde neerslag van het huidig seizoen en waar stockbeheerders de stock van het materiaal kunnen beheren.

## Tech-stack
Frontend en backend is laravel + blade. Verbonden met een sql database.

# Documentatie

## Project structuur
Aquafin3/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/
│   ├── Enums/
│   └── Providers/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── pages/
│   │   ├── layouts/
│   │   └── components/
│   ├── css/
│   └── js/
├── routes/
├── storage/
│   └── app/public/
│       └── materialen/
├── public/
│   ├── storage/
│   ├── css/
│   ├── images/
│   └── build/
├── config/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env
├── composer.json
└── package.json

## Database
### ERD
<img width="1304" height="949" alt="Diagram" src="https://github.com/user-attachments/assets/09455c3c-70eb-480f-b856-52626bf0733b" />

## User rollen en permissies
### Technieker
### Stockbeheerder
### Admin

## Paginas

### Hoofd pagina
(TECHNIEKER)
<img width="1876" height="877" alt="image" src="https://github.com/user-attachments/assets/8552b860-bbe9-4795-828f-7ca93fe9dce7" />
Je komt op deze pagina terecht wannner je inlogd op de technieker account je krijgt de 7 daagse meteo te zien en ook een herhaling van de gasdetectiemateriaal

### Winkelmandje
(TECHNIEKER)
<img width="1919" height="990" alt="image" src="https://github.com/user-attachments/assets/7bf3aac7-52ed-473e-8b60-62434f6dc69c" />
Hier kan de technieker het materiaal bekijken dat in zijn winkelmandje zit, en hoeveelheden aanpassen of items verwijderen.

### Materialen bestellen
(TECHNIEKER)
<img width="1880" height="884" alt="image" src="https://github.com/user-attachments/assets/869970c2-bd05-43f4-aa8a-b634071f958c" />
Hier kan de technieker materiaal bestellen en in het mandje, materiaal opzoeken via de zoekbalk of filteren op categorie.

### Vorige bestellingen
(TECNIEKER)
<img width="1905" height="879" alt="image" src="https://github.com/user-attachments/assets/4a81c8ae-ff86-4bba-9d1a-3ecd69eb758f" />
Je kunt via deze pagina de overzicht krijgen van al u bestellingen en je kunt ze ook opzoeken.

 




## Overstromings voorspelling

### Hoe werkt het?

De applicatie voert een 5-jaars voorspelling uit voor overstromingsrisico's op basis van historische gegevens en trends.

#### Stap 1: Gegevens verzamelen
Het systeem gebruikt neerslaggegevens uit de periode 2004-2025. Deze data is opgeslagen per maand en per jaar in de database.

#### Stap 2: Seizoen-gegevens groeperen
De applicatie groepeert de maanden per seizoen:
- Winter: December, Januari, Februari
- Lente: Maart, April, Mei
- Zomer: Juni, Juli, Augustus
- Herfst: September, Oktober, November

Voor elk seizoen in de historische data wordt de totale neerslag per jaar berekend.

#### Stap 3: Trendanalyse
Voor elk seizoen wordt een lineaire regressie uitgevoerd op de historische gegevens om te bepalen:
- **Trend (helling)**: Of de neerslag toeneemt of afneemt per jaar (mm/jaar)
- **Gemiddelde**: De gemiddelde neerslag over de gehele periode
- **Standaarddeviatie**: De variabiliteit in de historische data

#### Stap 4: Toekomstige neerslag voorspellen
Voor elk jaar in de 5-jaarsperiode wordt de verwachte neerslag berekend op basis van:
1. Het historische gemiddelde
2. De trendlijn (trend × aantal jaren in de toekomst)
3. Cyclische variatie gebaseerd op standaarddeviatie (zorgt voor realistische jaar-tot-jaar schommelingen)

**Formule**: `Projectie = Gemiddelde + (Trend × JarenInToekomst) + Variance`

#### Stap 5: Risico
Elke seizoen wordt vergeleken met een drempelwaarde voor overstromingsgevaar:
- Winter: 300 mm
- Lente: 250 mm
- Zomer: 260 mm
- Herfst: 280 mm

Afhankelijk van het verschil met de drempel krijgt de seizoen een risiconiveau:
- Laag: onder de drempel
- Gemiddeld: gelijk aan drempel tot 20% boven
- Hoog: 20% of meer boven de drempel

#### Stap 6: Jaarlijks risico berekenen
Voor elk jaar in de 5-jaarsperiode wordt het totale risico bepaald door het aantal seizoenen met verhoogd risico:
- 0-1 seizoen: Laag risico
- 2-3 seizoenen: Gemiddeld risico
- 4 seizoenen: Hoog risico

### Beperkingen
De voorspelling is gebaseerd op historische patronen en trends. Dit betekent:
- De toekomst volgt niet altijd het verleden
- Onvoorziene klimaatveranderingen kunnen het patroon verstoren
- Korte-termijn extrema kunnen niet worden voorspeld

## Teamleden
- JOUHRI Assia - Frontend, Backend, Afbeeldingen, User-Stories
- FILALI Yassine - Loginsysteem, Frontend, Backend
- NGUYEN Trang - User-Stories, Frontend, Backend, Prototype
- NGUYEN Thien - Frontend, User-roles, User-stories, Prototype
- TANGHE Niels - Database, API, Frontend, Backend, Hosting, Trello
