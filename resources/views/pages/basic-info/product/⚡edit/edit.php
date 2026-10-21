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
#[Title('ویرایش محصول')]
class extends Component
{
    public Product $product;

    public string $title = '';

    public string $slug = '';

    public string $type = '';

    public string $short_description = '';

    public string $description = '';

    public string $seo_title = '';

    public string $seo_description = '';

    public string $amount = '';

    public string $standardId = '';

    public bool $is_active = false;

    public function mount(Product $product): void
    {
        $this->product = $product;

        $this->title = $product->title;
        $this->slug = $product->slug;
        $this->type = $product->type->value;
        $this->short_description = $product->short_description ?? '';
        $this->description = $product->description ?? '';
        $this->seo_title = $product->seo_title ?? '';
        $this->seo_description = $product->seo_description ?? '';
        $this->is_active = $product->is_active;

        $currentPrice = $product->currentPrice();

        $this->amount = $currentPrice
            ? (string) $currentPrice->amount
            : '';

        $this->standardId = (string) (
            $product->standards()->value('standards.id') ?? ''
        );
    }

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
            'title' => ['required', 'string', 'min:2', 'max:200'],

            'slug' => [
                'required',
                'string',
                'min:2',
                'max:200',
                'regex:/^[A-Za-z-]+$/',
                Rule::unique('products', 'slug')->ignore($this->product->id),
            ],

            'type' => ['required', Rule::enum(ProductType::class)],

            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:200'],
            'seo_description' => ['nullable', 'string', 'max:320'],

            'amount' => ['required', 'integer', 'min:1'],

            'standardId' => [
                Rule::requiredIf(
                    fn () => $this->type === ProductType::WrittenSampleQuestion->value
                ),
                'nullable',
                'integer',
                Rule::exists('standards', 'id'),
            ],

            'is_active' => ['required', 'boolean'],
        ];
    }

    public function updatedType(): void
    {
        if ($this->type !== ProductType::WrittenSampleQuestion->value) {
            $this->standardId = '';
            $this->resetValidation('standardId');
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $product = Product::query()
                ->whereKey($this->product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $product->update([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'type' => $validated['type'],
                'short_description' => $validated['short_description'] ?: null,
                'description' => $validated['description'] ?: null,
                'seo_title' => $validated['seo_title'] ?: null,
                'seo_description' => $validated['seo_description'] ?: null,
                'is_active' => $validated['is_active'],
            ]);

            if ($validated['type'] === ProductType::WrittenSampleQuestion->value) {
                $product->standards()->sync([(int) $validated['standardId']]);
            } else {
                $product->standards()->detach();
            }

            $now = now();

            $currentPrice = $product->prices()
                ->where('starts_at', '<=', $now)
                ->where(fn ($query) => $query
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>', $now)
                )
                ->orderByDesc('starts_at')
                ->lockForUpdate()
                ->first();

            $newAmount = (int) $validated['amount'];

            if (! $currentPrice || $currentPrice->amount !== $newAmount) {
                $startsAt = $now->copy();

                // جلوگیری از ایجاد بازه صفرثانیه‌ای در صورت
                // تغییر قیمت در همان ثانیه ثبت قیمت قبلی
                if ($currentPrice && $startsAt->lessThanOrEqualTo($currentPrice->starts_at)) {
                    $startsAt = $currentPrice->starts_at->copy()->addSecond();
                }

                if ($currentPrice) {
                    $currentPrice->update([
                        'ends_at' => $startsAt,
                    ]);
                }

                $product->prices()->create([
                    'amount' => $newAmount,
                    'starts_at' => $startsAt,
                    'ends_at' => null,
                ]);
            }
        });

        unset($this->product);

        Flux::toast(
            text: 'اطلاعات محصول با موفقیت ویرایش شد.',
            heading: 'عملیات موفق',
            variant: 'success',
            position: 'top right',
        );

        $this->redirectRoute('product.index', navigate: true);
    }
};
