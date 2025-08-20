<?php

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

    public $keyword = '';

    public $collections = [];

    public $sort_by = 'latest';

    protected function rules(): array
    {
        return [
            'keyword' => 'min:3|string|max:255',
            'collections' => 'array',
            'collections.*' => 'integer|exists:tags,id',
            'sort_by' => 'in:latest,oldest,price_asc,price_desc',
        ];
    }

    protected function queryString(): array
    {
        return [
            'keyword' => ['except' => ''],
            'collections' => ['except' => []],
            'sort_by' => ['except' => 'latest'],
        ];
    }

    public function applyFilter()
    {
        $this->validate();

        $this->resetPage();
    }

    public function mount()
    {
        $this->validate();
    }

    public function resetFilter()
    {
        $this->reset();
        $this->keyword = '';
        $this->resetErrorBag();
        $this->resetPage();
    }

    public function render()
    {
        $products = ProductData::collect([]);
        $tags = ProductCollectionData::collect([]);

        if ($this->getErrorBag()->isNotEmpty()) {
            return view('livewire.product-catalog', compact('products', 'tags'));
        }

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
