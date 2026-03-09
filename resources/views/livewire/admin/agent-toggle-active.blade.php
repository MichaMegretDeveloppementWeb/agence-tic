<div>
    @if($agent->is_active)
        <x-ui.button
            variant="danger"
            size="compact"
            wire:click="toggle"
            wire:confirm="Êtes-vous sûr de vouloir désactiver cet agent ? Il ne pourra plus se connecter."
        >
            Désactiver
        </x-ui.button>
    @else
        <x-ui.button
            size="compact"
            wire:click="toggle"
            wire:confirm="Êtes-vous sûr de vouloir réactiver cet agent ?"
        >
            Réactiver
        </x-ui.button>
    @endif
</div>
