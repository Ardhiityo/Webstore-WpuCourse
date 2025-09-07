<div>
    <button class="bg-red-400 text-white rounded-lg px-4 py-2 hover:bg-red-600" wire:click='remove'
        wire:loading.attr='disabled'>
        Delete
        <div wire:loading
            class="animate-spin inline-block size-4 border-3 border-current border-t-transparent text-white rounded-full dark:text-white"
            role="status" aria-label="loading">
            <span class="sr-only">Loading...</span>
        </div>
    </button>
</div>
