<?php

/** 
 *
 * @link              https://ramiz-theba-portfolio.netlify.app/
 * @since             1.0.0
 * @package           Book_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Book Search
 * Plugin URI:        https://ramiz-theba-portfolio.netlify.app/
 * Description:       A plugin for library book search
 * Version:           1.0.0
 * Author:            Ramiz Theba
 * Author URI:        https://ramiz-theba-portfolio.netlify.app/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       book-search
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/**
 * Currently plugin version. 
 */
define('BOOK_SEARCH_VERSION', '1.0.0');

//Define Dirpath for hooks
define('DIR_PATH', plugin_dir_path(__FILE__));

if (!class_exists('BookSearch')) {
  class BookSearch
  {

    protected $loader;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->setup_actions();
      $this->load_dependencies();
      $this->define_public_hooks();

      if (defined('BOOK_SEARCH_PLUGIN_VERSION')) {
        $this->version = BOOK_SEARCH_PLUGIN_VERSION;
      } else {
        $this->version = '1.0.0';
      }
    }

    public function load_dependencies()
    {
      /**
       * The class responsible for defining all actions that occur in the public-facing
       * side of the site.
       */
      require_once plugin_dir_path(__FILE__) . 'public/class-book-search.php';

    }


    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

      $plugin_public = new Book_Search_Public();
      add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_styles'));
      add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_scripts'));
      add_shortcode('book_search', array($plugin_public, 'book_search_callback'));
      add_action('wp_ajax_get_book_data_ajax_call', array($plugin_public, 'get_book_data_callback'));
      add_action('wp_ajax_nopriv_get_book_data_ajax_call', array($plugin_public, 'get_book_data_callback'));

      add_filter('template_include', array($this, 'book_search_single_template'));

    }

    /**
     * The function book_search_single_template is a useful tool for setting up a template for displaying single book posts on a website.
     */
    public function book_search_single_template($template)
    {
      if (is_singular('book')) {
        $template = plugin_dir_path(__FILE__) . 'single-book.php';
      }
      return $template;
    }


    /**
     * Setting up Hooks
     */
    public function setup_actions()
    {
      // Main plugin hooks
      register_activation_hook(DIR_PATH, array('BookSearch', 'activate'));
      register_deactivation_hook(DIR_PATH, array('BookSearch', 'deactivate'));

      add_action('init', array($this, 'book_search_register_custom_post_type'));
      add_action('init', array($this, 'book_search_register_custom_taxonomy'));
      add_action('add_meta_boxes', array($this, 'book_search_register_metabox_book_meta'));
      add_action('save_post', array($this, 'save_book_meta_box'));

    }

    /**
     * Activate callback
     */
    public static function activate()
    {
      //Activation code in here

    }

    /**
     * Deactivate callback
     */
    public static function deactivate()
    {
      //Deactivation code in here
    }

    public function book_search_register_custom_post_type()
    {
      $labels = array(
        'name' => __('Books', 'book-search'),
        'singular_name' => __('Book', 'book-search'),
        'add_new' => __('Add New', 'book-search'),
        'add_new_item' => __('Add New Book', 'book-search'),
        'edit_item' => __('Edit Book', 'book-search'),
        'new_item' => __('New Book', 'book-search'),
        'view_item' => __('View Book', 'book-search'),
        'search_items' => __('Search Books', 'book-search'),
        'not_found' => __('No books found', 'book-search'),
        'not_found_in_trash' => __('No books found in Trash', 'book-search'),
        'parent_item_colon' => __('Parent Book:', 'book-search'),
        'menu_name' => __('Books', 'book-search'),
      );

      $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'books'),
        'supports' => array('title', 'excerpt', 'custom-fields'),
      );

      register_post_type('book', $args);
    }

    public function book_search_register_custom_taxonomy()
    {
      $author_labels = array(
        'name' => _x('Authors', 'taxonomy general name', 'book-search'),
        'singular_name' => _x('Author', 'taxonomy singular name', 'book-search'),
        'search_items' => __('Search Authors', 'book-search'),
        'all_items' => __('All Authors', 'book-search'),
        'parent_item' => __('Parent Author', 'book-search'),
        'parent_item_colon' => __('Parent Author:', 'book-search'),
        'edit_item' => __('Edit Author', 'book-search'),
        'update_item' => __('Update Author', 'book-search'),
        'add_new_item' => __('Add New Author', 'book-search'),
        'new_item_name' => __('New Author Name', 'book-search'),
        'menu_name' => __('Authors', 'book-search'),
      );

      $author_args = array(
        'hierarchical' => true,
        'labels' => $author_labels,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'author'),
      );

      register_taxonomy('author', array('book'), $author_args);


      $publisher_labels = array(
        'name' => _x('Publishers', 'taxonomy general name', 'book-search'),
        'singular_name' => _x('Publisher', 'taxonomy singular name', 'book-search'),
        'search_items' => __('Search Publishers', 'book-search'),
        'all_items' => __('All Publishers', 'book-search'),
        'parent_item' => __('Parent Publisher', 'book-search'),
        'parent_item_colon' => __('Parent Publisher:', 'book-search'),
        'edit_item' => __('Edit Publisher', 'book-search'),
        'update_item' => __('Update Publisher', 'book-search'),
        'add_new_item' => __('Add New Publisher', 'book-search'),
        'new_item_name' => __('New Publisher Name', 'book-search'),
        'menu_name' => __('Publishers', 'book-search'),
      );

      $publisher_args = array(
        'hierarchical' => true,
        'labels' => $publisher_labels,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'publisher'),
      );

      register_taxonomy('publisher', array('book'), $publisher_args);
    }

    public function book_search_register_metabox_book_meta()
    {
      add_meta_box('books_meta_data_box', 'Books Data', array($this, 'render_book_meta_box'), 'book');
    }

    function render_book_meta_box($post)
    {
      wp_nonce_field('book_meta_box_nonce', 'book_meta_box_nonce');

      $price = get_post_meta($post->ID, '_book_price', true);
      $rating = get_post_meta($post->ID, '_book_rating', true);
      ?>
      <p>
        <label for="book_price">
          <?php _e('Price'); ?>
        </label>
        <input type="text" name="book_price" id="book_price" value="<?php echo esc_attr($price); ?>" class="widefat">
      </p>
      <p>
        <label for="book_rating">
          <?php _e('Rating'); ?>
        </label>
        <select name="book_rating" id="book_rating" class="widefat">
          <option value="1" <?php selected($rating, '1'); ?>><?php _e('1'); ?></option>
          <option value="2" <?php selected($rating, '2'); ?>><?php _e('2'); ?></option>
          <option value="3" <?php selected($rating, '3'); ?>><?php _e('3'); ?></option>
          <option value="4" <?php selected($rating, '4'); ?>><?php _e('4'); ?></option>
          <option value="5" <?php selected($rating, '5'); ?>><?php _e('5'); ?></option>
        </select>
      </p>
      <?php
    }

    function save_book_meta_box($post_id)
    {
      if (!isset($_POST['book_meta_box_nonce']) || !wp_verify_nonce($_POST['book_meta_box_nonce'], 'book_meta_box_nonce')) {
        return;
      }

      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
      }

      if (isset($_POST['post_type']) && 'book' == $_POST['post_type']) {
        if (current_user_can('edit_post', $post_id)) {
          $price = sanitize_text_field($_POST['book_price']);
          update_post_meta($post_id, '_book_price', $price);

          $rating = sanitize_text_field($_POST['book_rating']);
          update_post_meta($post_id, '_book_rating', $rating);
        }
      }
    }

  }

  // instantiate the plugin class
  $wp_plugin_template = new BookSearch();
}