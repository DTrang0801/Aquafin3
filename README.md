# Aquafin | Programming project | Groep 3
Dit is een tool voor Aquafin waarin techniekers materiaal kunnen bestellen op basis van de voorspelde neerslag van het huidig seizoen en waar stockbeheerders de stock van het materiaal kunnen beheren.

## Tech-stack
Frontend en backend is laravel + blade. Verbonden met een sql database.

## 5-Jaars Overstromings voorspelling

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
- JOUHRI Assia
- FILALI Yassine
- NGUYEN Trang
- NGUYEN Thien
- TANGHE Niels
