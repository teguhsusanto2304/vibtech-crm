<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VehicleRequireSwitchWidget extends Component
{
    public $job;

    /**
     * Create a new component instance.
     */
    public function __construct($job)
    {
        $this->job = $job;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.vehicle-require-switch-widget');
    }
}
