<?php

namespace Woof\Traits;

use Woof\View\View;

trait HasView
{



    protected $assets = [
        'javascripts' => [],
        'css' => [],
    ];



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

    public function addScript($name, $path = '', $dependencies = [], $version = false, $inFooter = false)
    {
        $this->assets['javascripts'][] = [
            $name,
            $path,
            $dependencies,
            $version,
            $inFooter
        ];
        return $this;
    }

    public function addCSS($name, $path = '', $dependencies = [], $version = false, $media  = 'all')
    {
        $this->assets['css'][] = [
            $name,
            $path,
            $dependencies,
            $version,
            $media
        ];
        return $this;
    }


    protected function registerAssets()
    {
        add_action('init', function() {
            foreach($this->assets['javascripts'] as $descriptor) {
                call_user_func_array('wp_enqueue_script', $descriptor);
            }

            foreach($this->assets['css'] as $descriptor) {
                call_user_func_array('wp_enqueue_style', $descriptor);
            }
        });
        return $this;
    }
}
