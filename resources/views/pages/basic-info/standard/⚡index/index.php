<?php

use App\Enums\QuestionType;
use App\Models\Standard;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('استانداردهای آموزشی')]
class extends Component
{
    use WithPagination;

    public string $sortBy = 'name_fa';

    public string $sortDirection = 'asc';

    public ?int $highlightedId = null;

    public int $perPage = 10;

    private const SORTABLE_COLUMNS = [
        'id',
        'name_fa',
        'name_en',
        'code',
        'sum_h',
        'required_h',
        'created_at',
        'updated_at',
    ];

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
    }

    #[Computed]
    public function standards(): LengthAwarePaginator
    {
        $sortBy = in_array($this->sortBy, self::SORTABLE_COLUMNS, true)
            ? $this->sortBy
            : 'name_fa';

        $sortDirection = in_array($this->sortDirection, ['asc', 'desc'], true)
            ? $this->sortDirection
            : 'asc';

        return Standard::query()
            ->with('field')
            ->withCount([
                'chapters',
                'products',
                'questions as multiple_choice_questions_count' => fn ($query) => $query->where(
                    'questions.type',
                    QuestionType::MultipleChoice->value
                ),
                'questions as practical_questions_count' => fn ($query) => $query->where(
                    'questions.type',
                    QuestionType::Practical->value
                ),
            ])
            ->orderBy($sortBy, $sortDirection)
            ->orderBy('id')
            ->paginate($this->perPage);
    }

    #[On('standard-created')]
    public function afterCreate(int $id): void
    {
        $this->focusRecord($id);

        Flux::toast(
            text: 'استاندارد جدید با موفقیت ثبت شد.',
            heading: 'ثبت شد.',
            variant: 'success',
            position: 'top right',
        );
    }

    #[On('standard-updated')]
    public function afterUpdate(int $id): void
    {
        $this->focusRecord($id);

        Flux::toast(
            text: 'اطلاعات استاندارد با موفقیت ویرایش شد.',
            heading: 'تغییرات اعمال شد.',
            variant: 'warning',
            position: 'top right',
        );
    }

    private function focusRecord(int $id): void
    {
        $this->sortBy = 'name_fa';
        $this->sortDirection = 'asc';
        $this->resetPage();

        $standard = Standard::query()->find($id);

        if (! $standard) {
            return;
        }

        $position = Standard::query()
            ->where('name_fa', '<', $standard->name_fa)
            ->count();

        $position += Standard::query()
            ->where('name_fa', $standard->name_fa)
            ->where('id', '<', $standard->id)
            ->count();

        $page = intdiv($position, $this->perPage) + 1;

        $this->gotoPage($page);

        $this->highlightedId = $standard->id;
    }

    public function toggleStatus(int $standardId): void
    {
        $standard = Standard::query()->findOrFail($standardId);

        $standard->update([
            'is_active' => ! $standard->is_active,
        ]);

        unset($this->standards);

        Flux::toast(
            text: 'وضعیت استاندارد با موفقیت تغییر کرد.',
            heading: 'به‌روزرسانی شد.',
            variant: 'warning',
            position: 'top right',
        );
    }

    #[On('standard-deleted')]
    public function afterDelete(): void
    {
        unset($this->standards);

        $standards = $this->standards;

        if (
            $standards->isEmpty()
            && $standards->currentPage() > 1
        ) {
            $this->previousPage();
        }
    }
};
