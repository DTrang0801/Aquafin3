<x-site-layout>
    <div class="container text-center hero-box">
        <h1 class="page-title hero-title">Aquafin</h1>
        <p class="hero-team">Assia · Niels · Trang · Thien Y · Yassine</p>
    </div>

    <div style="text-align: center; margin-top:24px;padding:16px;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;">
        <p style="margin:0 0 8px 0;font-weight:600;color:#856404;">⚠️ Vergeet geen gasdetectiemateriaal!</p>
        <!-- <p style="margin:0 0 10px 0;color:#856404;font-size:14px;">Gasdetectiemeter</p> -->
        <form action="{{ route('winkelmandje.add') }}" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="materiaal_id" value="59">
            <input type="hidden" name="aantal" value="1">
            <button type="submit" style="background:#2563eb;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">➕ Toevoegen aan mandje</button>
        </form>
    </div>
</x-site-layout>