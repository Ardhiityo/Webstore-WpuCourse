<?php

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;
use Livewire\WithPagination;
use App\Data\ProductCollectionData;
use App\Data\ShippingServiceData;
use Spatie\LaravelData\DataCollection;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public $select_collection = [];
    public string $sort_by = 'latest';

    protected function queryString()
    {
        return [
            'search' => ['except' => ''],
            'select_collection' => ['except' => []],
            'sort_by' => ['except' => 'latest'],
        ];
    }

    public function applyFilters()
    {
        $this->validate();
        $this->resetPage();
    }

    public function mount()
    {
        $this->validate();
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetErrorBag();
        $this->resetPage();
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'min:3', 'max:20'],
            'select_collection' => ['array'],
            'select_collection.*' => ['int', 'exists:tags,id'],
            'sort_by' => ['in:latest,oldest,price_asc,price_desc']
        ];
    }

    public function render()
    {
        $products = ProductData::collect([]);
        $collections = ProductCollectionData::collect([]);

        if ($this->getErrorBag()->isNotEmpty()) {
            return view('livewire.product-catalog', compact('products', 'collections'));
        }

        $query = Product::query();

        if ($this->search) {
            $query->whereLike('name', "%{$this->search}%");
        }

        if (!empty($this->select_collection)) {
            $query->whereHas(
                'tags',
                fn($q) => $q->whereIn('id', $this->select_collection)
            );
        }

        switch ($this->sort_by) {
            case 'oldest':
                $query->oldest();
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = ProductData::collect($query->paginate(9));

        $result_collections = Tag::withType('collection')->withCount('products')->get();

        $collections = ProductCollectionData::collect($result_collections);

        return view('livewire.product-catalog', compact('products', 'collections'));
    }
}
