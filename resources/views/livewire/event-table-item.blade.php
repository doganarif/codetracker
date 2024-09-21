<tr>
    <!-- Event Type -->
    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
        <div class="flex items-center">
            <x-heroicon-o-calendar class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0"/>
            <span class="text-sm sm:text-base font-semibold truncate">
                @switch($eventType)
                    @case('PushEvent')
                        Push Commit
                        @break
                    @case('PullRequestEvent')
                        Create Pull Request
                        @break
                    @default
                        {{ $eventType }}
                @endswitch
            </span>
        </div>
    </td>
    <!-- Repo Name -->
    <td class="px-3 py-4 text-sm text-gray-500 max-w-[150px]">
        <div class="truncate" title="{{ $repoName }}">{{ $repoName }}</div>
    </td>
    <!-- Event Title -->
    <td class="px-3 py-4 text-sm text-gray-500 max-w-[200px]">
        <div class="truncate" title="{{ $eventTitle }}">{{ $eventTitle }}</div>
    </td>
    <!-- Event Description -->
    <td class="px-3 py-4 text-sm text-gray-500 max-w-[300px]">
        @if($eventDescription)
            <div class="truncate" title="{{ $eventDescription }}">{{ $eventDescription }}</div>
        @else
            <span class="text-gray-400 italic">No description</span>
        @endif
    </td>
    <!-- Event Date -->
    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
        {{ \Carbon\Carbon::parse($eventDate)->format('F j, Y g:i A') }}
    </td>
    <!-- Detail Button -->
    <td class="py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 whitespace-nowrap">
        <a
            href="{{ route('events.detail', $eventId) }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-1 px-3 rounded-lg shadow-lg focus:ring focus:ring-blue-200 transition-all duration-300 ease-in-out"
        >
            View Details
        </a>
    </td>
</tr>
