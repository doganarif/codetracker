<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $event->title }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ $event->repo_name }}
            </p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Event Type
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @switch($event->type)
                            @case('PushEvent')
                                Push Commit
                                @break
                            @case('PullRequestEvent')
                                Create Pull Request
                                @break
                            @default
                                {{ $event->type }}
                        @endswitch
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Event Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $event->event_date->format('F j, Y g:i A') }}
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Description
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($parsedDescription)
                            <div class="prose max-w-none">
                                {!! $parsedDescription !!}
                            </div>
                        @else
                            <p class="text-gray-500 italic">No description available</p>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- AI Advice Button and Display -->
    <div class="mt-6">
        <button wire:click="getAIAdvice" wire:loading.attr="disabled" class="text-white bg-indigo-600 hover:bg-indigo-800 px-4 py-2 rounded">
            Get AI Advice
        </button>

        <!-- Loading indicator while fetching AI advice -->
        <span wire:loading class="text-indigo-600 ml-2">Fetching advice...</span>
    </div>

    <!-- Display AI Advice -->
    @if ($aiAdvice)
        <div class="mt-4 p-4 bg-gray-100 rounded">
            <h4 class="text-lg font-semibold">AI Advice</h4>
            <div class="prose max-w-none mt-2">
                {!! $aiAdvice !!}
            </div>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('my-events') }}" class="text-indigo-600 hover:text-indigo-900">Back to Events</a>
    </div>
</div>
