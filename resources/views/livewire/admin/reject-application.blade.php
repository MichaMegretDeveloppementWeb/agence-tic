<div>
    @error('application-reject-failed')
        <x-ui.alert type="error" dismissible class="mb-4">{{ $message }}</x-ui.alert>
    @enderror

    <p class="text-[13px] text-gray-600">
        Êtes-vous sûr de vouloir refuser la candidature de
        <span class="font-medium text-gray-900">{{ $application->name }}</span> ?
    </p>
    <p class="mt-1 text-[12px] text-gray-400">Cette action changera le statut de la candidature en « Refusée ».</p>

    <div class="mt-6 flex items-center justify-end gap-x-3">
        <x-ui.button variant="ghost" type="button" @click="$dispatch('close-modal', 'reject-application')">Annuler</x-ui.button>
        <x-ui.button variant="danger" wire:click="reject" :loading="true" target="reject">Refuser la candidature</x-ui.button>
    </div>
</div>
