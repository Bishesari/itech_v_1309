<?php

use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('مدیریت آزمون‌ها')]
class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public ?int $highlightedId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        $allowedColumns = [
            'title',
            'type',
            'question_count',
            'is_active',
            'created_at',
        ];

        if (! in_array($column, $allowedColumns, true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc'
                ? 'desc'
                : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function exams()
    {
        return Exam::query()
            ->with('standard')
            ->when(
                trim($this->search) !== '',
                fn (Builder $query) => $query->where('title', 'like', '%'.trim($this->search).'%')
                    ->orWhereHas(
                        'standard',
                        fn (Builder $standardQuery) => $standardQuery->where(
                            'name_fa',
                            'like',
                            '%'.trim($this->search).'%',
                        ),
                    ),
            )
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function focusRecord(int $id): void
    {
        $this->highlightedId = $id;
    }

    #[On('exam-created')]
    public function refreshAfterCreate(): void
    {
        unset($this->exams);
        $this->resetPage();
    }

    #[On('exam-updated')]
    public function refreshAfterUpdate(): void
    {
        unset($this->exams);
    }
};
