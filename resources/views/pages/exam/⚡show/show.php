<?php

use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public Exam $exam;

    public function mount(Exam $exam): void
    {
        abort_unless(
            $exam->is_active
            && ($exam->starts_at === null || $exam->starts_at->lte(now()))
            && ($exam->ends_at === null || $exam->ends_at->gte(now())),
            404,
        );

        $this->exam = $exam->load('standard');
    }

    public function with(): array
    {
        return [
            'exam' => $this->exam,
        ];
    }
};
