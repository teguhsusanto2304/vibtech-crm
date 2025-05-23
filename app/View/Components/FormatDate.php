<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormatDate extends Component
{
    /**
     * Create a new component instance.
     */
    public $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function render()
    {
        return <<<'blade'
            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
        blade;
    }
}
