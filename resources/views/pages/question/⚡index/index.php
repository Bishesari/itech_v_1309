<?php

use App\Models\Chapter;
use App\Models\Field;
use App\Models\Question;
use App\Models\Standard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

new
#[Title('بانک سؤال‌ها')]
class extends Component
{
    use WithPagination;

    #[Url(as: 'field_id')]
    public string $fieldId = '';

    #[Url(as: 'standard_id')]
    public string $standardId = '';

    #[Url(as: 'chapter_id')]
    public string $chapterId = '';

    public string $sortBy = 'id';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    private const SORTABLE_COLUMNS = [
        'id',
        'type',
        'difficulty',
        'is_active',
        'is_frequently_final',
        'created_at',
        'updated_at',
    ];

    public function updatedFieldId(): void
    {
        $this->standardId = '';
        $this->chapterId = '';
        $this->resetPage();
        unset($this->standards, $this->chapters, $this->questions);
    }

    public function updatedStandardId(): void
    {
        $this->chapterId = '';
        $this->resetPage();
        unset($this->chapters, $this->questions);
    }

    public function updatedChapterId(): void
    {
        $this->resetPage();
        unset($this->questions);
    }

    public function sort(string $column): void
    {
        if (! in_array($column, self::SORTABLE_COLUMNS, true)) {
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
        unset($this->questions);
    }

    public function clearFilters(): void
    {
        $this->fieldId = '';
        $this->standardId = '';
        $this->chapterId = '';
        $this->resetPage();

        unset($this->standards, $this->chapters, $this->questions);
    }

    #[Computed]
    public function fields(): Collection
    {
        return Field::query()
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    #[Computed]
    public function standards(): Collection
    {
        return Standard::query()
            ->when(
                $this->fieldId !== '',
                fn ($query) => $query->where('field_id', $this->fieldId)
            )
            ->orderBy('name_fa')
            ->get(['id', 'field_id', 'code', 'name_fa']);
    }

    #[Computed]
    public function chapters(): Collection
    {
        if ($this->standardId === '') {
            return new Collection;
        }

        return Chapter::query()
            ->where('standard_id', $this->standardId)
            ->orderBy('number')
            ->get(['id', 'standard_id', 'number', 'title']);
    }

    #[Computed]
    public function questions(): LengthAwarePaginator
    {
        $sortBy = in_array($this->sortBy, self::SORTABLE_COLUMNS, true)
            ? $this->sortBy
            : 'id';

        $sortDirection = in_array($this->sortDirection, ['asc', 'desc'], true)
            ? $this->sortDirection
            : 'desc';

        return Question::query()
            ->with(['chapter.standard.field'])
            ->when(
                $this->fieldId !== '',
                fn ($query) => $query->whereHas(
                    'chapter.standard',
                    fn ($standardQuery) => $standardQuery->where(
                        'field_id',
                        $this->fieldId
                    )
                )
            )
            ->when(
                $this->standardId !== '',
                fn ($query) => $query->whereHas(
                    'chapter',
                    fn ($chapterQuery) => $chapterQuery->where(
                        'standard_id',
                        $this->standardId
                    )
                )
            )
            ->when(
                $this->chapterId !== '',
                fn ($query) => $query->where(
                    'chapter_id',
                    $this->chapterId
                )
            )
            ->orderBy($sortBy, $sortDirection)
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
    }
};
