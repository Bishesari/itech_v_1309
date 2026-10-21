<?php

use App\Enums\ProductType;
use App\Models\Product;
use App\Models\Standard;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('ایجاد محصول جدید')]
class extends Component
{
    public string $title = '';

    public string $slug = '';

    public string $type = 'written_sample_question';

    public string $short_description = '';

    public string $description = '';

    public string $seo_title = '';

    public string $seo_description = '';

    public string $amount = '';

    public string $standardId = '';

    public bool $is_active = false;

    #[Computed]
    public function standards()
    {
        return Standard::query()
            ->where('is_active', true)
            ->orderBy('name_fa')
            ->get(['id', 'name_fa', 'code']);
    }

    protected function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:2',
                'max:200',
            ],

            'slug' => [
                'required',
                'string',
                'min:2',
                'max:200',
                'regex:/^[A-Za-z-]+$/',
                Rule::unique('products', 'slug'),
            ],

            'type' => [
                'required',
                Rule::enum(ProductType::class),
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'seo_title' => [
                'nullable',
                'string',
                'max:200',
            ],

            'seo_description' => [
                'nullable',
                'string',
                'max:320',
            ],

            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'standardId' => [
                Rule::requiredIf(
                    fn () => $this->type ===
                        ProductType::WrittenSampleQuestion->value
                ),
                'nullable',
                'integer',
                Rule::exists('standards', 'id')
                    ->where('is_active', true),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function updatedType(): void
    {
        if (
            $this->type !==
            ProductType::WrittenSampleQuestion->value
        ) {
            $this->standardId = '';
            $this->resetValidation('standardId');
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        $product = DB::transaction(function () use ($validated) {
            $product = Product::create([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'type' => $validated['type'],
                'short_description' =>
                    $validated['short_description'] ?: null,
                'description' =>
                    $validated['description'] ?: null,
                'seo_title' =>
                    $validated['seo_title'] ?: null,
                'seo_description' =>
                    $validated['seo_description'] ?: null,
                'is_active' => $validated['is_active'],
            ]);

            if (
                $validated['type'] ===
                ProductType::WrittenSampleQuestion->value
            ) {
                $product->standards()->attach(
                    (int) $validated['standardId']
                );
            }

            $product->prices()->create([
                'amount' => (int) $validated['amount'],
                'starts_at' => now(),
                'ends_at' => null,
            ]);

            return $product;
        });

        Flux::toast(
            text: 'محصول با موفقیت ایجاد شد.',
            heading: 'عملیات موفق',
            variant: 'success',
            position: 'top right',
        );

        $this->redirectRoute('product.index', navigate: true);
    }
};
