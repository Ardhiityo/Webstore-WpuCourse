<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class PageStatic extends Component
{
    public Page $page;

    public function mount(Page $page)
    {
        if (!$page->id) {
            return redirect()->route('home');
        }
        $this->page = $page;
    }

    public function render()
    {
        return view('livewire.page-static');
    }
}
