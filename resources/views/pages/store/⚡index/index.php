<?php

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new
#[Layout('layouts::public')]
class extends Component
{
    #[Computed]
    public function products()
    {
        return Product::query()
            ->where('is_active', true)
            ->where('is_publicly_listed', true)
            ->with('prices')
            ->orderBy('title')
            ->paginate(12);
    }

    public function render()
    {
        return $this->view()->title('فروشگاه');
    }
};
