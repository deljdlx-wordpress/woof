<?php

namespace Woof;

use Woof\Model\Wordpress\Post;
use WP_REST_Request;

class RestApi
{
    protected $baseURI;
    protected $namespace = 'woof/v1';

    protected $routes = [];

    protected $plugin;

    public function __construct($plugin, $namespace = null)
    {

        $this->plugin = $plugin;

        if($namespace !== null) {
            $this->namespace = $namespace;
        }
        // calcul "automatique" du base uri du site
        $this->baseURI = dirname($_SERVER['SCRIPT_NAME']);

        $this->registerRoutes();

        $this->register();
    }


    public function registerRoutes()
    {
    }

    public function register()
    {
        add_action('jwt_auth_whitelist', function($endpoints) {
            foreach($this->routes as $route) {
                if($route['whitelisted']) {
                    $endpoints[] = $this->baseURI . '/wp-json/' . $this->namespace . $route['path'];
                }
            }
            return $endpoints;
        });

        add_action('rest_api_init', function() {
            foreach($this->routes as $route) {
                // arguments:
                // - le namespace de l'api
                // - le chemin
                // - définition du endpoint (= action à réaliser lorsqu'une route est apellée)
                register_rest_route(
                    $this->namespace,
                    $route['path'],
                    [
                        'methods' => $route['method'],
                        'callback' => $route['callback'],
                        'permission_callback' => function ( ) {
                            return true;
                        },
                    ]
                );
            }
        });
    }

    public function addRoute($method, $path, $callback, $whiteListed = false)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'whitelisted' => $whiteListed
        ];
    }





    // ==================================================================================================
    // ==================================================================================================

    // ==================================================================================================
    // ==================================================================================================


    public function uploadImage()
    {
        // header("Access-Control-Allow-Origin: *");
        // header("Content*-type: text/html");

        $imageFileIndex = 'image';

        // récupération des informations concernant l'image uploadée
        $imageData = $_FILES[$imageFileIndex];

        // récupération du chemin fichier dans lequel est stocké l'image qui vient d'être uploadée
        $imageSource = $imageData['tmp_name'];

        // récupération es informations du dossier dans lequel wp stocke les fichiers uploadés
        $destination = wp_upload_dir();

        // dossier worpdress dans lequel nous allons stocker l'image
        $imageDestinationFoler = $destination['path'];

        // DOC nettoyage d'un nom de fichier avec wp https://developer.wordpress.org/reference/functions/sanitize_file_name/
        $imageName =  sanitize_file_name(
            md5(uniqid()) . '-' . // génération d'une partie aléatoire pour ne pas écraser de fichier existant
            $imageData['name']);
        $imageDestination = $imageDestinationFoler . '/' . $imageName;

        // on déplace le fichier uploadé dans le dossier de stokage de wp
        $success = move_uploaded_file($imageSource, $imageDestination);

        // si le déplacement du fichier à bien fonctionné
        if($success) {
            // récupération des informations dont wordpress a besoin pour identifier le type de fichier uploadé
            $imageType =  wp_check_filetype( $imageDestination, null);

            // préparation des informations nécessaires pour créer le media
            $attachment = array(
                'post_mime_type' => $imageType['type'],
                'post_title' => $imageName,
                'post_content' => '',
                'post_status' => 'inherit'
            );

            // on enregistre l'image dans wordpress
            $attachmentId = wp_insert_attachment( $attachment, $imageDestination );

            if(is_int($attachmentId)) {
                // youpi merci wordpress...
                require_once( ABSPATH . 'wp-admin/includes/image.php' );

                // DOC on génère les metadatas pour le média https://developer.wordpress.org/reference/functions/wp_generate_attachment_metadata/
                $metadata = wp_generate_attachment_metadata( $attachmentId, $imageDestination );

                // on met à jour les metadata du media
                wp_update_attachment_metadata( $attachmentId, $metadata );

                return [
                    'status' => 'success',
                    'image' => [
                        'url' => $destination['url'] . '/' . $imageName,
                        'id' => $attachmentId
                    ]
                ];
            }
            else {
                return [
                    'status' => 'failed'
                ];
            }
        }

        return [
            'status' => 'failed'
        ];
    }


    // ==================================================================================================
    // ==================================================================================================

    public function createPost($postType, $data)
    {

        $defaultData = [];
        $defaultData['post_author'] = get_current_user_id();
        $defaultData['post_type'] = $postType;
        $defaultData['post_status'] = 'publish';
        $defaultData['post_content'] = '';
        $defaultData['post_title'] = 'Untitled';
        $defaultData['post_excerpt'] = '';

        $data = array_merge($defaultData, $data);

        $post = new Post();
        $post->setValues($data);
        $post->save();

        // $postId = wp_insert_post($wordpressData);
        if($post->getId()) {
            return $post;
            // return get_post($postId);
        }
        else {
            throw new Exception('Post creation failed');
        }
    }

    public function updatePost($postId, $data)
    {
        $post = new Post();
        $post->loadById($postId);
        $post->setValues($data);

        $post->save();

        return $post;

    }

    // ==================================================================================================
    // ==================================================================================================


    public function getPostData()
    {
        // récupération des données JSON envoyées en POST
        $json = file_get_contents('php://input');

        if($json) {
            // nous décodons le json en un tableau PHP classqque
            // DOC décoder du json en PHP https://www.php.net/json_decode
            $data = json_decode($json, true);
            // nous retournons le tableau
            return $data;
        }
        else {
            return $_POST;
        }
    }

    // IMPORTANT nonce rest call validation
    protected function getUserIdFromNonce(WP_REST_Request $request)
    {
        // NOTICE check if _wpnonce was sent in POST/GET paramter
        // $check = check_ajax_referer( 'wp_rest', '_wpnonce', false );

        $nonce = $request->get_header('X-WP-Nonce');
        wp_verify_nonce($nonce, 'wp_rest');
        $currentUserId = get_current_user_id();
        return $currentUserId;
    }

    /*
    public function recipeCreate()
    {
        // DOC créer un post avec wp https://developer.wordpress.org/reference/functions/wp_insert_post/


        if(is_int($postId)) {
            // engistrement de la durée de préparation dans une custom meta
            add_post_meta($postId, 'duration', $data['duration']);
            add_post_meta($postId, 'difficulty', $data['difficulty']);

            // And finally assign featured image to post
            if($data['imageId']) {
                set_post_thumbnail( $postId, $data['imageId']);
            }
        }
        $post = get_post($postId);
        return $post;
    }
    */


    /*
    public function getRecipesByDifficulty()
    {
        $difficulty = filter_input(INPUT_GET, 'difficulty');
        $difficulty = (int) $difficulty;

        // DOC custom query https://developer.wordpress.org/reference/classes/wp_query/
        $query = new WP_Query([
            'post_type' => 'recipe',
            'meta_key' => 'difficulty',
            'meta_value' => $difficulty
        ]);

        $responseData = [];
        $posts = $query->get_posts();

        foreach($posts as $post) {
            $author =  get_user_by('ID', $post->post_author);
            $featuredImage = get_the_post_thumbnail_url($post->ID, 'full');

            $responseData[] = [
                'post' => $post,
                'author' => $author,
                'featuredImage' => $featuredImage,
            ];
        }

        return $responseData;
    }
    */



    /*
    public function getRecipesByTaxonomies()
    {

        $taxonomies = $_GET['taxonomies'];

        // DOC custom query https://developer.wordpress.org/reference/classes/wp_query/
        $query = new WP_Query([
            'post_type' => 'recipe',

            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'ingredient',
                    'field'    => 'term_id',
                    'terms'    => $taxonomies,
                ),
                array(
                    'taxonomy' => 'recipe-type',
                    'field'    => 'term_id',
                    'terms'    => $taxonomies,
                ),
            ),
        ]);

        $posts = $query->get_posts();
        return $posts;
    }
    */


    /*
    public function signup()
    {

        $data = $this->getPostData();
        $errors = [];

        // controle de la validité des données envoyées
        $username = $data['username'];
        if(!$username) {
            $errors['username'] = ['Username is mandatory'];
        }

        $password = $data['password'];
        if(!$password) {
            $errors['password'] = ['Password is mandatory'];
        }

        $email = $data['email'];
        if(!$email) {
            $errors['email'] = ['Email is mandatory'];
        }


        // si pas d'erreur ; on tente de créer un nouveau user
        if(empty($errors)) {
            // DOC création d'un utilisateur https://developer.wordpress.org/reference/functions/wp_create_user/
            $success = wp_create_user($username, $password, $email);

            // si pas d'erreur de création du coté wp, on retourne l'id du user
            if(is_int($success)) {
                return [
                    'status' => 'success',
                    'data' => [
                        'userId' => $success,
                        'username' => $username,
                        'email' => $email,
                    ]
                ];
            }
            // sinon on retourne les erreurs de wp
            else {
                return [
                    'status' => 'failed',
                    'errors' => $success->errors
                ];
            }
        }
        // on retoure les erreurs
        else {
            return [
                'status' => 'failed',
                'errors' => $errors
            ];
        }
    }
    */

    /*

    */
}
