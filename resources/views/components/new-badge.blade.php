@props(['date', 'read' => false])

@if($date && !$read && $date->diffInHours(now()) < 48)
    <span class="inline-flex items-center rounded-full bg-emerald-50 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-emerald-700">
        Nouveau
    </span>
@endif
