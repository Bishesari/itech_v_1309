<?php

use App\Models\Standard;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public Standard $standard;

    public string $number = '';

    public string $title = '';

    protected function rules(): array
    {
        return [
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chapters', 'number')
                    ->where('standard_id', $this->standard->id),
            ],
            'title' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $chapter = $this->standard->chapters()->create($validated);

        $this->modal('new-chapter')->close();

        $this->resetForm();

        $this->dispatch('chapter-created', id: $chapter->id);
    }

    public function resetForm(): void
    {
        $this->reset(['number', 'title']);

        $this->resetValidation();
    }
};
