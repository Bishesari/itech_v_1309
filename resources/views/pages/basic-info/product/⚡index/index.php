<?php

use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('مدیریت محصولات')]
class extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    #[Computed]
    public function products(): LengthAwarePaginator
    {
        return Product::query()
            ->with(['standards', 'prices'])
            ->when(
                trim($this->search) !== '',
                fn ($query) => $query->where(
                    fn ($query) => $query
                        ->where('title', 'like', '%'.trim($this->search).'%')
                        ->orWhere('slug', 'like', '%'.trim($this->search).'%')
                )
            )
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function productTypeLabel(ProductType $type): string
    {
        return $type->label();
    }
};
