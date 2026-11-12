<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $type;
    public $name;
    public $value;

    /**
     * Create a new component instance.
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @return void
     */
    public function __construct($type = 'text', $name = '', $value = '')
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}
