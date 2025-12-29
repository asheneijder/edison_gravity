<x-filament-panels::page>
    <div class="space-y-6">
        @if ($groupedMessages->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="p-3 bg-gray-50 rounded-full dark:bg-gray-800">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">No processed messages</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Run the batch processing job to see SWIFT messages here.</p>
            </div>
        @else
            @foreach ($groupedMessages as $date => $types)
                @php
                    $displayDate = \Carbon\Carbon::parse($date)->toFormattedDateString();
                    $totalCount = $types->flatten()->count();
                    // Generate a unique ID for the date accordion
                    $dateId = 'date-' . \Illuminate\Support\Str::slug($date);
                @endphp

                {{-- Date Group Card --}}
                <div x-data="{ expanded: false }" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                    
                    {{-- Date Header (Clickable) --}}
                    <button @click="expanded = !expanded" type="button" class="flex items-center justify-between w-full px-6 py-4 transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                        <div class="flex items-center gap-x-3">
                            {{-- Calendar Icon (Fixed Size) --}}
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <div class="text-left">
                                <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">Processed Batch</span>
                                <span class="block text-lg font-bold text-gray-900 dark:text-white">{{ $displayDate }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                {{ $totalCount }} Messages
                            </span>
                            {{-- Chevron Icon (Rotates) --}}
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                                 :class="expanded ? 'rotate-180' : ''"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </button>

                    {{-- Collapsible Content (Message Types) --}}
                    <div x-show="expanded" x-collapse style="display: none;" class="border-t border-gray-200 dark:border-gray-800">
                        <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($types as $type => $messages)
                                @php
                                    $shortType = strtok($type, ' ');
                                    $description = trim(substr($type, strlen($shortType)));
                                    $count = count($messages);
                                    
                                    // Extract columns for table
                                    $firstMsg = $messages->first();
                                    $columns = [];
                                    if ($firstMsg && is_array($firstMsg['messages'])) {
                                        $columns = array_keys($firstMsg['messages']);
                                        // Filter to simple scalar values for table display
                                        $columns = array_filter($columns, fn($col) => is_scalar($firstMsg['messages'][$col] ?? null));
                                    }
                                @endphp

                                <li x-data="{ tableExpanded: false }" class="bg-gray-50/50 dark:bg-white/5">
                                    {{-- Type Header Row --}}
                                    <div @click="tableExpanded = !tableExpanded" class="flex items-center justify-between px-6 py-4 cursor-pointer hover:bg-gray-100/50 dark:hover:bg-white/10 transition-colors">
                                        
                                        <div class="flex items-center gap-x-4 min-w-0 flex-1">
                                            {{-- Type Badge --}}
                                            <span class="flex-shrink-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold font-mono text-blue-700 bg-blue-50 border border-blue-200 rounded-md dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800">
                                                {{ $shortType }}
                                            </span>
                                            {{-- Description --}}
                                            <span class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                {{ $type }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-x-6 ml-4">
                                            {{-- Download Button --}}
                                            <a href="{{ route('swift.download.csv', ['date' => $date, 'type' => $type]) }}" 
                                               @click.stop
                                               class="flex items-center gap-x-1.5 text-xs font-semibold text-primary-600 hover:text-primary-500 transition-colors">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M12 12.75l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                Download CSV
                                            </a>

                                            <div class="flex items-center gap-x-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ $count }} items</span>
                                                <svg class="w-4 h-4 transition-transform duration-200" 
                                                     :class="tableExpanded ? 'rotate-90' : ''"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Data Table (Collapsible) --}}
                                    <div x-show="tableExpanded" x-collapse style="display: none;" class="overflow-x-auto border-t border-gray-100 dark:border-gray-800">
                                        <table class="w-full text-left border-collapse whitespace-nowrap">
                                            <thead>
                                                <tr class="bg-gray-100/50 dark:bg-white/5 border-b border-gray-200 dark:border-gray-700">
                                                    @foreach ($columns as $col)
                                                        <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                                            {{ str_replace('_', ' ', $col) }}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                                @foreach ($messages as $msg)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                                        @foreach ($columns as $col)
                                                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                                {{ $msg['messages'][$col] ?? '-' }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-filament-panels::page>
