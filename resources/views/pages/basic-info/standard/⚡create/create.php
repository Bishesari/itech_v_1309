<?php

use App\Models\Field;
use App\Models\Standard;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $field_id = '';
    public string $code = '';
    public string $name_fa = '';
    public string $name_en = '';
    public string $nazari_h = '0';
    public string $amali_h = '0';
    public string $karvarzi_h = '0';
    public string $project_h = '0';
    public string $required_h = '0';
    public string $sum_h = '0';
    protected function rules(): array
    {
        return [
            'field_id' => ['required', 'integer', 'exists:fields,id'],
            'code' => ['required', 'string', 'min:2', 'max:15', Rule::unique('standards', 'code')],
            'name_fa' => ['required', 'string', 'min:2', 'max:100'],
            'name_en' => ['required', 'string', 'min:2', 'max:100'],
            'nazari_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],
            'amali_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],
            'karvarzi_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],
            'project_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],
            'required_h' => ['required', 'numeric', 'gt:0', 'max:9999.9'],
        ];
    }

    public function updatedNazariH(): void
    {
        $this->calcSum();
    }

    public function updatedAmaliH(): void
    {
        $this->calcSum();
    }

    public function updatedKarvarziH(): void
    {
        $this->calcSum();
    }

    public function updatedProjectH(): void
    {
        $this->calcSum();
    }

    private function calculateSum(): float
    {
        return round((float) $this->nazari_h + (float) $this->amali_h + (float) $this->karvarzi_h + (float) $this->project_h, 1);
    }

    private function calcSum(): void
    {
        $this->sum_h = (string) $this->calculateSum();
    }

    public function save(): void
    {
        $validated = $this->validate();

        // مجموع ساعات فقط در سمت سرور محاسبه می‌شود.
        $validated['sum_h'] = $this->calculateSum();

        $standard = Standard::query()->create($validated);

        $this->modal('new-standard')->close();

        $this->dispatch('standard-created', id: $standard->id);

        $this->resetForm();
    }

    #[Computed]
    public function fields()
    {
        return Field::query()
            ->orderBy('title')
            ->get();
    }

    public function resetForm(): void
    {
        $this->reset([
            'field_id',
            'code',
            'name_fa',
            'name_en',
            'required_h',
        ]);

        $this->nazari_h = '0';
        $this->amali_h = '0';
        $this->karvarzi_h = '0';
        $this->project_h = '0';
        $this->sum_h = '0';

        $this->resetValidation();
    }
};
