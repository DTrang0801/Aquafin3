<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Materiaallijst</h1>

        @foreach ($categorieen as $categorie)
            <div x-data="{ openCategory: false }" class="mb-6 border border-gray-200 rounded-lg bg-gray-50 shadow-sm">
                
                <button @click="openCategory = !openCategory" 
                        class="w-full flex justify-between items-center p-4 bg-gray-100 hover:bg-gray-200 text-left rounded-t-lg transition font-semibold text-blue-700 uppercase tracking-wider">
                    <span>{{ $categorie->naam }}</span>
                    <span x-show="openCategory">▲</span>
                    <span x-show="!openCategory">▼</span>
                </button>

                <div x-show="openCategory" x-transition class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    
                    @foreach ($categorie->subcategorieen as $subcategorie)
                        <div x-data="{ openSub: false }" class="ml-4 mb-4 border border-gray-100 rounded">
                            
                            <button @click="openSub  = !openSub" 
                                    class="w-full flex justify-between items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 text-left text-sm font-medium text-gray-800 italic">
                                <span>→ {{ $subcategorie->naam }}</span>
                                <span class="text-xs text-gray-500" x-text="openSub ? '[ Verberg ]' : '[ Toon ]'"></span>
                            </button>

                            <div x-show="openSub" x-transition class="p-3 bg-white">
                                @if($subcategorie->materialen->isEmpty())
                                    <p class="text-sm text-gray-500 ml-4">Geen materialen in deze subcategorie.</p>
                                @else
                                    <table class="min-w-full bg-white border border-gray-200 rounded shadow-sm">
                                        <thead class="bg-gray-100 text-left text-xs uppercase text-gray-700">
                                            <tr>
                                                <th class="py-2 px-4 border-b">Naam</th>
                                                <th class="py-2 px-4 border-b">Beschrijving</th>
                                                <th class="py-2 px-4 border-b w-32">Belangrijk?</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm">
                                            @foreach ($subcategorie->materialen as $materiaal)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-2 px-4 border-b font-medium">{{ $materiaal->naam }}</td>
                                                    <td class="py-2 px-4 border-b text-gray-600">{{ $materiaal->beschrijving ?? 'Geen beschrijving' }}</td>
                                                    <td class="py-2 px-4 border-b">
                                                        <span class="px-2 py-0.5 text-xs rounded {{ $materiaal->belangrijk ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                            {{ $materiaal->belangrijk ? 'Ja' : 'Nee' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>