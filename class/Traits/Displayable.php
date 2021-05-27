<?php

namespace Woof\Traits;

use Woof\View\Template;
use Woof\View\View;

trait Displayable
{

    /**
     * @param string $path
     * @param array $args
     * @return View
     */
    public function loadTemplate($path, $args = [])
    {
        $view = new View($path);
        $view->set($args);
        return $view;
    }

    public function addScript($name, $path, $dependencies = [], $version = false, $inFooter = false)
    {
        add_action('init', function() use($name, $path, $dependencies, $version, $inFooter) {
            // echo __FILE__.':'.__LINE__; exit();
            wp_enqueue_script(
                $name,
                $path,
                $dependencies,
                $version,
                $inFooter
            );
        });
    }
}
