<?php

use App\Models\Field;
use Flux\Flux;
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

    private const SORTABLE_COLUMNS = [
        'id',
        'title',
        'slug',
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
    public function fields(): LengthAwarePaginator
    {
        $sortBy = in_array($this->sortBy, self::SORTABLE_COLUMNS, true)
            ? $this->sortBy
            : 'title';

        $sortDirection = in_array($this->sortDirection, ['asc', 'desc'], true)
            ? $this->sortDirection
            : 'asc';

        return Field::query()
            ->orderBy($sortBy, $sortDirection)
            ->withCount('standards')
            ->paginate($this->perPage);
    }

    #[On('field-created')]
    public function afterCreate(int $id): void
    {
        $this->focusRecord($id);

        Flux::toast(
            heading: 'ثبت شد.',
            text: 'رشته جدید با موفقیت ثبت شد.',
            variant: 'success',
            position: 'top right',
        );
    }

    #[On('field-updated')]
    public function afterUpdate(int $id): void
    {
        $this->focusRecord($id);
        Flux::toast(
            heading: 'تغییرات اعمال شد.',
            text: 'اطلاعات رشته با موفقیت ویرایش شد.',
            variant: 'warning',
            position: 'top right',
        );
    }

    private function focusRecord(int $id): void
    {
        $this->sortBy = 'title';
        $this->sortDirection = 'asc';
        $this->resetPage();

        $field = Field::query()->find($id);

        if (! $field) {
            return;
        }

        $position = Field::query()
            ->where('title', '<', $field->title)
            ->count();

        $page = intdiv($position, $this->perPage) + 1;

        $this->gotoPage($page);
        $this->highlightedId = $field->id;
    }

    #[On('field-deleted')]
    public function afterDelete(): void
    {
        unset($this->fields);

        $fields = $this->fields;

        if ($fields->isEmpty() && $fields->currentPage() > 1) {
            $this->previousPage();
        }
    }
};
