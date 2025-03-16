<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PersonnalInvolvedWidget extends Component
{
    /**
     * Create a new component instance.
     */
    public $personnels;
    public $job;
    public $staff;

    /**
     * Create a new component instance.
     */
    public function __construct($personnels,$job,$staff)
    {
        $this->personnels = $personnels;
        $this->job = $job;
        $this->staff = $staff;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.personnal-involved-widget');
    }
}
