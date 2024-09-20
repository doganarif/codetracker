<div class="mt-10 px-4 sm:px-6 lg:px-8">
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
