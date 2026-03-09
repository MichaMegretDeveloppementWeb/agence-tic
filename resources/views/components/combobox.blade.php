@props([
    'options' => [],
    'wireModel' => '',
    'placeholder' => 'Sélectionner...',
    'hasError' => false,
    'nullable' => false,
])

<div
    x-data="{
        open: false,
        search: '',
        debouncedSearch: '',
        debounceTimer: null,
        value: $wire.entangle('{{ $wireModel }}'),
        options: {{ Js::from($options) }},
        highlightIndex: -1,
        get filteredOptions() {
            if (!this.debouncedSearch.trim()) return this.options;
            const s = this.debouncedSearch.toLowerCase().trim();
            return this.options.filter(o => o.label.toLowerCase().includes(s));
        },
        get selectedLabel() {
            const val = String(this.value ?? '');
            if (!val) return '';
            const opt = this.options.find(o => String(o.id) === val);
            return opt ? opt.label : '';
        },
        select(option) {
            this.value = String(option.id);
            this.search = '';
            this.debouncedSearch = '';
            this.open = false;
            this.highlightIndex = -1;
        },
        clear() {
            this.value = '';
            this.search = '';
            this.debouncedSearch = '';
        },
        openDropdown() {
            this.open = true;
            this.highlightIndex = -1;
            this.$nextTick(() => this.$refs.searchInput.focus());
        },
        closeDropdown() {
            this.open = false;
            this.search = '';
            this.debouncedSearch = '';
            this.highlightIndex = -1;
            clearTimeout(this.debounceTimer);
        },
        onKeydown(e) {
            if (!this.open) return;
            const len = this.filteredOptions.length;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.highlightIndex = (this.highlightIndex + 1) % len;
                this.scrollToHighlighted();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.highlightIndex = this.highlightIndex <= 0 ? len - 1 : this.highlightIndex - 1;
                this.scrollToHighlighted();
            } else if (e.key === 'Enter' && this.highlightIndex >= 0 && this.highlightIndex < len) {
                e.preventDefault();
                this.select(this.filteredOptions[this.highlightIndex]);
            }
        },
        scrollToHighlighted() {
            this.$nextTick(() => {
                const el = this.$refs.listbox?.children[this.highlightIndex];
                if (el) el.scrollIntoView({ block: 'nearest' });
            });
        }
    }"
    @click.outside="closeDropdown()"
    @keydown.escape.prevent="closeDropdown()"
    @keydown="onKeydown($event)"
    class="relative"
>
    {{-- Closed state: shows selected value --}}
    <button
        x-show="!open"
        @click.prevent="openDropdown()"
        type="button"
        class="flex w-full items-center rounded-lg border-0 py-2 pl-3 pr-8 text-left text-[13px] ring-1 ring-inset focus:ring-2 focus:ring-inset {{ $hasError ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }}"
    >
        <span
            x-text="selectedLabel || '{{ $placeholder }}'"
            :class="selectedLabel ? 'text-gray-900' : 'text-gray-400'"
            class="truncate"
        ></span>
    </button>

    {{-- Open state: search input --}}
    <input
        x-show="open"
        x-ref="searchInput"
        x-model="search"
        @input="clearTimeout(debounceTimer); debounceTimer = setTimeout(() => { debouncedSearch = search; highlightIndex = -1; }, 500)"
        type="text"
        placeholder="Rechercher..."
        class="block w-full rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900"
    />

    {{-- Clear button (nullable only) --}}
    @if($nullable)
        <button
            x-show="selectedLabel && !open"
            @click.stop="clear()"
            type="button"
            class="absolute right-7 top-1/2 -translate-y-1/2 p-0.5"
        >
            <x-ui.icon name="x-mark" class="h-3.5 w-3.5 text-gray-400 hover:text-gray-600" />
        </button>
    @endif

    {{-- Chevron --}}
    <x-ui.icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        x-cloak
        class="absolute z-50 mt-1 w-full rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200 max-h-60 overflow-y-auto scrollbar-thin"
    >
        <div x-ref="listbox">
            <template x-for="(option, index) in filteredOptions" :key="option.id">
                <button
                    type="button"
                    @click="select(option)"
                    @mouseenter="highlightIndex = index"
                    class="flex w-full items-center px-3 py-1.5 text-left text-[13px]"
                    :class="{
                        'bg-gray-100': highlightIndex === index,
                        'bg-gray-50 font-medium text-gray-900': String(option.id) === String(value ?? '') && highlightIndex !== index,
                        'text-gray-700': String(option.id) !== String(value ?? '') && highlightIndex !== index,
                    }"
                >
                    <span x-text="option.label" class="truncate"></span>
                    <template x-if="String(option.id) === String(value ?? '')">
                        <svg class="ml-auto h-4 w-4 shrink-0 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </template>
                </button>
            </template>
        </div>
        <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-[13px] text-gray-400">
            Aucun résultat
        </div>
    </div>
</div>
