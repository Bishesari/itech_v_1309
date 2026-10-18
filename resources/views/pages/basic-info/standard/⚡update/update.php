<?php

use App\Models\Standard;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public Standard $standard;

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

    public function mount(Standard $standard): void
    {
        $this->standard = $standard;

        $this->field_id = (string) $standard->field_id;
        $this->code = $standard->code;
        $this->name_fa = $standard->name_fa;
        $this->name_en = $standard->name_en;

        $this->nazari_h = (string) $standard->nazari_h;
        $this->amali_h = (string) $standard->amali_h;
        $this->karvarzi_h = (string) $standard->karvarzi_h;
        $this->project_h = (string) $standard->project_h;
        $this->required_h = (string) $standard->required_h;
        $this->sum_h = (string) $standard->sum_h;
    }

    protected function rules(): array
    {
        return [
            'field_id' => [
                'required',
                'integer',
                'exists:fields,id',
            ],

            'code' => [
                'required',
                'string',
                'min:2',
                'max:15',
                Rule::unique('standards', 'code')->ignore($this->standard->id),
            ],

            'name_fa' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'name_en' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'nazari_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],
            'amali_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],
            'karvarzi_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],
            'project_h' => ['required', 'numeric', 'min:0', 'max:9999.9'],

            'required_h' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999.9',
            ],
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
        return round(
            (float) $this->nazari_h
            + (float) $this->amali_h
            + (float) $this->karvarzi_h
            + (float) $this->project_h,
            1
        );
    }

    private function calcSum(): void
    {
        $this->sum_h = (string) $this->calculateSum();
    }

    public function resetFormValidation(): void
    {
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate();

        // مجموع ساعات را در سمت سرور محاسبه می‌کنیم.
        $validated['sum_h'] = $this->calculateSum();

        $this->standard->update($validated);

        $this->modal('edit-standard-'.$this->standard->id)->close();

        $this->dispatch('standard-updated', id: $this->standard->id);
    }
};
