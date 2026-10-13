<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

new
#[Layout('layouts::public')]
class extends Component
{
    public Product $product;
    public function render()
    {
        return $this->view()->title("{$this->product->title}");
    }
};
