<div wire:poll.60s>
    <a href="{{ route('reminders.index') }}" class="relative flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600">
        <x-ui.icon name="bell" class="h-5 w-5" />
        @if($this->unreadCount > 0)
            <span class="absolute right-0 top-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </a>
</div>
