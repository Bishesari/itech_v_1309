<?php

use App\Models\Question;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('نمایش سؤال')] class extends Component
{
    public Question $question;

    public function mount(Question $question): void
    {
        $this->question = $question->load([
            'chapter.standard.field',
            'options',
            'assignedBy',
        ]);
    }
};
