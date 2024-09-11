<?php

namespace Marcha\FilamentTree\Concern\Actions;

use Marcha\FilamentTree\Components\Tree;

interface HasTree
{
    public function tree(Tree $tree): static;
}
