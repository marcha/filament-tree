<?php

namespace Marcha\FilamentTree\Concern;

use Marcha\FilamentTree\Components\Tree;
use Marcha\FilamentTree\Contract\HasTree;

trait BelongsToTree
{
    protected Tree $tree;

    public function tree(Tree $tree): static
    {
        $this->tree = $tree;

        return $this;
    }

    public function getTree(): Tree
    {
        return $this->tree;
    }

    public function getLivewire(): HasTree
    {
        return $this->getTree()->getLivewire();
    }
}
