@props([
    'options' => [5, 10, 15, 25, 50, 100],
])

<div class="flex items-center gap-x-2">
    <label class="text-[12px] text-gray-400 whitespace-nowrap">Par page</label>
    <select wire:model.live="perPage" class="rounded-lg border-0 py-1.5 pl-2.5 pr-7 text-[12px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
        @foreach($options as $option)
            <option value="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>
</div>
