<x-site-layout>
    <div class="winkelmandje-container">
        <h1 class="winkelmandje-title">Winkelmandje</h1>
        <p class="winkelmandje-subtitle">Hier zie je een klein overzicht van de gekozen materialen.</p>

        <div class="producten-lijst">

            @if($materialen->isEmpty())
                <p>Je mandje is leeg.</p>
            @else
                @foreach($materialen as $materiaal)
                    <div class="product-kaart">

                        <div class="product-info">
                            <h2>{{ $materiaal->naam }}</h2>
                            <p>{{ $materiaal->omschrijving }}</p>
                        </div>

                        <div class="product-details">
                            <form action="{{ route('mandje.verlagen', $materiaal->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit">-</button>
                            </form>

                            <span>Aantal: {{ $materiaal->pivot->aantal }}</span>

                            <form action="{{ route('mandje.verhogen', $materiaal->id) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit">+</button>
                            </form>

                            <form action="{{ route('mandje.verwijderen', $materiaal->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button style="color: red; border: none; background: none; cursor: pointer;">X</button>
                            </form>
                        </div>

                    </div>
                @endforeach
            @endif

        </div>
    </div>

<style>
    .winkelmandje-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
        color: #1f2937;
    }

    .winkelmandje-title {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .winkelmandje-subtitle {
        margin-bottom: 30px;
        color: #4b5563;
    }

    .producten-lijst {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .product-kaart {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    .product-info h2 {
        font-size: 1.2rem;
        color: #111827;
        margin: 0;
    }

    .product-details {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
    }

    .product-details span {
        font-weight: 600;
        color: #111827;
        min-width: 80px;
        text-align: center;
    }

    .product-details button {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid #ccc;
        background: white;
        cursor: pointer;
        font-size: 1rem;
        font-weight: bold;
    }

    .product-details button:hover {
        background: #f3f4f6;
    }
</style>
</x-site-layout>