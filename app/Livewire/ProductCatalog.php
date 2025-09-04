<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;
use Livewire\WithPagination;
use App\Data\ProductCollectionData;
use Illuminate\Support\Facades\Log;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public $select_collection = [];
    public string $sort_by = 'latest';

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'min:3', 'max:30'],
            'select_collection' => ['array'],
            'select_collection.*' => ['integer', 'exists:tags,id'],
            'sort_by' => ['in:latest,oldest,price_asc,price_desc']
        ];
    }

    public function mount()
    {
        $this->validate();
    }

    public function applyFilters()
    {
        $this->validate();

        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->select_collection = [];
        $this->search = '';
        $this->sort_by = 'latest';
        $this->resetPage();
        $this->resetErrorBag();
    }

    protected function queryString()
    {
        return [
            'search' => [
                'except' => '',
            ],
            'select_collection' => [
                'except' => []
            ],
            'sort_by' => [
                'except' => 'latest'
            ]
        ];
    }

    public function render()
    {
        $products = ProductCollectionData::collect([]);
        $collections = ProductData::collect([]);

        if ($this->getErrorBag()->isNotEmpty()) {
            return view('livewire.product-catalog', compact('products', 'collections'));
        }

        $result = Product::query();

        if ($keyword = $this->search) {
            $result->whereLike('name', "%{$keyword}%");
        }

        if (!empty($this->select_collection)) {
            $result->whereHas(
                'tags',
                fn($query) => $query->whereIn('id', $this->select_collection)
            );
        }

        switch ($this->sort_by) {
            case 'oldest':
                $result->oldest();
                break;
            case 'price_asc':
                $result->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $result->orderBy('price', 'desc');
                break;
            default:
                $result->latest();
        }

        $products = ProductData::collect($result->paginate(9));

        $collection_result = Tag::withType('collection')->withCount('products')->get();

        $collections = ProductCollectionData::collect($collection_result);

        return view('livewire.product-catalog', compact('products', 'collections'));
    }
}
