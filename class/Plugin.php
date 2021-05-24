<?php
// cette classe va nous permettre de gérer notre plugin

namespace Woof;

// use Woof\Model\Database;

use Woof\Model\Wordpress\Database as WordpressDatabase;
use Woof\Model\Wordpress\PostType;
use Woof\Model\Wordpress\Taxonomy;
use Woof\ORM\ORM;
use Woof\Routing\Route;


class Plugin
{

    protected static $instance;

    protected $filepath;

    /**
     * @var WordpressRouter
     */
    protected $router;

    /**
     * @var CustomPostType[]
     */
    protected $customTypes = [];

    /**
     * @var CustomTaxonomy[]
     */
    protected $customTaxonomies = [];

    /**
     * @var PostMetadata[]
     */
    protected $postMetadatas = [];


    protected $roles = [];

    protected $routes = [];


    protected $database;


    public function __construct($filepath)
    {

        $this->filepath = $filepath;

        $this->database = WordpressDatabase::getInstance();
        $this->orm = ORM::getInstance();


        $this->registerRouter();

        $this->registerPostTypes();
        $this->registerPostMetadatas();

        $this->registerTaxonomies();
        $this->registerTaxonomiesMetadata();

        $this->registerCustomRoles();

        $this->registerUserMetadata();
    }

    public static function getInstance($filepath)
    {
        if(static::$instance === null) {
            static::$instance = new static($filepath);
        }
        return static::$instance;
    }


    public function registerUserMetadata() {}
    public function registerPostTypes() {}
    public function registerPostMetadatas() {}
    public function registerTaxonomies() {}
    public function registerTaxonomiesMetadata() {}
    public function registerCustomRoles() {}


    /**
     * @return WordpressDatabase
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     *
     * @return Database
     */
    public function getORM()
    {
        return $this->orm;
    }


    //===============================================================================

    /**
     * @param string $name
     * @param string $label
     * @param string $class
     * @return PostType
     */
    protected function createPostType($label, $name = null, $class = PostType::class)
    {
        $customType = new $class($label, $name);
        $customType->register();
        $this->customTypes[$customType->getName()] = $customType;
        return $customType;
    }


    /**
     * @param string $label
     * @param $postTypes
     * @param string $name
     * @param string $class
     * @return Taxonomy
     */
    protected function createTaxonomy($label, $postTypes = null, $name = null, $class = Taxonomy::class)
    {
        $customTaxonomy = new $class($label, $postTypes, $name);
        $customTaxonomy->register();
        $this->customTaxonomies[$customTaxonomy->getName()] = $customTaxonomy;
        return $customTaxonomy;
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $postType
     * @param string $class
     * @return PostMetadata
     */
    protected function registerPostMetadata($name, $label, $postType, $class = PostMetadata::class)
    {
        $postMetadata = new $class(
            $postType, // le custom post type  sur lequel ajouter le champs supplémentaire
            $name, // l'identifiant (la variable) qui va nous nous permettre de stocker l'information
            $label // libéllé
        );
        $postMetadata->register();
        $this->postMetadatas[$name] = $postMetadata;
        return $postMetadata;
    }

    protected function registerRole($name, $label, $class = CustomRole::class)
    {
        $role = new $class($name, $label);
        $role->register();
        $this->roles[] = $role;
        return $role;
    }

    //===============================================================================

    public function loadTemplate($path, $args = [])
    {
        ob_start();
        extract($args);
        include($path);
        return ob_get_clean();
    }


    //===============================================================================

    /**
     * @return this
     */
    public function registerRouter()
    {
        $this->router = new \Woof\Routing\Router($this);
        $this->registerRoutes();
        $this->router->register();
        return $this;
    }

    /**
     * @return this
     */
    public function addRoute($method, $regexWordpress, $patternCustomRouter, $callback, $name = null)
    {
        $route = new Route($method, $regexWordpress, $patternCustomRouter, $callback, $name);
        $this->router->addRoute($route);
        return $this;
    }

    protected function registerRoutes()
    {

    }

    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return this
     */
    public function route()
    {
        $this->router->route();
        return $this;
    }

    /**
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }


    //===============================================================================

    //===============================================================================
    public static function createCustomTables()
    {

    }



    //===============================================================================
    // méthodes utilitaires
    //===============================================================================

    // appelé lorsque le plugin est désactivé
    /**
     * @return this
     */
    public static function deactivate()
    {
        static::flushRoutes();

    }


    // appelé lorsque le plugin est activé
    public static function activate()
    {
        static::createCustomTables();
    }

    // appelé lors de la désinstallation du plugin ⚠️ Attention cette méthode doit être statique (obligation wordpress)
    public static function uninstall()
    {
    }

    public static function flushRoutes()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

}
