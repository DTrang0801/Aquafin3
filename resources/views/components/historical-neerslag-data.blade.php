<div class="historical-neerslag-section weather-card">
    <div class="section-header">
        <h2 class="section-heading">Historische neerslaggegevens</h2>
        <p class="section-description">
            Alle opgeslagen maandelijkse neerslagmetingen in de database.
        </p>
    </div>

    @if($historicalNeerslagData && $historicalNeerslagData->count() > 0)
        <div class="table-wrapper">
            <table class="custom-table neerslag-table">
                <thead>
                    <tr>
                        <th>Jaar</th>
                        <th>Maand</th>
                        <th>Neerslag (mm)</th>
                        <th>Opgeslagen op</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historicalNeerslagData as $neerslag)
                        @php
                            $monthNames = ['', 'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'];
                        @endphp
                        <tr>
                            <td class="table-center font-bold">{{ $neerslag->jaar }}</td>
                            <td class="table-center">{{ $monthNames[$neerslag->maand] }}</td>
                            <td class="table-center">
                                <span class="neerslag-badge">{{ $neerslag->mm }} mm</span>
                            </td>
                            <td class="table-center text-small">
                                {{ $neerslag->updated_at ? $neerslag->updated_at->format('d-m-Y H:i') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <p class="empty-state-title">Geen gegevens beschikbaar</p>
            <p class="empty-state-subtitle">Voeg maandelijkse neerslaggegevens toe via het formulier hierboven.</p>
        </div>
    @endif
</div>

<style>
    .historical-neerslag-section {
        margin-top: 1rem;
    }

    .table-wrapper {
        height: 300px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: 8px;
    }

    .neerslag-table {
        width: 100%;
        border-collapse: collapse;
    }

    .neerslag-table thead {
        background: var(--bg-light);
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .neerslag-table th {
        padding: 0.5rem;
        text-align: center;
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.8125rem;
    }

    .neerslag-table td {
        padding: 0.5rem;
        border-bottom: 1px solid var(--border);
        color: var(--text-medium);
        font-size: 0.8125rem;
    }

    .neerslag-table tbody tr:hover {
        background-color: rgba(37, 99, 235, 0.02);
    }

    .table-center {
        text-align: center;
    }

    .font-bold {
        font-weight: 600;
        color: var(--text-dark);
    }

    .text-small {
        font-size: 0.75rem;
        color: var(--text-lighter);
    }

    .neerslag-badge {
        display: inline-block;
        background: var(--primary-light);
        color: var(--primary-dark);
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .empty-state {
        text-align: center;
        padding: 1.5rem;
        color: var(--text-light);
    }

    .empty-state-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-medium);
        margin: 0 0 0.25rem 0;
    }

    .empty-state-subtitle {
        font-size: 0.8125rem;
        color: var(--text-light);
        margin: 0;
    }
</style>
