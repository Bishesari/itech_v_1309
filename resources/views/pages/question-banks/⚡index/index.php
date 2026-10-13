<?php

use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::public')]
#[Title('بانک سؤال‌ها')]
class extends Component
{
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        return Product::query()
            ->where('type', ProductType::WrittenSampleQuestion->value)
            ->where('is_active', true)
            ->whereHas('standards', fn ($query) => $query->where('is_active', true))
            ->with([
                'standards' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('title')
            ->paginate(12);
    }
};
