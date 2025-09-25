<?php

namespace App\View\Components;

class BreadcrumbElement {
    public function __construct(public string $name, public ?string $url) {
    }
}
