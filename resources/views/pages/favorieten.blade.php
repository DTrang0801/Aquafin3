<x-site-layout>

    <div class="container favorieten-pagina">
        <h1 class="title">Favorieten / Populaire Materialen</h1>
        <p class="page-subtitle">
            Hieronder vind je de populairste materialen van dit seizoen. Deze producten worden vaak gekozen door techniekers voor onderhoud, herstellingen en aansluitingen.
        </p>


    <div class="favorieten-grid">
        @foreach($favorieten as $materiaal)
            <div class="favoriet-kaart">
            <h3>{{ $materiaal->naam }}</h3>
            <p>{{ $materiaal->beschrijving ?? 'Geen beschrijving' }}</p>
            <form action="{{ route('mandje.toevoegen', $materiaal->id) }}" method="POST">
                @csrf
                <button type="submit">Voeg toe aan mandje</button>
            </form>
            </div>
        @endforeach
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

    .favorieten-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
    }

    </style>


</x-site-layout>