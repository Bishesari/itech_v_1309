<?php

use App\Models\Chapter;
use App\Models\Standard;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('مدیریت فصل‌ها')]
class extends Component
{
    use WithPagination;

    #[Locked]
    public Standard $standard;

    public string $sortBy = 'number';

    public string $sortDirection = 'asc';

    public ?int $highlightedId = null;

    public int $perPage = 10;

    private const SORTABLE_COLUMNS = [
        'id',
        'number',
        'title',
        'created_at',
        'updated_at',
    ];

    public function mount(Standard $standard): void
    {
        $this->standard = $standard;
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
    }

    #[Computed]
    public function chapters(): LengthAwarePaginator
    {
        $sortBy = in_array($this->sortBy, self::SORTABLE_COLUMNS, true)
            ? $this->sortBy
            : 'number';

        $sortDirection = in_array($this->sortDirection, ['asc', 'desc'], true)
            ? $this->sortDirection
            : 'asc';

        return Chapter::query()
            ->where('standard_id', $this->standard->id)
            ->orderBy($sortBy, $sortDirection)
            ->orderBy('id')
            ->paginate($this->perPage);
    }

    #[On('chapter-created')]
    #[On('chapter-updated')]
    public function afterChange(int $id): void
    {
        unset($this->chapters);

        $this->focusRecord($id);

        Flux::toast(
            text: 'اطلاعات فصل با موفقیت ثبت یا ویرایش شد.',
            heading: 'عملیات موفق',
            variant: 'success',
            position: 'top right',
        );
    }

    private function focusRecord(int $id): void
    {
        $chapter = Chapter::query()
            ->where('standard_id', $this->standard->id)
            ->find($id);

        if (! $chapter) {
            return;
        }

        $this->sortBy = 'number';
        $this->sortDirection = 'asc';
        $this->resetPage();

        $position = Chapter::query()
            ->where('standard_id', $this->standard->id)
            ->where('number', '<', $chapter->number)
            ->count();

        $position += Chapter::query()
            ->where('standard_id', $this->standard->id)
            ->where('number', $chapter->number)
            ->where('id', '<', $chapter->id)
            ->count();

        $this->gotoPage(intdiv($position, $this->perPage) + 1);

        $this->highlightedId = $chapter->id;
    }

    #[On('chapter-deleted')]
    public function afterDelete(): void
    {
        unset($this->chapters);

        if ($this->chapters->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }
};
