<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Collection;

class SalesPersonFilter extends Component
{
    /**
     * Create a new component instance.
     */
    public Collection $salesPersons;
    public string $salesPersonTitle;

    /**
     * Create a new component instance.
     *
     * @param Collection $salesPersons  A collection of sales persons.
     * @param string|null $salesPersonTitle The title for the sales person filter.
     */
    public function __construct(Collection $salesPersons, ?string $salesPersonTitle = null) // Make it nullable and set default in constructor if not provided
    {
         $this->salesPersons = $salesPersons;
        // Set default here if it wasn't provided
        $this->salesPersonTitle = $salesPersonTitle ?? 'Salesperson';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sales-person-filter');
    }
}
