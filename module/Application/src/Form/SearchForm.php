<?php

declare(strict_types=1);

namespace Application\Form;

use Laminas\Form\Form;

class SearchForm extends Form
{
    public function __construct()
    {
        parent::__construct('search');

        $this->add([
            'name' => 'name',
            'type' => 'text',
        ]);
        $this->add([
            'name' => 'language',
            'type' => 'text',
        ]);
        $this->add([
            'name' => 'genre',
            'type' => 'text',
        ]);
    }
}
