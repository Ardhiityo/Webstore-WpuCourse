<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;
use Livewire\WithPagination;
use App\Data\ProductCollectionData;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public array $select_collection = [];
    public string $sort_by = 'latest';

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->select_collection = [];
        $this->search = '';
        $this->sort_by = 'latest';
        $this->resetPage();
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
