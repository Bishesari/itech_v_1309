<?php

use App\Enums\ProductType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

new
#[Layout('layouts::public')]
class extends Component
{
    #[Locked]
    public ?string $receiptOrderNumber = null;

    #[Locked]
    public ?int $receiptAmount = null;

    #[Locked]
    public ?string $receiptReference = null;

    #[Locked]
    public ?string $receiptPaidAt = null;

    public Product $product;

    public function mount(): void
    {
        abort_unless($this->product->is_active, 404);

        $orderNumber = request()->query('order');

        if (! auth()->check() || ! is_string($orderNumber) || $orderNumber === '') {
            return;
        }

        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->whereHas('items', function ($query) {
                $query
                    ->where(
                        'product_type',
                        ProductType::WrittenSampleQuestion->value,
                    )
                    ->where('product_id', $this->product->getKey());
            })
            ->first();

        if (! $order) {
            return;
        }

        $payment = $order->payments()
            ->where('user_id', auth()->id())
            ->where('status', Payment::STATUS_PAID)
            ->whereNotNull('verified_at')
            ->latest('verified_at')
            ->first();

        if (! $payment) {
            return;
        }

        $this->receiptOrderNumber = $order->order_number;
        $this->receiptAmount = $payment->amount;
        $this->receiptReference = $payment->gateway_reference;
        $this->receiptPaidAt = $payment->verified_at?->format('Y/m/d H:i');
    }

    public function render()
    {
        return $this->view()
            ->title(
                $this->product->seo_title
                    ?: $this->product->title
            );
    }
};
