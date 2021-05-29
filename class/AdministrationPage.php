<?php

namespace Woof;

use Woof\Traits\HasPlugin;
use Woof\Traits\HasView;

class AdministrationPage
{

    use HasPlugin;
    use HasView {
        HasView::loadTemplate as traitLoadTemplate;
    }

    protected $name;
    protected $menuTitle;
    protected $slug;


    protected $capability = 'activate_plugins';
    protected $icon = 'dashicons-admin-tools';
    protected $order = null;


    protected $section;

    protected $template;


    public function __construct($section, $name, $slug = null, $parent = null, $capability = 'activate_plugins')
    {

        $this->section = $section;
        $this->plugin = $this->section->getPlugin();

        if($slug === null) {
            $slug = slugify($name);
        }

        $this->slug = $slug;
        $this->menuTitle = $name;
        $this->name = $name;
        $this->capability = $capability;

        $this->parent = $parent;

        $this->addAssets();

        $this->registerAssets();
    }

    public function setMenuTitle($title)
    {
        $this->menuTitle = $title;
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

    public function setTemplate($path)
    {
        $this->template = $path;
        return $this;
    }

    public function display($args = [])
    {
        if($this->template) {
            $template = $this->loadTemplate( $this->getPluginPath().  $this->template, $args);
            echo $template;
        }
    }

    public function isCurrent()
    {
        if (preg_match('`page_' . $this->slug . '$`', $this->getScreenId())) {
            return true;
        }
        return false;
    }

    public function getScreenId()
    {
        $data = get_current_screen();
        return $data->id;
    }


    public function getSlug()
    {
        return $this->slug;
    }


    public function loadTemplate($path, $args = [])
    {
        $template = $this->traitLoadTemplate($path, $args);
        $template->set('pluginURL', $this->getPluginURL());
        $template->set('pluginPath', $this->getPluginPath());
        return $template;
    }

    public function register()
    {
        if(!$this->parent) {
            //DOC add_menu_page https://developer.wordpress.org/reference/functions/add_menu_page/
            add_menu_page(
                $this->name,
                $this->menuTitle,
                $this->capability,
                $this->slug,
                [$this, 'display'],
                $this->icon,
                $this->order,
            );
        }
        else {
            //DOC add_submenu_page https://developer.wordpress.org/reference/functions/add_submenu_page/
            add_submenu_page(
                $this->parent->getSlug(),
                $this->name,
                $this->menuTitle,
                $this->capability,
                $this->slug,
                [$this, 'display'],
                $this->order,
            );
        }

    }

    protected function registerAssets()
    {
        // DOC hooks order https://codex.wordpress.org/Plugin_API/Action_Reference
        add_action('admin_enqueue_scripts', function() {

            if($this->isCurrent()) {
                foreach($this->assets['javascripts'] as $descriptor) {
                    call_user_func_array('wp_enqueue_script', $descriptor);
                }

                foreach($this->assets['css'] as $descriptor) {
                    call_user_func_array('wp_enqueue_style', $descriptor);
                }
            }
        });
        return $this;
    }

}

