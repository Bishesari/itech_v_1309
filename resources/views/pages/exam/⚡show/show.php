<?php

use App\Enums\ExamType;
use App\Enums\ProductType;
use App\Models\Exam;
use App\Models\OrderItem;
use App\Services\Exams\StartExamAttemptService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component
{
    public Exam $exam;
    public ?int $selectedOrderItemId = null;

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

    public function startAttempt(StartExamAttemptService $service): void
    {
        try {
            $orderItemId = null;

            if ($this->exam->type === ExamType::Simulated) {
                $this->validate([
                    'selectedOrderItemId' => ['required', 'integer'],
                ], [
                    'selectedOrderItemId.required' => 'ابتدا خرید موردنظر را انتخاب کنید.',
                ]);

                $orderItemId = $this->selectedOrderItemId;
            }

            $attempt = $service->start(
                examId: $this->exam->id,
                user: Auth::user(),
                orderItemId: $orderItemId,
            );

            $this->redirect(
                route('exam.attempt.show', $attempt),
                navigate: true,
            );
        } catch (ValidationException $exception) {
            $this->addError(
                'start',
                collect($exception->errors())->flatten()->first()
            );
        }
    }

    public function with(): array
    {
        $orderItems = collect();

        if ($this->exam->type === ExamType::Simulated) {
            $orderItems = OrderItem::query()
                ->where('product_id', $this->exam->product_id)
                ->where('product_type', ProductType::SimulatedExam->value)
                ->where('quantity', 1)
                ->whereDoesntHave('examAttempt')
                ->whereHas('order', function ($query) {
                    $query
                        ->where('user_id', Auth::id())
                        ->where('status', 'paid')
                        ->whereHas('payments', function ($payments) {
                            $payments->where('status', 'paid');
                        });
                })
                ->orderByDesc('id')
                ->get();
        }

        return [
            'exam' => $this->exam,
            'orderItems' => $orderItems,
        ];
    }
};
