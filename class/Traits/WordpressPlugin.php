<?php

namespace Woof\Traits;

use Phi\Traits\Introspectable;

Trait WordpressPlugin
{
    use Introspectable;

    public function getPluginPath($pluginName = null)
    {
        $path = plugin_dir_path($this->getDefinitionFolder());

        if ($pluginName !== null) {
            $path = dirname($path) . '/' . $pluginName;
        }

        return $path;
    }

    public function getPluginURL($pluginName = null)
    {

        $url =  plugin_dir_url($this->getDefinitionFolder());

        if ($pluginName !== null) {
            $url = dirname($url) . '/' . $pluginName;
        }
        return $url;

    }
}

