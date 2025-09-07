<div>
    <button wire:click='remove' wire:loading.attr='disabled'
        class="cursor-pointer flex justify-center items-center gap-2 bg-red-500 rounded-lg py-2 text-sm px-3 text-white">
        Hapus
        <div wire:loading
            class="animate-spin inline-block size-4 border-3 border-current border-t-transparent text-white rounded-full dark:text-white"
            role="status" aria-label="loading">
            <span class="sr-only">Loading...</span>
        </div>
    </button>
</div>
