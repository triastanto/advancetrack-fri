<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Input extends Component
{
    public $type;
    public $name;
    public $label;
    public $placeholder;
    public $required;
    public $value;
    public $error;
    public $helpText;
    public $wireModel;
    public $attributes;

    public function __construct(
        $type = 'text',
        $name = '',
        $label = '',
        $placeholder = '',
        $required = false,
        $value = '',
        $error = '',
        $helpText = '',
        $wireModel = '',
        $attributes = []
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->value = $value;
        $this->error = $error;
        $this->helpText = $helpText;
        $this->wireModel = $wireModel;
        $this->attributes = $attributes;
    }

    public function render()
    {
        return view('components.form.input');
    }
}
