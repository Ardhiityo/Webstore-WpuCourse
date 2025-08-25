<?php

namespace App\Livewire;

use App\Data\CartData;
use Livewire\Component;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Gate;
use App\Contract\CartServiceInterface;

class Checkout extends Component
{
    public array $data = [
        'full_name' => null,
        'email' => null,
        'phone' => null,
        'shipping_line' => null,
    ];

    public array $summaries = [
        "sub_total" => 0,
        "sub_total_formatted" => "-",
        "shipping_total" => 0,
        "shipping_total_formatted" => "-",
        "grand_total" => 0,
        "grand_total_formatted" => "-"
    ];

    public function mount()
    {
        $gate = Gate::inspect('product-available');

        if ($gate->denied()) {
            return redirect()->route('cart');
        }

        $this->calculateTotal();
    }

    public function rules()
    {
        return [
            'data.full_name' => 'required|string|max:255|min:3',
            'data.email' => 'required|email:dns|max:255',
            'data.phone' => 'required|string|max:20|min:9',
            'data.shipping_line' => 'required|string|max:500|min:10',
        ];
    }

    public function getCartsProperty(CartServiceInterface $cart): CartData
    {
        return $cart->all();
    }

    public function calculateTotal()
    {
        $sub_total = $this->carts->total;
        data_set($this->summaries, 'sub_total', $sub_total);

        $sub_total_formatted = $this->carts->total_formatted;
        data_set($this->summaries, 'sub_total_formatted', $sub_total_formatted);

        $shipping_total = 0;
        data_set($this->summaries, 'shipping_total', $shipping_total);

        $shipping_total_formatted = Number::currency($shipping_total);
        data_set($this->summaries, 'shipping_total_formatted', $shipping_total_formatted);

        $grand_total = $sub_total + $shipping_total;
        data_set($this->summaries, 'grand_total', $grand_total);

        $grand_total_formatted = Number::currency($grand_total);
        data_set($this->summaries, 'grand_total_formatted', $grand_total_formatted);
    }

    public function placeAnOrder()
    {
        $this->validate();

        // proses penyimpanan data order ke database
    }

    public function render()
    {
        return view('livewire.checkout', [
            'carts' => $this->carts,
        ]);
    }
}
