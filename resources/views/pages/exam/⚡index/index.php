<?php

use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;

new
#[Layout('layouts::app')]
class extends Component
{
    public function with(): array
    {
        $now = now();

        $exams = Exam::query()
            ->with('standard')
            ->where('is_active', true)
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('starts_at')
            ->orderBy('title')
            ->get();

        return compact('exams');
    }
};
