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
        $this->assignments = $contextService->available(
            auth()->user()->person,
        );

        // این صفحه فقط برای چند نقش است
        if ($this->assignments->count() < 2) {
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }
    }

    public function select(int $assignmentId): void
    {
        $assignment = $this->assignments->firstWhere('id', $assignmentId);

        if (! $assignment) {
            return;
        }

        $this->selectedAssignmentId = $assignmentId;
        $this->selectedAssignment = $assignment;
    }

    public function confirm(CurrentRoleContextService $service): void
    {
        $allowedIds = $this->assignments->pluck('id')->all();

        $this->validate([
            'selectedAssignmentId' => [
                'required',
                'integer',
                Rule::in($allowedIds),
            ],
        ]);

        $service->select(
            auth()->user()->person,
            $this->selectedAssignmentId,
        );

        $this->redirectRoute('dashboard', navigate: true);
    }
};
