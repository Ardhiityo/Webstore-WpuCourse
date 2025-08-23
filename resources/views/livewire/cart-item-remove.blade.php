<div>
    <button type="button" wire:click="removeFromCart()" wire:loading.attr="disabled"
        class="inline-flex items-center justify-center w-full px-3 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg cursor-pointer gap-x-2 hover:bg-red-700 focus:outline-hidden focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
        Remove
        </svg>
        <div wire:loading
            class="animate-spin inline-block size-4 border-3 border-current border-t-transparent text-blue-400 rounded-full dark:text-blue-400"
            role="status" aria-label="loading">
            <span class="sr-only">Loading...</span>
        </div>
    </button>
</div>
