<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MainButtonWidget extends Component
{
    public string $title;

    public string $routeName;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title, string $routeName)
    {
        $this->title = $title;
        $this->routeName = $routeName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.main-button-widget');
    }
}
