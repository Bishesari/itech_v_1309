<?php

use App\Models\Field;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('رشته های آموزشی')]
class extends Component
{
    use WithPagination;

    public string $sortBy = 'title';

    public string $sortDirection = 'asc';

    public ?int $highlightedId = null;

    public int $perPage = 10;

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }
        $this->sortBy = $column;
        $this->sortDirection = 'asc';
    }

    #[Computed]
    public function fields(): LengthAwarePaginator
    {
        return Field::query()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->withCount('standards')
            ->paginate($this->perPage);
    }

    #[On('field-created')]
    #[On('field-updated')]
    public function focusRecord(?int $id = null): void
    {
        $this->reset(['sortBy', 'sortDirection']);

        if (! $id) {
            return;
        }

        $field = Field::query()->find($id);
        if (! $field) {
            return;
        }
        $beforeCount = Field::where('title', '<', $field->title)->count();
        $page = intdiv($beforeCount, $this->perPage) + 1;
        $this->gotoPage($page);
        $this->highlightedId = $id;
    }
    #[On('field-deleted')]
    public function afterDelete(): void
    {
        $fields = $this->fields();

        if ($fields->isEmpty() && $fields->currentPage() > 1) {
            $this->previousPage();
        }
    }
};
