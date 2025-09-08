<?php

namespace App\Livewire;

use App\Data\CartData;
use Livewire\Component;
use App\Data\RegionData;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Contract\CartServiceInterface;
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
        'region_selected' => null
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

    public function getCartProperty(CartServiceInterface $cart): CartData
    {
        return $cart->all();
    }

    public function mount(CartServiceInterface $carts)
    {
        if (Gate::denies('is_stock_available')) {
            return redirect()->route('cart');
        }

        $this->calculateTotal();
    }

    public function placeAnOrder(CartServiceInterface $carts)
    {
        $this->validate();

        $this->calculateTotal();
    }

    public function updatedRegionSelectorRegionSelected($value)
    {
        data_set($this->data, 'destination_region_code', $value);
    }

    public function getRegionsProperty(): DataCollection
    {
        $data = [
            [
                'code' => 'b001',
                'province' => 'jawa barat',
                'city' => 'bandung',
                'district' => 'cikutra barat',
                'sub_district' => 'cikutra barat',
                'postal_code' => 42411
            ],
            [
                'code' => 'b002',
                'province' => 'jawa barat',
                'city' => 'bandung',
                'district' => 'cikutra timur',
                'sub_district' => 'cikutra timur',
                'postal_code' => 42412
            ]
        ];

        if (!data_get($this->region_selector, 'keyword')) {

            $data = [];
        }

        return new DataCollection(RegionData::class, $data);
    }

    public function getRegionProperty(): ?RegionData
    {
        $region_selected = data_get($this->region_selector, 'region_selected');

        if (!$region_selected) {
            return null;
        };

        return $this->regions->toCollection()->first(fn(RegionData $region) => $region->code === $region_selected);
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
