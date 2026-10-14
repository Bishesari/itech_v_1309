<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

new
#[Layout('layouts::public')]
class extends Component
{
    public Product $product;
    public function mount(): void
    {
        abort_unless(
            $this->product->is_active,
            404
        );
    }
    public function render()
    {
        return $this->view()
            ->title(
                $this->product->seo_title
                    ?: $this->product->title
            );
    }
};
