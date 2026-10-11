<?php

use App\Models\RoleAssignment;
use App\Services\Authorization\CurrentRoleContextService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::auth')]
#[Title('انتخاب نقش')]
class extends Component
{
    public Collection $assignments;

    public ?int $selectedAssignmentId = null;

    public ?RoleAssignment $selectedAssignment = null;

    public function mount(CurrentRoleContextService $contextService): void
    {
        $person = auth()->user()->person;

        $this->assignments = $contextService->available($person);

        // بدون نقش: ادامه به داشبورد تازه‌وارد
        if ($this->assignments->isEmpty()) {
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        // یک نقش: انتخاب خودکار و ورود به داشبورد
        if ($this->assignments->count() === 1) {
            $assignment = $this->assignments->first();

            $contextService->select($person, $assignment->id);

            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    public function select(
        int $assignmentId,
        CurrentRoleContextService $contextService,
    ): void {
        // دریافت دوباره نقش‌های معتبر
        $this->assignments = $contextService->available(
            auth()->user()->person,
        );

        $assignment = $this->assignments->firstWhere(
            'id',
            $assignmentId,
        );

        if (! $assignment) {
            $this->selectedAssignmentId = null;
            $this->selectedAssignment = null;

            $this->addError(
                'selectedAssignmentId',
                'این نقش دیگر قابل انتخاب نیست. صفحه را بررسی کنید.',
            );

            return;
        }

        $this->resetValidation('selectedAssignmentId');

        $this->selectedAssignmentId = (int) $assignment->id;
        $this->selectedAssignment = $assignment;
    }

    public function confirm(CurrentRoleContextService $contextService): void
    {
        $person = auth()->user()->person;

        // اعتبارسنجی بر اساس نقش‌های معتبر در همین لحظه
        $this->assignments = $contextService->available($person);

        $allowedIds = $this->assignments
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $validated = $this->validate([
            'selectedAssignmentId' => [
                'required',
                'integer',
                Rule::in($allowedIds),
            ],
        ]);

        $contextService->select(
            $person,
            (int) $validated['selectedAssignmentId'],
        );

        $this->redirectRoute('dashboard', navigate: true);
    }
};
