@php
    $containerKey = 'filament_tree_container_' . $this->getId();
    $maxDepth = $getMaxDepth() ?? 1;
    $records = collect($this->getRootLayerRecords() ?? []);
@endphp

<div wire:disabled="updateTree"
    x-ignore
    ax-load
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-tree-component', 'marcha/filament-tree') }}"
    x-data="treeNestableComponent({
        containerKey: {{ $containerKey }},
        maxDepth: {{ $maxDepth }}
    })">
    <x-filament::section :heading="($this->displayTreeTitle() ?? false) ? $this->getTreeTitle() : null">
        <menu class="flex gap-2 mb-4" id="nestable-menu">
            <div class="btn-group">
                <x-filament::button color="gray" tag="button" data-action="expand-all" x-on:click="expandAll()" wire:loading.attr="disabled" wire:loading.class="cursor-wait opacity-70">
                    {{ __('filament-tree::filament-tree.button.expand_all') }}
                </x-filament::button>
                <x-filament::button color="gray" tag="button" data-action="collapse-all" x-on:click="collapseAll()" wire:loading.attr="disabled" wire:loading.class="cursor-wait opacity-70">
                    {{ __('filament-tree::filament-tree.button.collapse_all') }}
                </x-filament::button>
            </div>
            <div class="btn-group">
                @if ($this->getIsEditable())
                    <x-filament::button tag="button" data-action="save" x-on:click="save()" wire:loading.attr="disabled" wire:loading.class="cursor-wait opacity-70">
                        <x-filament::loading-indicator class="h-4 w-4" wire:loading wire:target="updateTree"/>
                        <span wire:loading.remove wire:target="updateTree">
                            {{ __('filament-tree::filament-tree.button.save') }}
                        </span>

                    </x-filament::button>
                @endif
            </div>

            <div class="btn-group ml-auto">
                <form wire:submit.prevent="searchTree">   
                    <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                        <x-filament::input
                            type="text"
                            wire:model.lazy="searchString"
                            placeholder="{{ __('filament-tree::filament-tree.input.search') }}"
                        />
                    </x-filament::input.wrapper>                
                </form>                                                                             
            </div>
        </menu>
        
        @if ($this->searchString !== '')
        <div class="fi-ta-filter-indicators mb-2 flex items-start justify-between gap-x-3 bg-gray-50 px-3 py-1.5 dark:bg-white/5 sm:px-6">
            <div class="flex flex-col gap-x-3 gap-y-1 sm:flex-row">
                <span class="whitespace-nowrap text-sm font-medium leading-6 text-gray-700 dark:text-gray-200">
                    Aktivna pretraga
                </span>
                <div class="flex flex-wrap gap-1.5">
                    <span style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);" class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary">
                        <span class="grid">
                            <span class="truncate">
                                Traži: {{ $this->searchString }}
                            </span>
                        </span>                        

                        <button type="button" style="--c-300:var(--primary-300);--c-700:var(--primary-700);" 
                            class="fi-badge-delete-button -my-1 -me-2 -ms-1 flex items-center justify-center p-1 outline-none transition duration-75 text-custom-700/50 hover:text-custom-700/75 focus-visible:text-custom-700/75 dark:text-custom-300/50 dark:hover:text-custom-300/75 dark:focus-visible:text-custom-300/75" 
                            wire:click="resetTreeSearch" wire:loading.attr="disabled">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"></path>
                            </svg>                            
                        </button>
                    </span>           
                </div>
            </div>
        </div>
        @endif

        <div class="filament-tree dd" id="{{ $containerKey }}">
            <x-filament-tree::tree.list :records="$records" :containerKey="$containerKey" :tree="$tree"/>
        </div>
    </x-filament::section>
</div>

<form wire:submit.prevent="callMountedTreeAction">
    @php
        $action = $this->getMountedTreeAction();
    @endphp

    <x-filament::modal
        :alignment="$action?->getModalAlignment()"
        :close-button="$action?->hasModalCloseButton()"
        :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
        :description="$action?->getModalDescription()"
        display-classes="block"
        :footer-actions="$action?->getVisibleModalFooterActions()"
        :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
        :heading="$action?->getModalHeading()"
        :icon="$action?->getModalIcon()"
        :icon-color="$action?->getModalIconColor()"
        :id="$this->getId() . '-tree-action'"
        :slide-over="$action?->isModalSlideOver()"
        :sticky-footer="$action?->isModalFooterSticky()"
        :sticky-header="$action?->isModalHeaderSticky()"
        :visible="filled($action)"
        :width="$action?->getModalWidth()"
        :wire:key="$action ? $this->getId() . '.tree.actions.' . $action->getName() . '.modal' : null"
        x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->getId() }}') && $wire.mountedTreeActions.length) open()"
        x-on:modal-closed.once.stop="
            const mountedTreeActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTreeActionShouldOpenModal()) }}

            if (! mountedTreeActionShouldOpenModal) {
                return
            }

            if ($wire.mountedFormComponentActions.length) {
                return
            }

            $wire.unmountTreeAction(false)
        "
        x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->getId() }}') close()"
    >
        @if ($action)
            {{ $action->getModalContent() }}

            @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                {{ $infolist }}
            @elseif ($this->mountedTreeActionHasForm())
                {{ $this->getMountedTreeActionForm() }}
            @endif

            {{ $action->getModalContentFooter() }}
        @endif
    </x-filament::modal>
</form>