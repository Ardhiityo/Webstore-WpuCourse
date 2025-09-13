<?php

namespace App\Livewire;

use App\Data\CartData;
use Livewire\Component;
use App\Data\RegionData;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Contract\CartServiceInterface;
use App\Data\ShippingData;
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
        'total_weight' => 0
    ];

    public array $region_selector = [
        'keyword' => null,
        'region_selected' => null,
    ];

    public array $shipping_selector = [
        'shipping_selected' => null
    ];

    public array $data = [
        'full_name' => null,
        'email' => null,
        'phone' => null,
        'address_line' => null,
        'destination_region_code' => null
    ];

    public function rules(): array
    {
        return [
            'data.full_name' => ['required', 'string', 'min:3', 'max:25'],
            'data.email' => ['required', 'string', 'email:dns', 'min:3', 'max:25'],
            'data.phone' => ['required', 'integer', 'min:8', 'max:13'],
            'data.address_line' => ['required', 'string', 'min:8', 'max:255'],
            'data.destination_region_code' => ['required', 'string', 'exists:regions,code'],
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

    public function updatedRegionSelectorRegionSelected($value)
    {
        data_set($this->data, 'destination_region_code', $value);
    }

    public function placeAnOrder()
    {
        $this->validate();
    }

    public function calculateTotal()
    {
        $cart = $this->cart;

        data_set($this->summaries, 'sub_total', $cart->total);
        data_set($this->summaries, 'sub_total_formatted', $cart->total_formatted);

        $shipping_cost = 0;
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
