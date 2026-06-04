<x-site-layout>

    <div class="container favorieten-pagina">
        <h1 class="title">Favorieten / Populaire Materialen</h1>
        <p class="page-subtitle">
            Hieronder vind je de populairste materialen van dit seizoen. Deze producten worden vaak gekozen door techniekers voor onderhoud, herstellingen en aansluitingen.
        </p>

        <section class="seizoen-blok">
            <h2>Winterfavorieten</h2>
            <div class="favorieten-grid">
                <div class="favoriet-kaart">
                    <h3>PVC Buis 50mm</h3>
                    <p>Geschikt voor waterafvoer bij nat en koud weer.</p>
                    <button>Voeg toe aan mandje</button>
                </div>

                <div class="favoriet-kaart">
                    <h3>Waterdichte koppeling</h3>
                    <p>Populair voor lekvrije aansluitingen in regenachtige periodes.</p>
                    <button>Voeg toe aan mandje</button>
                </div>

                <div class="favoriet-kaart">
                    <h3>Afsluitklep</h3>
                    <p>Handig bij onderhoud en tijdelijke afsluiting van leidingen.</p>
                    <button>Voeg toe aan mandje</button>
                </div>
            </div>
        </section>

        <section class="seizoen-blok">
            <h2>Lentefavorieten</h2>
            <div class="favorieten-grid">
                <div class="favoriet-kaart">
                    <h3>Koppeling 32mm</h3>
                    <p>Veel gebruikt bij nieuwe aansluitingen en kleine herstellingen.</p>
                    <button>Voeg toe aan mandje</button>
                </div>

                <div class="favoriet-kaart">
                    <h3>Controleput deksel</h3>
                    <p>Belangrijk voor inspectie en toegang tot leidingsystemen.</p>
                    <button>Voeg toe aan mandje</button>
                </div>

                <div class="favoriet-kaart">
                    <h3>Flexibele afvoerbuis</h3>
                    <p>Ideaal voor snelle plaatsing en eenvoudige verbindingen.</p>
                    <button>Voeg toe aan mandje</button>
                </div>
            </div>
        </section>

        <section class="seizoen-blok">
            <h2>Zomerfavorieten</h2>
            <div class="favorieten-grid">
                <div class="favoriet-kaart">
                    <h3>Irrigatiebuis</h3>
                    <p>Vaak gekozen voor waterverdeling en buiteninstallaties.</p>
                    <button>Voeg toe aan mandje</button>
                </div>

                <div class="favoriet-kaart">
                    <h3>Pompverbinding</h3>
                    <p>Geschikt voor systemen met hogere doorstroming.</p>
                    <button>Voeg toe aan mandje</button>
                </div>

                <div class="favoriet-kaart">
                    <h3>Filterelement</h3>
                    <p>Helpt om installaties proper en efficiënt te houden.</p>
                    <button>Voeg toe aan mandje</button>
                </div>
            </div>
        </section>
    </div>

    <style> 
    .favorieten-pagina {
        padding: 40px 20px 60px;
        color: #1f2937;
    }
    
    .page-title {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .page-subtitle {
        max-width: 800px;
        margin-bottom: 30px;
        color: #374151;
        font-size: 1rem;
    }
    
    .seizoen-blok {
        margin-bottom: 40px;
    }
    
    .seizoen-blok h2 {
        margin-bottom: 20px;
        color: #0f172a;
    }
    
    .favorieten-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .favoriet-kaart {
        background: rgba(255, 255, 255, 0.92);
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
    }
    
    .favoriet-kaart h3 {
        margin-bottom: 10px;
        font-size: 1.2rem;
    }
    
    .favoriet-kaart p {
        margin-bottom: 12px;
        color: #4b5563;
    }
    
    .tag {
        display: inline-block;
        margin-bottom: 14px;
        padding: 6px 12px;
        background-color: #dbeafe;
        color: #1d4ed8;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .favoriet-kaart button {
        background-color: #2563eb;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
    
    .favoriet-kaart button:hover {
        background-color: #1d4ed8;
    }

    </style>

</x-site-layout>