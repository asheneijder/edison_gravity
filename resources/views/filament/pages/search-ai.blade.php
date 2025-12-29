<x-filament-panels::page>
    <div class="max-w-5xl mx-auto space-y-8">

        {{-- Search Input (Prompt Style) --}}
        <form wire:submit="search" class="relative z-10">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-primary-500/30 to-purple-500/30 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                <div class="relative bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-2 border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center px-4">
                        <x-heroicon-m-sparkles class="h-6 w-6 text-primary-500 animate-pulse" wire:loading wire:target="query, search" />
                        <x-heroicon-m-magnifying-glass class="h-6 w-6 text-gray-400" wire:loading.remove wire:target="query, search" />
                        
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="query"
                            class="w-full border-0 bg-transparent py-4 px-4 text-xl text-gray-950 dark:text-white placeholder:text-gray-400 focus:ring-0 sm:leading-6"
                            placeholder="Ask Edison to find messages, files, or users..." 
                            autofocus
                            autocomplete="off"
                        >
                        
                        <div class="flex items-center gap-2">
                             <button type="submit" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition">
                                <x-heroicon-m-arrow-right class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-2 text-right">
                <span class="text-xs text-gray-400 dark:text-gray-500">Press <kbd class="font-sans font-medium text-gray-500 dark:text-gray-400">Enter</kbd> to search</span>
            </div>
        </form>

        {{-- Combined Results Feed --}}
        @if(!empty($results))
        <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            
            {{-- 1. AI Response Card --}}
            @if($artbotResponse)
            <div class="bg-gradient-to-br from-primary-50 to-white dark:from-primary-950/30 dark:to-gray-900 rounded-xl border border-primary-100 dark:border-primary-900/50 p-6 shadow-sm">
                <div class="flex gap-4">
                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                        <x-heroicon-s-cpu-chip class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-primary-900 dark:text-primary-100 uppercase tracking-wide mb-1">Edison AI</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $artbotResponse }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- 2. Action Buttons (Users & Functions) --}}
            @if(!empty($results['functions']) || !empty($results['users']))
            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4 px-1">Quick Actions</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Functions --}}
                    @foreach($results['functions'] as $func)
                        <a href="{{ $func['url'] }}" class="flex items-center gap-3 p-3 rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 transition group">
                            <span class="p-2 rounded bg-gray-50 dark:bg-gray-800 text-gray-500 group-hover:text-primary-500">
                                <x-icon name="{{ $func['icon'] ?? 'heroicon-o-link' }}" class="h-5 w-5" />
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $func['label'] }}</span>
                        </a>
                    @endforeach

                    {{-- Users --}}
                    @foreach($results['users'] as $user)
                        <a href="{{ $user['url'] ?? '#' }}" class="flex items-center gap-3 p-3 rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 transition group">
                            <span class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">
                                {{ substr($user['name'], 0, 1) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user['name'] }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user['email'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 3. Data Feed (Messages & Files) --}}
            @if(!empty($results['messages']) || !empty($results['inbound_files']) || !empty($results['outbound_files']))
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Left: Messages --}}
                @if(!empty($results['messages']))
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 px-1">Swift Messages</h4>
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 divide-y divide-gray-100 dark:divide-gray-800 shadow-sm">
                        @foreach($results['messages'] as $msg)
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                            {{ $msg['type'] }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($msg['date'])->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="text-xs font-mono text-gray-300 dark:text-gray-600">DB</div>
                                </div>
                                <div class="mt-2 flex items-center gap-3 text-sm">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $msg['sender'] }}</span>
                                    <x-heroicon-m-arrow-long-right class="h-4 w-4 text-gray-400" />
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $msg['receiver'] }}</span>
                                </div>
                                <div class="mt-2 text-xs text-gray-400 font-mono truncate bg-gray-50 dark:bg-gray-950 p-1.5 rounded border border-gray-100 dark:border-gray-800">
                                    {{ $msg['file'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Right: Files --}}
                @if(!empty($results['inbound_files']) || !empty($results['outbound_files']))
                <div class="space-y-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 px-1">Filesystem</h4>
                    
                    {{-- Outbound --}}
                    @if(!empty($results['outbound_files']))
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 shadow-sm">
                        <h5 class="text-xs font-bold text-gray-400 uppercase mb-3">Downloads</h5>
                        <ul class="space-y-2">
                            @foreach($results['outbound_files'] as $file)
                                <li>
                                    <button 
                                        wire:click="downloadFile('{{ $file['path'] }}')"
                                        class="w-full flex items-center gap-2 p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 text-left transition group border border-transparent hover:border-primary-100 dark:hover:border-primary-800"
                                    >
                                        <div class="h-8 w-8 rounded bg-primary-50 dark:bg-gray-800 text-primary-600 flex items-center justify-center">
                                            <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate group-hover:text-primary-700 dark:group-hover:text-primary-400">{{ $file['name'] }}</p>
                                            <p class="text-[10px] text-gray-400">{{ round($file['size'] / 1024) }}kb</p>
                                        </div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Inbound --}}
                    @if(!empty($results['inbound_files']))
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 shadow-sm">
                        <h5 class="text-xs font-bold text-gray-400 uppercase mb-3">Inbound Source</h5>
                        <ul class="space-y-2">
                            @foreach($results['inbound_files'] as $file)
                                <li class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <x-heroicon-o-document-text class="h-5 w-5 text-gray-400" />
                                    <span class="text-xs text-gray-600 dark:text-gray-300 truncate flex-1">{{ $file['name'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                </div>
                @endif
            </div>
            @endif

        </div>
        @endif

        {{-- Empty State --}}
        @if(empty($results) && !empty($query))
             <div class="text-center py-20 animate-pulse">
                <p class="text-gray-400 dark:text-gray-600">Searching...</p>
             </div>
        @elseif(empty($query))
            <div class="py-20 text-center space-y-4">
                <div class="inline-flex p-4 rounded-full bg-gray-50 dark:bg-gray-900 mb-4">
                    <x-heroicon-o-sparkles class="h-12 w-12 text-gray-300" />
                </div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Ready when you are.</h2>
                <div class="flex flex-wrap justify-center gap-2">
                     <button wire:click="$set('query', 'Payment')" class="px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700 text-sm text-gray-500 hover:border-primary-500 hover:text-primary-600 transition">Payment</button>
                     <button wire:click="$set('query', 'Users')" class="px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700 text-sm text-gray-500 hover:border-primary-500 hover:text-primary-600 transition">Users</button>
                     <button wire:click="$set('query', 'MT103')" class="px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700 text-sm text-gray-500 hover:border-primary-500 hover:text-primary-600 transition">MT103</button>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>