<div class="mt-10 px-4 sm:px-6 lg:px-8">
    <!-- Active Challenge Section -->
    @if ($activeChallenge)
        <div class="bg-green-50 shadow-md rounded-lg p-6 mb-8 w-full">
            <h2 class="text-2xl font-bold text-green-800 mb-4">Your Active Challenge</h2>
            <div class="flex items-center space-x-4">
                <x-heroicon-o-trophy class="w-10 h-10 text-green-600"/>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $activeChallenge->name }}</h3>
                    <p class="text-gray-600">{{ $activeChallenge->description }}</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm text-gray-500">
                    <strong>Start Date:</strong> {{ $activeChallenge->start_date->format('F j, Y g:i A') }}<br>
                    <strong>End Date:</strong> {{ $activeChallenge->end_date->format('F j, Y g:i A') }}
                </p>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <h4 class="text-lg font-semibold text-gray-700">Your Progress</h4>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-gray-200">
                        <div style="width: {{ $progress }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div>
                    </div>
                    <p class="text-sm text-gray-600">{{ round($progress, 2) }}% completed</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Challenges Section -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold leading-6 text-gray-900">Challenges</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all the challenges including their name, status, start date, and end date. You can manage the challenges from here.</p>
        </div>
    </div>

    <!-- Scrollable Table Container -->
    <div class="mt-8 flow-root bg-white shadow-lg rounded-lg">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <!-- Table with relative height to leave some space from the bottom -->
                    <div class="overflow-y-auto" style="max-height: calc(100vh - 200px);">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Challenge</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Start Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">End Date</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($challenges as $challenge)
                                <livewire:challenge-card
                                    :key="'challenge-'.$challenge->id"
                                    :challengeId="$challenge->id"
                                    :challengeName="$challenge->name"
                                    :description="$challenge->description"
                                    :startDate="$challenge->start_date"
                                    :endDate="$challenge->end_date"
                                    :isActive="in_array($challenge->id, $acceptedChallenges)"
                                    :isOutdated="$challenge->isOutdated"
                                />
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
