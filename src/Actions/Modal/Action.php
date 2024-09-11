<?php

namespace Marcha\FilamentTree\Actions\Modal;

use Filament\Actions\StaticAction;
use Marcha\FilamentTree\Concern\Actions\HasTree;
use Marcha\FilamentTree\Concern\BelongsToTree;

/**
 * @deprecated Use `\Filament\Actions\StaticAction` instead.
 */
class Action extends StaticAction implements HasTree
{
    use BelongsToTree;
}
