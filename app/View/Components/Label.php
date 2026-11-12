<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Label extends Component
{
    public $value;
    /**
     * Create a new component instance.
     * @param string $value
     * @return void
     */
    public function __construct($value = '')
    {
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     * 
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): View|Closure|string
    {
        return view('components.label');
    }
}
