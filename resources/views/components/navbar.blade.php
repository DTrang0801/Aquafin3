<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-800">Aquafin</a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Materiaal bestellen</a>

                @if(Auth::user()?->role_id === \App\Models\Role::STOCKBEHEERDER)
                    <a href="{{ route('materialen.create') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Nieuw materiaal
                    </a>
                @endif

                <a href="{{ route('weersvoorspelling') }}" class="text-sm text-gray-600 hover:text-gray-900">Neerslag</a>

                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Mandje</a>

                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Profiel</a>

                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Uitloggen</a>
            </div>
        </div>
    </div>
</nav>
