<?php

namespace Woof;

class Administration
{

    use Traits\Displayable;

    protected $entries = [];

    public function __construct()
    {
        add_action('admin_menu', function () {
            $this->registerPages();
        });
    }

    public function addPage($name,  $callback, $slug = null, $parent = false, $capability = 'activate_plugins', $pageTitle = null, $icon = 'dashicons-admin-tools', $order = null)
    {
        if($slug === null) {
            $slug = slugify($name);
        }

        if($pageTitle === null) {
            $pageTitle = $name . ' - page';
        }


        $this->entries[$slug] = [
            'pageTitle' => $pageTitle,
            'name' => $name,
            'capability' => $capability,
            'callback' => $callback,
            'icon' => $icon,
            'order' => $order,
            'parent' => $parent,
        ];
    }

    public function registerPages()
    {

        foreach($this->entries as $slug => $descriptor) {

            // https://developer.wordpress.org/reference/functions/add_submenu_page/
            // https://developer.wordpress.org/reference/functions/add_page/

            if(!$descriptor['parent']) {
                add_menu_page(
                    $descriptor['pageTitle'],
                    $descriptor['name'],
                    $descriptor['capability'],
                    $slug,
                    $descriptor['callback'],
                    $descriptor['icon'],
                    $descriptor['order'],
                );
            }
            else {
                add_submenu_page(
                    $descriptor['parent'],
                    $descriptor['pageTitle'],
                    $descriptor['name'],
                    $descriptor['capability'],
                    $slug,
                    $descriptor['callback'],
                    $descriptor['order']
                );
            }
        }
    }

    //===============================================================================


}

