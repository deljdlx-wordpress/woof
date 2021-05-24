<?php
require_once __DIR__ .'/static-vendor/autoload.php';


require_once __DIR__ .'/woof-package/phi/phi-filesystem/source/autoload.php';
require_once __DIR__ .'/woof-package/phi/phi-traits/source/autoload.php';
require_once __DIR__ .'/woof-package/phi/phi-html/source/autoload.php';
require_once __DIR__ .'/woof-package/phi/phi-container/source/autoload.php';

require_once __DIR__ .'/woof-package/woof/woof-model/autoload.php';
require_once __DIR__ .'/woof-package/woof/woof-orm/autoload.php';
require_once __DIR__ .'/woof-package/woof/woof-theme/autoload.php';
require_once __DIR__ .'/woof-package/woof/woof-view/autoload.php';


require_once(ABSPATH.'wp-admin/includes/user.php');

// nous stokons dans la constante OPROFILE_FILEPATH le chemin fichier correspondant à la racine du plugin
if(!defined('WOOF_FILEPATH')) {
    define('WOOF_FILEPATH', __DIR__);
}
