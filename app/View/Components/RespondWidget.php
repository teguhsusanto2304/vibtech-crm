<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RespondWidget extends Component
{
    public $personnels;
    public $respond;
    public $job;
    /**
     * Create a new component instance.
     */
    public function __construct($personnels,$respond,$job)
    {
        $this->personnels = $personnels;
        $this->respond = $respond;
        $this->job = $job;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.respond-widget');
    }
}
