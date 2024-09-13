@php 
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Marcha\FilamentTree\Components\Tree;
@endphp

@props(['record', 'containerKey', 'tree', 'title' => null, 'icon' => null])

@php
    /** @var $record Model */
    /** @var $containerKey string */
    /** @var $tree Tree */

    $recordKey = $tree->getRecordKey($record);
    $parentKey = $tree->getParentKey($record);

    if ($this->searchString !== '') {
        $children = $record->childrenByKeyword($this->searchString, $recordKey);
    } else {
        $children = $record->children;    
    }
    $isEditable = $this->getIsEditable();
    $collapsed = $this->getNodeCollapsedState($record);

    $actions = $tree->getActions();
    $itemCss = $this->getTreeRecordCssClass($record);
@endphp

<li data-id="{{ $recordKey }}"
    @class([
        'dd-nodrag' => !$isEditable, 'filament-tree-row dd-item'
    ])>
    <div wire:loading.remove.delay
        wire:target="{{ implode(',', Tree::LOADING_TARGETS) }}"
        style="--c-400:var(--primary-400)" 
        class="rounded-lg border h-10 mb-2 flex w-full items-center dark:hover:bg-custom-400/10 hover:bg-custom-400/10 {{$itemCss}} {{$isEditable?'dd-handle':''}}">

        <button type="button" @class([
            'h-full flex items-center',
            'rounded-l-lg border-r rtl:rounded-l rtl:border-r-0 rtl:border-l px-2',
            'bg-gray-50 border-gray-300 dark:bg-white/5 dark:border-white/10',
        ])>
            @if ($icon)
                <div class="w-4 text-gray-400 dark:text-gray-500">
                    <x-dynamic-component :component="$icon" class="w-5 h-5"/>
                </div>
            @endif
        </button>

        <div class="dd-content dd-nodrag flex gap-1">
            <span class="ml-4 font-semibold">
                {!! $title !!}
            </span>

            <div @class(['dd-item-btns', 'hidden' => !count($children), 'flex items-center justify-center pl-3'])>
                <button data-action="expand" @class(['hidden' => !$collapsed])>
                    <x-heroicon-o-chevron-down class="text-gray-400 w-4 h-4" />
                </button>
                <button data-action="collapse" @class(['hidden' => $collapsed])>
                    <x-heroicon-o-chevron-up class="text-gray-400 w-4 h-4" />
                </button>
            </div>
        </div>

        @if ($isEditable && count($actions))
            <div class="dd-nodrag ml-auto mr-4 rtl:ml-4 rtl:mr-auto">
                <x-filament-tree::actions :actions="$actions" :record="$record" />
            </div>
        @endif
    </div>
    @if (count($children))
        <x-filament-tree::tree.list :records="$children" :containerKey="$containerKey" :tree="$tree" :collapsed="$collapsed" />
    @endif
    <div class="rounded-lg border border-gray-300 mb-2 w-full px-4 py-4 animate-pulse hidden"
         wire:loading.class.remove.delay="hidden"
         wire:target="{{ implode(',', Tree::LOADING_TARGETS) }}">
        <div class="h-4 bg-gray-300 rounded-md"></div>
    </div>
</li>
