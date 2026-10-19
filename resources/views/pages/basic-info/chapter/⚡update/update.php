<?php

use App\Models\Chapter;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public Chapter $chapter;

    public string $number = '';

    public string $title = '';

    public function mount(Chapter $chapter): void
    {
        $this->chapter = $chapter;

        $this->number = (string) $chapter->number;
        $this->title = $chapter->title;
    }

    protected function rules(): array
    {
        return [
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('chapters', 'number')
                    ->where('standard_id', $this->chapter->standard_id)
                    ->ignore($this->chapter->id),
            ],

            'title' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
        ];
    }

    public function resetFormValidation(): void
    {
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate();

        $this->chapter->update($validated);

        $this->modal('edit-chapter-'.$this->chapter->id)->close();

        $this->dispatch('chapter-updated', id: $this->chapter->id);
    }
};
