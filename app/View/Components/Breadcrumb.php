<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $breadcrumb;

    public $title;

    public function __construct($breadcrumb = [], $title = '')
    {
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}
