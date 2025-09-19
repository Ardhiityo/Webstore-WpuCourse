<?php

namespace App\Livewire;

use App\Data\CartData;
use App\Rules\ValidPaymentMethodHash;
use App\Rules\ValidShippingHash;
use Livewire\Component;
use App\Data\RegionData;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Contract\CartServiceInterface;
use App\Data\CheckoutData;
use App\Data\CustomerData;
use App\Data\ShippingData;
use App\Services\CheckoutService;
use App\Services\PaymentMethodQueryService;
use App\Services\RegionQueryService;
use App\Services\ShippingMethodService;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;

class Checkout extends Component
{
    public array $summaries = [
        'sub_total' => 0,
        'sub_total_formatted' => '-',
        'shipping_total' => 0,
        'shipping_total_formatted' => '-',
        'grand_total' => 0,
        'grand_total_formatted' => '-',
        'total_weight' => 0,
    ];

    public array $region_selector = [
        'keyword' => null,
        'region_selected' => null,
    ];

    public array $shipping_selector = [
        'shipping_method' => null,
    ];

    public array $payment_method_selector = [
        'payment_method_selected' => null
    ];

    public array $data = [
        'full_name' => null,
        'email' => null,
        'phone' => null,
        'address_line' => null,
        'destination_region_code' => null,
        'shipping_hash' => null,
        'payment_method_hash' => null
    ];

    protected function rules(): array
    {
        return [
            'data.full_name' => ['required', 'string', 'min:3', 'max:25'],
            'data.email' => ['required', 'string', 'email:dns', 'min:3', 'max:25'],
            'data.phone' => ['required', 'min:8', 'max:13'],
            'data.address_line' => ['required', 'string', 'min:8', 'max:255'],
            'data.destination_region_code' => ['required', 'string', 'exists:regions,code'],
            'data.shipping_hash' => ['required', 'string', new ValidShippingHash()],
            'data.payment_method_hash' => ['required', new ValidPaymentMethodHash()]
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'data.full_name' => 'Name',
            'data.email' => 'Email',
            'data.phone' => 'Phone',
            'data.address_line' => 'Address',
            'data.destination_region_code' => 'Region',
            'data.shipping_hash' => 'Shipping Method',
            'data.payment_method_hash' => 'Payment Method'
        ];
    }

    public function mount()
    {
        if (Gate::denies('is_stock_available')) {
            return redirect()->route('cart');
        }

        $this->calculateTotal();
    }

    public function getCartProperty(CartServiceInterface $cart): CartData
    {
        return $cart->all();
    }

    public function getRegionsProperty(RegionQueryService $query_service): DataCollection
    {
        $keyword = data_get($this->region_selector, 'keyword');

        if (!$keyword) {
            return new DataCollection(RegionData::class, []);
        }

        return $query_service->searchRegionByName($keyword);
    }

    public function getRegionProperty(RegionQueryService $query_service): ?RegionData
    {
        $region_selected = data_get($this->region_selector, 'region_selected');

        if (!$region_selected) {
            return null;
        };

        return $query_service->searchRegionByCode($region_selected);
    }

    /**
     * Summary of getShippingMethodsProperty
     * @return DataCollection<ShippingData>
     */
    public function getShippingMethodsProperty(RegionQueryService $region_query, ShippingMethodService $shipping_service): DataCollection|Collection
    {
        if (!data_get($this->data, 'destination_region_code')) {
            return new DataCollection(ShippingData::class, []);
        }

        $origin_code = config('shipping.shipping_origin_code');

        return $shipping_service->getShippingMethods(
            $region_query->searchRegionByCode($origin_code),
            $region_query->searchRegionByCode(data_get($this->data, 'destination_region_code')),
            $this->cart
        )->toCollection()->groupBy('service');
    }

    public function getPaymentMethodsProperty(PaymentMethodQueryService $query_service): Collection
    {
        return $query_service->getPaymentMethods()->toCollection();
    }

    public function updatedPaymentMethodSelectorPaymentMethodSelected($value)
    {
        data_set($this->data, 'payment_method_hash', $value);
    }

    public function updatedRegionSelectorRegionSelected($value)
    {
        data_set($this->data, 'destination_region_code', $value);
    }

    public function updatedShippingSelectorShippingMethod($value)
    {
        data_set($this->data, 'shipping_hash', $value);

        $this->calculateTotal();
    }

    public function getShippingMethodProperty(ShippingMethodService $query_service)
    {
        $region_selected = data_get($this->region_selector, 'region_selected');
        $shipping_method = data_get($this->shipping_selector, 'shipping_method');

        if (!$region_selected || !$shipping_method) {
            return null;
        }

        $data = $query_service->getShippingMethod($shipping_method);

        if (!$data) {
            $this->addError('shipping_hash', 'Ups, transaction timeout');
            return redirect()->route('checkout');
        }

        return $data;
    }

    public function placeAnOrder(
        CartServiceInterface $cart
    ) {
        $validated = $this->validate();

        $shipping_hash = data_get($validated, 'data.shipping_hash');
        $shipping_method = app(ShippingMethodService::class)->getShippingMethod($shipping_hash);

        $payment_method_hash = data_get($validated, 'data.payment_method_hash');
        $payment = app(PaymentMethodQueryService::class)
            ->getPaymentMethodByHash($payment_method_hash);

        $checkout = CheckoutData::from([
            'customer' => CustomerData::from(data_get($validated, 'data')),
            'address_line' => data_get($validated, 'data.address_line'),
            'origin' => $shipping_method->origin,
            'destination' => $shipping_method->destination,
            'cart' => $this->cart,
            'payment' => $payment,
            'shipping' => $shipping_method
        ]);

        $service = app(CheckoutService::class);

        $sales_order = $service->makeAnOrder($checkout);

        $cart->clear();

        return redirect()->route('order-confirmed', $sales_order->trx_id);
    }

    public function calculateTotal()
    {
        $cart = $this->cart;

        data_set($this->summaries, 'sub_total', $cart->total);
        data_set($this->summaries, 'sub_total_formatted', $cart->total_formatted);

        $shipping_cost = $this->shipping_method?->cost ?? 0;
        data_set($this->summaries, 'shipping_total', $shipping_cost);

        $shipping_total_formatted = Number::currency($shipping_cost);
        data_set($this->summaries, 'shipping_total_formatted', $shipping_total_formatted);

        $grand_total = $cart->total + $shipping_cost;
        data_set($this->summaries, 'grand_total', $grand_total);
        data_set($this->summaries, 'grand_total_formatted', Number::currency($grand_total));

        data_set($this->summaries, 'total_weight', $cart->total_weight);
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
