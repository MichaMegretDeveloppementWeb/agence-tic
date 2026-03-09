<div
    x-data="{
        open: false,
        selectedIndex: -1,
        openSearch() {
            this.open = true;
            this.$nextTick(() => this.$refs.searchInput.focus());
        },
        closeSearch() {
            this.open = false;
            this.selectedIndex = -1;
            $wire.set('query', '');
            $wire.set('results', []);
        },
        navigateResults(direction) {
            const count = $wire.results.length;
            if (count === 0) return;
            if (direction === 'down') {
                this.selectedIndex = (this.selectedIndex + 1) % count;
            } else {
                this.selectedIndex = this.selectedIndex <= 0 ? count - 1 : this.selectedIndex - 1;
            }
        },
        openSelected() {
            if (this.selectedIndex >= 0 && this.selectedIndex < $wire.results.length) {
                window.location.href = $wire.results[this.selectedIndex].url;
            }
        }
    }"
    x-on:keydown.window.prevent.meta.k="openSearch()"
    x-on:keydown.window.prevent.ctrl.k="openSearch()"
    x-on:open-global-search.window="openSearch()"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[60] bg-gray-900/50"
        @click="closeSearch()"
    ></div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition
        x-cloak
        class="fixed inset-x-0 top-[15%] z-[70] mx-auto w-full max-w-lg px-4 sm:px-0"
        @keydown.escape.window="if (open) closeSearch()"
        @keydown.arrow-down.prevent="navigateResults('down')"
        @keydown.arrow-up.prevent="navigateResults('up')"
        @keydown.enter.prevent="openSelected()"
    >
        <div class="rounded-xl bg-white shadow-2xl ring-1 ring-gray-200">
            {{-- Search input --}}
            <div class="flex items-center gap-x-3 border-b border-gray-100 px-4 py-3">
                <x-ui.icon name="magnifying-glass" class="h-5 w-5 shrink-0 text-gray-400" />
                <input
                    x-ref="searchInput"
                    wire:model.live.debounce.300ms="query"
                    type="text"
                    placeholder="Rechercher des rapports, documents, agents..."
                    class="w-full border-0 bg-transparent text-[13px] text-gray-900 placeholder:text-gray-400 focus:ring-0"
                />
                <kbd class="hidden shrink-0 items-center rounded border border-gray-200 px-1.5 py-0.5 text-[10px] font-medium text-gray-400 sm:inline-flex">ESC</kbd>
            </div>

            {{-- Results --}}
            @if(count($results) > 0)
                <ul class="max-h-80 overflow-y-auto py-2">
                    @foreach($results as $index => $result)
                        <li>
                            <a
                                href="{{ $result['url'] }}"
                                class="flex items-center gap-x-3 px-4 py-2 transition-colors"
                                :class="selectedIndex === {{ $index }} ? 'bg-gray-50' : 'hover:bg-gray-50'"
                                @mouseenter="selectedIndex = {{ $index }}"
                                @click="closeSearch()"
                            >
                                <x-ui.badge color="{{ match($result['type']) { 'Rapport' => 'blue', 'Document' => 'indigo', 'Agent' => 'emerald', default => 'gray' } }}">
                                    {{ $result['type'] }}
                                </x-ui.badge>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13px] font-medium text-gray-900">{{ $result['title'] }}</p>
                                    <p class="truncate text-[12px] text-gray-400">{{ $result['subtitle'] }}</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @elseif(strlen($query) >= 2)
                <div class="px-4 py-8 text-center">
                    <x-ui.icon name="magnifying-glass" class="mx-auto h-8 w-8 text-gray-300" />
                    <p class="mt-2 text-[13px] text-gray-400">Aucun résultat pour « {{ $query }} »</p>
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <x-ui.icon name="magnifying-glass" class="mx-auto h-8 w-8 text-gray-300" />
                    <p class="mt-2 text-[13px] text-gray-400">Tapez au moins 2 caractères pour rechercher</p>
                </div>
            @endif

            {{-- Footer --}}
            <div class="border-t border-gray-100 px-4 py-2">
                <p class="text-[11px] text-gray-400">
                    <kbd class="rounded border border-gray-200 px-1 py-0.5 text-[10px]">&uarr;&darr;</kbd> naviguer
                    <kbd class="ml-2 rounded border border-gray-200 px-1 py-0.5 text-[10px]">&crarr;</kbd> ouvrir
                    <kbd class="ml-2 rounded border border-gray-200 px-1 py-0.5 text-[10px]">esc</kbd> fermer
                </p>
            </div>
        </div>
    </div>
</div>
