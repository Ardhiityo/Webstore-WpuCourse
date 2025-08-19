<?php

namespace App\Livewire;

use App\Data\ProductCollectionData;
use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public $keyword = '';
    public $collections = [];
    public $sort_by = 'latest';

    public function applyFilter()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset();
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query();

        if ($this->keyword) {
            $query->whereLike('name', '%' . $this->keyword . '%');
        }

        if (!empty($this->collections)) {
            $query->whereHas('tags', function ($q) {
                $q->whereIn('id', $this->collections);
            });
        }

        switch ($this->sort_by) {
            case 'latest':
                $query->latest();
                break;
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

        $tags = Tag::query()->withType('collection')->withCount('products')->get();

        ProductCollectionData::collect($tags);

        return view('livewire.product-catalog', compact('products', 'tags'));
    }
}
