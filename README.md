<img width="443" height="114" alt="image" src="https://github.com/user-attachments/assets/3451445e-77f7-4569-a5c8-556b183e6588" /> <br>
# Aquafin | Programming project | Groep 3
Dit is een tool voor Aquafin waarin techniekers materiaal kunnen bestellen op basis van de voorspelde neerslag van het huidig seizoen en waar stockbeheerders het materiaal kunnen beheren.

## Inhoudsopgave
- [Teamleden](#teamleden)
- [Tech-stack](#tech-stack)
- [Documentatie](#documentatie)
  - [Database](#database)
  - [Folderstructuur](#folderstructuur)
  - [User rollen en permissies](#user-rollen-en-permissies)
  - [Gebruik API](#gebruik-api)
  - [Paginas](#paginas)
  - [Overstromings voorspelling & Risico](#overstromings-voorspelling--risico)
  - [Controllers](#controllers)
  - [Enums](#enums)
  - [Services](#services)

## Teamleden
- **Jouhri Assia** - Frontend, Backend, Afbeeldingen, User-Stories
- **Filali Yassine** - Loginsysteem, Frontend, Backend
- **Nguyen Trang** - User-Stories, Frontend, Backend, Prototype
- **Nguyen Thien Y**  - Frontend, User-roles, User-stories, Prototype
- **Tanghe Niels** - Database, API, Frontend, Backend, Hosting, Trello

## Tech-stack

**Backend:**
- **PHP** 8.4
- **Laravel Framework** 13

**Frontend:**
- **Blade Templates** - Server-side templating
- **Alpine.js** 3 - JavaScript framework
- **Tailwind CSS** 3 - CSS framework
- **Vite** - Frontend build tool

**Database:**
- **SQLite** - Database

**Testen & Ontwikkeling:**
- **Pest** 4 - PHP testing framework
- **PHPUnit** 12 - Unit testing framework

**API Integratie:**
- **Open-Meteo API** - Gratis weer- en neerslaggegevens API

## Setup

### Vereisten
- PHP 8.4 of hoger
- Composer
- Node.js & npm
- SQLite support

### Installatie

1. **Clone de repository**
   ```bash
   git clone https://github.com/DTrang0801/Aquafin3
   cd aquafin
   ```

2. **Installeer PHP dependencies**
   ```bash
   composer install
   ```

3. **Installeer Node.js dependencies**
   ```bash
   npm install
   ```

4. **Stel omgevingsvariabelen in**
   ```bash
   cp .env.example .env
   ```

5. **Genereer applicatiesleutel**
   ```bash
   php artisan key:generate
   ```

6. **Voer database migraties uit**
   ```bash
   php artisan migrate
   ```

7. **Seed de database (optioneel)**
   ```bash
   php artisan db:seed
   ```

8. **Build frontend**
   ```bash
   npm run build
   ```

### De applicatie uitvoeren

**Dev mode:**
```bash
php artisan serve
npm run dev
```

**Production:**
```bash
npm run build
```

### Tests uitvoeren

```bash
php artisan test --compact
```

Een specifiek testbestand uitvoeren:
```bash
php artisan test --compact tests/Feature/YourFeatureTest.php
```

### Commando's

Haal ontbrekende neerslaggegevens op van Open-Meteo:
```bash
php artisan app:fetch-all-missing-months
```

Archiveer de neerslaggegevens van de vorige maand:
```bash
php artisan app:archive-past-month
```

Bekijk alle routes:
```bash
php artisan route:list
```

# Documentatie

## Database
### ERD
<img width="1304" height="1588" alt="ERD-AQUAFIN" src="https://github.com/user-attachments/assets/829a8efa-d452-4297-9615-4201e664cb52" />


## Folderstructuur
<img width="307" height="954" alt="image" src="https://github.com/user-attachments/assets/9d5b4e0b-8d8e-4821-aedb-b9170e60ecae" />


## User rollen en permissies
### Technieker
De technieker is een werknemer van aquafin die elke dag op de baan is. Hij kan materiaal bestellen op deze applicatie zodat hij elke week goed voorbereid is.
### Stockbeheerder
De stockbeheerder beheerd welk materiaal er te beschilling is voor de techniekers. Ook kan de stockbeheerder bepalen welk materiaal belangrijk is wanneer er een overstromingsrisico is door naar de risicoanalyses te kijken.
### Admin
De admin is de beheerder van de gebruikers en de rollen binnen de applicatie. Hij kan gebruikers bewerken, toevoegen, of aanpassen. De admin kan geen zelf geen bestellingen plaatsen.

## Gebruik API
### Open-Meteo
We gebruiken de gratis API van [Open-Meteo](https://open-meteo.com/). om relevante data over de huidige, toekomstige en vorige neerslag op te halen. deze data word gebruikt om onze database aan te vullen of om de techniekers te informeren over hoeveel neerslag ze kunnen verwachten de komende week.

## Paginas
Een overzicht van elke pagina in onze applicatie.
### Hoofd pagina
**TECHNIEKER**
<img width="1876" height="877" alt="image" src="https://github.com/user-attachments/assets/8552b860-bbe9-4795-828f-7ca93fe9dce7" />
Je krijgt een weersvoorspelling voor de komende 7 dagen te zien en ook een reminder om je gasdetectiemateriaal niet te vergeten.

### Winkelmandje
**TECHNIEKER**
<img width="1919" height="990" alt="image" src="https://github.com/user-attachments/assets/7bf3aac7-52ed-473e-8b60-62434f6dc69c" />
Hier kan de technieker het materiaal bekijken dat in zijn winkelmandje zit, en de hoeveelheden aanpassen of items verwijderen.

### Materialen bestellen
**TECHNIEKER**
<img width="1880" height="884" alt="image" src="https://github.com/user-attachments/assets/869970c2-bd05-43f4-aa8a-b634071f958c" />
Hier kan de technieker materiaal kiezen om te bestellen door het toe te voegen aan zijn mandje. Hij kan ook materiaal opzoeken via de zoekbalk of filteren op categorie.

### Suggesties
**TECHNIEKER**
<img width="1920" height="1034" alt="image" src="https://github.com/user-attachments/assets/1a1a2dc7-cc83-4c6e-9ea8-5f78a738af5f" />
Toont materiaal dat gerelateerd is aan het materiaal dat net is toegevoegd aan het winkelmandje.

### Vorige bestellingen
**TECNIEKER**
<img width="1905" height="879" alt="image" src="https://github.com/user-attachments/assets/4a81c8ae-ff86-4bba-9d1a-3ecd69eb758f" />
Je kunt via deze pagina een overzicht krijgen van al uw vorige bestellingen. Je kan hier ook in zoeken met de zoekbalk.

### Herbestellen
**TECNIEKER**
<img width="920" height="398" alt="image" src="https://github.com/user-attachments/assets/83282bea-2dcd-4b9b-95b2-405894068533" />
Hier kan je op de button herbestellen drukken om dezelfde bestelling te plaatsen je kan de tijdstip wel kiezen en dan afronden.


### Bestelling bevestigen
**TECHNIEKER**
<img width="1916" height="955" alt="image" src="https://github.com/user-attachments/assets/e4cefd58-3c47-4e0a-a142-e19b379b0d55" />
Hier kan de technieker de items uit zijn winkelmandje bestellen. Hij kan hier nog extra info toevoegen zoals een aangepaste leverlocatie, datum of een opmerking.
Je kan ook zien welk materiaal je zal bestellen.

### Bestelling bewerken
**TECHNIEKER** + **STOCKBEHEERDER**
<img width="1920" height="1513" alt="image" src="https://github.com/user-attachments/assets/93f4cd69-e3f4-4c10-ae91-fd16eda1b1f8" />
Hier kan een technieker zijn eigen bestelling bewerken of kan de stockbeheerder eenders welke bestelling bewerken.

### Neerslag
**STOCKBEHEERDER**  +  **ADMIN**
<img width="1920" height="3875" alt="image" src="https://github.com/user-attachments/assets/b243e77c-f4dd-4cfd-99bb-44178dd12a3f" />
Hier kan de stockbeheerder de huidige neerslag bekijken, de neerslag van de afgelopen maand en de neerslag voor de komende 14 dagen. Ook kan hij kijken naar een overzich van overstomingsrisicos per seizoen voor de komende 5 jaar. Ook kan hij de neerslag van de laatste 3 en 1 maand bekijken en het huidig risico op overstroming

### Overzicht bestellingen
**STOCKBEHEERDER**
<img width="1920" height="1034" alt="image" src="https://github.com/user-attachments/assets/42b7bb02-2c01-41e4-bece-c7c327a9ab2c" />
Hier kan de stockbeheerder alle bestellingen bekijken die de techniekers hebben geplaatst. Hij kan zoeken of filteren op periode.

### Bestelling annuleren
**TECHNIEKER** + **STOCKBEHEERDER** + **ADMIN**
- Een **technieker** kan enkel zijn eigen bestellingen annuleren tot 1 dag na het plaatsen.
- Een **stockbeheerder** of **admin** kan elke bestelling annuleren.
- Bij annuleren krijg je een bevestigingspop-up en daarna een succesmelding.
- Geannuleerde bestellingen worden standaard verborgen.
- Via een knop met een oogicoon kan je ze tonen of verbergen.
- Als je op de knop klikt, worden enkel de geannuleerde bestellingen getoond.

### Beheer materiaal
**STOCKBEHEERDER**
<img width="1919" height="986" alt="image" src="https://github.com/user-attachments/assets/2a993c2a-b4ba-4b1a-8d26-45d4d416634a" />
Hier kan de stockbeheerder materiaal aanpassen, toevoegen of verwijderen.

### Materiaal bewerken
**STOCKBEHEERDER**
<img width="1917" height="985" alt="image" src="https://github.com/user-attachments/assets/56032df9-1631-4b74-b2ef-d6244e2ec058" />
Hier kan de stockbeheerder een gekozen materiaal bewerken. Hij kan dingen aanpassen zoals de naam, categorie, afbeelding, beschrijving...

### Materiaal toevoegen
**STOCKBEHEERDER**
<img width="1912" height="990" alt="image" src="https://github.com/user-attachments/assets/f1ca7ad9-c598-4d34-b3a4-5441931fcdd0" />
Hier kan de stockbeheerder materiaal toevoegen.

### Meest bestelde items
**STOCKBEHEERDER**
<img width="1919" height="989" alt="image" src="https://github.com/user-attachments/assets/37cb733d-124b-443a-a1c0-88821da0b2bb" />
Hier kan de stockbeheerder bekijken welk materiaal er het meest bestelt word.

### Kritieke items beheren
**STOCKBEHEERDER**
<img width="1920" height="1033" alt="image" src="https://github.com/user-attachments/assets/261ea4e3-08af-4655-a019-4a9eda6eef77" />
Hier kan de stockbeheerder beslissen welk materiaal er belagrijk is tijdens een perioode van hoog of gemmideld overstromingsrisico.

### Materielen
<img width="1900" height="827" alt="image" src="https://github.com/user-attachments/assets/7fca00fe-f82a-4ed2-a15e-4a1fbf27c01e" />
Je ziet de Materialen overzicht van alle beschikbare materialen op de website.

### Gebruikers 
<img width="1892" height="823" alt="image" src="https://github.com/user-attachments/assets/338ee931-9483-40bc-b29d-d56fe4172c90" />
Op deze pagina ziet de admin alle gebruikers, hij kan een nieuwe gebruiker toevoegen, hij kan bestaande gebruikers bewerken of verwijderen.



## Overstromings voorspelling & Risico

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

#### Stap 5: Risico-niveaus
Elke seizoen wordt vergeleken met een drempelwaarde voor overstromingsgevaar:
- Winter: 300 mm
- Lente: 250 mm
- Zomer: 260 mm
- Herfst: 280 mm

Afhankelijk van het verschil met de drempel krijgt de seizoen een risiconiveau:
- **Laag**: onder de drempel
- **Gemiddeld**: gelijk aan drempel tot 20% boven (100% - 119%)
- **Hoog**: 20% of meer boven de drempel (≥ 120%)

#### Stap 6: Jaarlijks risico berekenen
Voor elk jaar in de 5-jaarsperiode wordt het totale risico bepaald door het aantal seizoenen met verhoogd risico:
- 0 seizoenen: Laag risico
- 1 seizoen: Gemiddeld risico
- 2+ seizoenen: Hoog risico

#### Stap 7: Graduele materiaal markering
Het systeem kent elk materiaal een minimum risiconiveau toe (Gemiddeld of Hoog):
- **Gemiddeld**: Materiaal wordt gemarkeerd bij Gemiddeld risico of hoger
- **Hoog**: Materiaal wordt alleen gemarkeerd bij Hoog risico

### Beperkingen
De voorspelling is gebaseerd op historische patronen en trends. Dit betekent:
- De toekomst volgt niet altijd het verleden
- Onvoorziene klimaatveranderingen kunnen het patroon verstoren
- Korte-termijn extrema kunnen niet worden voorspeld

## Controllers

### CartController
Beheert het winkelmandje en bestellingen. Voegt materiaal toe/verwijdert het, verwerkt bestellingen, geeft suggesties, en beheerd de bestelgeschiedenis. Stockbeheerders kunnen bestellingen annuleren en bewerken.

### MateriaalController
Beheer van materialen. Toont materiaaloverzicht met zoeken en filters, maakt materiaal aan/bewerkt/verwijdert. Voor stockbeheerders: beheer van alle materialen en hun eigenschappen.

### HomeController
Startpagina logica. Toont weersvoorspelling (7 dagen) en herinneringen voor techniekers. Cached gegevens 30 minuten voor performance. Refresh-functionaliteit beschikbaar.

### WeatherController
Neerslag en overstromingsrisicobeheersing. Toont huidige/voorspelde neerslag, 5-jaarprognose, en historische data. Beheert kritieke materialen gekoppeld aan overstromingsgevaar. Simulatiemodus voor testen.

### StockDashboardController
Stockdashboard voor beheerders. Toont top 20 meest bestelde materialen met ordergeschiedenis.

## Enums

### FloodRiskLevel
Vertegenwoordigt de drie risiconiveaus van het overstroomingssysteem:
- 'laag': Neerslag onder 100% van seizoensdrempel
- 'gemiddeld': Neerslag 100-119% van seizoensdrempel
- 'hoog': Neerslag 120% of meer van seizoensdrempel

## Services

### OpenMeteoService
Integratie met Open-Meteo API voor weersgegevens. Haalt huidige en historische neerslag op, parsed voorspellingen voor weergave, berekent maandelijkse neerslagtotalen.

### FloodRiskService
Detecteert overstromingsrisico door seizoensgebonden neerslag te vergelijken met drempels. Retourneert een `FloodRiskLevel` (Laag/Gemiddeld/Hoog). Markeert kritieke materialen op basis van hun minimale risiconiveau: een materiaal met niveau "Hoog" wordt alleen gemarkeerd bij Hoog risico, terwijl "Gemiddeld" materiaal al bij Gemiddeld risico wordt gemarkeerd. Beheert gekoppelde materialen, archiveert historische neerslag.

### FloodRiskAnalysisService
5-jaars trendanalyse met lineaire regressie. Projecteert toekomstige neerslag per seizoen, berekent risiconiveaus, analyseert huidge seizoenvoortgang tegen drempels.
