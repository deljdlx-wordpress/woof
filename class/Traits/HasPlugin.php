<?php

namespace Woof\Traits;

use Phi\Traits\Introspectable;
use Woof\Plugin;

Trait HasPlugin
{
    use Introspectable;

    protected $plugin;

    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     *
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    public function getPluginPath($pluginName = null)
    {
        $path = plugin_dir_path($this->plugin->getDefinitionFolder());

        if ($pluginName !== null) {
            $path = dirname($path) . '/' . $pluginName;
        }

        return $path;
    }

    public function getPluginURL($pluginName = null)
    {

        $url =  plugin_dir_url($this->plugin->getDefinitionFolder());

        if ($pluginName !== null) {
            $url = dirname($url) . '/' . $pluginName;
        }
        return $url;

    }
}

