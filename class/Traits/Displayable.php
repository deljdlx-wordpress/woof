<?php

namespace Woof\Traits;

trait Displayable
{

    public function loadTemplate($path, $args = [])
    {
        ob_start();
        extract($args);
        include($path);
        return ob_get_clean();
    }
}
