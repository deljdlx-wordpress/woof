<?php

namespace Woof;

use Woof\Traits\HasView;
use Woof\Traits\WordpressPlugin;

class Administration
{

    use WordpressPlugin;
    use HasView;

    protected $entries = [];

    public function __construct()
    {
        $this->registerPages();
        $this->addAssets();
        $this->register();


    }

    public function addAssets()
    {

        $this->addCSS('wp-jquery-ui-dialog');

        $this->addScript('jquery-ui-dialog');

        $this->addScript(
            'woof-rest-client',
            $this->getPluginURL('woof') . '/public/assets/javascript/WoofRestClient.js',
        );
    }


    public function registerPages()
    {

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

    public function register()
    {
        $this->loadAssets();

        add_action('admin_menu', function () {
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
        });
    }
    //===============================================================================
}

