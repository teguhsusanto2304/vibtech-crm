<?php

namespace App\View\Components;

use Illuminate\View\Component;

class JobActionButtons extends Component
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
    public function render()
    {
        return view('components.job-action-buttons');
    }
}
