<?php

namespace Marcha\FilamentTree\Concern;

use Filament\Support\Contracts\TranslatableContentDriver;
use Marcha\FilamentTree\Contract\HasTree;

trait BelongsToLivewire
{
    protected HasTree $livewire;

    public function livewire(HasTree $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): HasTree
    {
        return $this->livewire;
    }
    
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return $this->getLivewire()->makeFilamentTranslatableContentDriver();
    }
}
