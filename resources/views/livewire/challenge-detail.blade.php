<div class="w-full px-4 sm:px-6 lg:px-8 py-12">
    <!-- Challenge Card -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8 w-full">
        <!-- Challenge Header -->
        <div class="flex items-center justify-between mb-6 w-full">
            <div class="flex items-center space-x-3">
                <x-heroicon-o-trophy class="w-8 h-8 text-yellow-500"/>
                <h1 class="text-3xl font-bold text-gray-900">{{ $challenge->name }}</h1>
            </div>

            <!-- Challenge Status -->
            @php
                $isOutdated = now()->gt($challenge->end_date);
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{ $isOutdated ? 'bg-red-100 text-red-800' : ($challenge->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                @if ($isOutdated)
                    <x-heroicon-o-clock class="w-5 h-5 mr-2 text-red-500"/>
                    Outdated
                @else
                    <x-heroicon-o-check-circle class="w-5 h-5 mr-2 text-green-500"/>
                    {{ $challenge->is_active ? 'Active' : 'Inactive' }}
                @endif
            </span>
        </div>

        <!-- Challenge Description -->
        <div class="border-t border-gray-200 pt-4 w-full">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Description</h2>
            <p class="text-gray-600 text-base leading-relaxed">{{ $challenge->description }}</p>
        </div>

        <!-- Challenge Dates -->
        <div class="border-t border-gray-200 pt-4 mt-6 w-full">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Dates</h2>
            <p class="text-gray-600">
                <strong>Start Date:</strong> {{ $challenge->start_date->format('F j, Y g:i A') }}<br>
                <strong>End Date:</strong> {{ $challenge->end_date->format('F j, Y g:i A') }}
            </p>
        </div>
    </div>

    <!-- Accepted Users Card -->
    <div class="bg-white shadow-md rounded-lg p-6 w-full">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Accepted Users</h2>
        @if ($challenge->users->count() > 0)
            <div class="grid grid-cols-5 gap-4 w-full">
                @foreach ($challenge->users as $user)
                    <div class="text-center">
                        <div class="relative w-16 h-16 bg-gray-200 rounded-full overflow-hidden mx-auto">
                            @if ($user->profile_picture)
                                <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" class="object-cover w-full h-full">
                            @else
                                <span class="flex items-center justify-center w-full h-full text-gray-800 font-bold text-xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-700">{{ $user->name }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No users have accepted this challenge yet.</p>
        @endif
    </div>
</div>

