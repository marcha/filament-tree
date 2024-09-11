@php use Illuminate\Database\Eloquent\Model; @endphp
@php use Filament\Facades\Filament; @endphp
@php use Marcha\FilamentTree\Components\Tree; @endphp
@php use App\Filament\Pages\Enums\TreeNodeType; @endphp
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
    $collapsed = $this->getNodeCollapsedState($record);

    $actions = $tree->getActions();
@endphp

<li data-id="{{ $recordKey }}"
    @class([
        'dd-nodrag' => !$this->isEditable(), 'filament-tree-row dd-item'
    ])>
    <div wire:loading.remove.delay
        wire:target="{{ implode(',', Tree::LOADING_TARGETS) }}"
        @class([
            'rounded-lg border h-10',
            'mb-2',
            'flex w-full items-center',
            'border-gray-300 bg-gray-100 dark:border-white/10 dark:bg-gray-800' => ($record->node_type->value !== 0),
            'border-gray-300 bg-white dark:border-white/10 dark:bg-gray-900' => ($record->node_type->value === 0),
            'dd-handle' => $this->isEditable()
        ])>

        <button type="button" @class([
            'h-full flex items-center',
            'rounded-l-lg border-r rtl:rounded-l rtl:border-r-0 rtl:border-l px-2',
            'bg-gray-50 border-gray-300 dark:bg-white/5 dark:border-white/10',
        ])>
            @switch ($record->node_type)
                @case (TreeNodeType::Folder)
                    <x-heroicon-o-folder class="text-gray-400 dark:text-gray-500 w-4 h-4"/>
                    @break

                @case (TreeNodeType::PDF)
                    <img src="../images/app/pdfdoc.png">
                    @break

                @case (TreeNodeType::Link)
                    <x-heroicon-o-link class="text-gray-400 dark:text-gray-500 w-4 h-4"/>
                    @break
                
                @default
            @endswitch
        </button>

        <div class="dd-content dd-nodrag flex gap-1">
            @if ($icon)
                <div class="w-4">
                    <x-dynamic-component :component="$icon" class="w-4 h-4"/>
                </div>
            @endif

            <span @class([
                'ml-4 rtl:mr-4' => !$icon,
                'font-semibold'
            ])>
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

        @if ($this->isEditable() && count($actions))
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
