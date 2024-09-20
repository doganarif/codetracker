<tr>
    <!-- Challenge Name -->
    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
        <div class="flex items-center">
            <x-heroicon-o-trophy class="w-6 h-6 text-yellow-500 mr-3"/>
            <span class="text-sm sm:text-base font-semibold">{{ $challengeName }}</span>
        </div>
    </td>

    <!-- Challenge Status -->
    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
        <span class="px-2 py-1 sm:px-3 sm:py-1 text-xs sm:text-sm font-medium rounded-full"
              :class="{
                'bg-green-100 text-green-700': {{ $isActive }} === true && !{{ $isOutdated }},
                'bg-gray-100 text-gray-700': {{ $isActive }} === false || {{ $isOutdated }},
                'bg-red-100 text-red-700': {{ $isOutdated }}
              }">
            @if ($isOutdated)
                Outdated
            @else
                {{ $isActive ? 'Active' : 'Inactive' }}
            @endif
        </span>
    </td>

    <!-- Start Date -->
    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
        {{ \Carbon\Carbon::parse($startDate)->format('F j, Y g:i A') }}
    </td>

    <!-- End Date -->
    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
        {{ \Carbon\Carbon::parse($endDate)->format('F j, Y g:i A') }}
    </td>

    <!-- Call-to-Action -->
    <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
        @if ($isOutdated)
            <span class="text-gray-500">Expired</span>
        @elseif (!$isActive && $hasStarted)
            <button
                wire:click="acceptChallenge"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-1 px-3 rounded-lg shadow-lg focus:ring focus:ring-indigo-200 transition-all duration-300 ease-in-out"
            >
                Accept
            </button>
        @elseif (!$hasStarted)
            <span class="text-gray-500">Not Started Yet</span>
        @else
            <button
                wire:click="cancelChallenge"
                class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1 px-3 rounded-lg shadow-lg focus:ring focus:ring-red-200 transition-all duration-300 ease-in-out"
            >
                Cancel
            </button>
        @endif
    </td>

    <!-- Detail Button -->
    <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
        <a href="{{ route('challenge.detail', $challengeId) }}"
           class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-1 px-3 rounded-lg shadow-lg focus:ring focus:ring-blue-200 transition-all duration-300 ease-in-out"
        >
            View Details
        </a>
    </td>
</tr>
