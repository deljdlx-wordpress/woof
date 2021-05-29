<?php

namespace Woof;

use Woof\Traits\HasPlugin;
use Woof\Traits\HasView;


class AdministrationSection
{

    use HasView {
        HasView::loadTemplate as traitLoadTemplate;
    }

    use HasPlugin;


    protected $plugin;

    protected $entries = [];

    protected $pages = [];

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $this->registerPages();
        $this->register();
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    // NOTICE notice override this method in subclasses
    public function registerPages()
    {
    }



    public function getName()
    {
        // DOC get_current_screen https://developer.wordpress.org/reference/functions/get_current_screen/
        $data = get_current_screen();

        return $data->id;
    }


    public function addPage($page)
    {
        $this->pages[] = $page;
    }


    protected function register()
    {

        add_action('admin_menu', function () {
            foreach($this->pages as $page) {
                $page->register();
            }
        });
    }

    //===============================================================================
}

