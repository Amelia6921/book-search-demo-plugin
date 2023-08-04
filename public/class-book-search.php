<?php

class Book_Search_Public
{

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     *  Initializes the plugin version by checking if the
     */    
    public function __construct()
    {
        if (defined('BOOK_SEARCH_PLUGIN_VERSION')) {
            $this->version = BOOK_SEARCH_PLUGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
    }

    /**
     * Enqueues the necessary CSS stylesheets for the plugin on the front-end.
     */
    public function enqueue_styles()
    {
        wp_enqueue_style('jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
        wp_enqueue_style('jquery-datatables', plugin_dir_url(__FILE__) . 'css/jquery.dataTables.min.css', array(), $this->version, 'all');
        wp_enqueue_style('book-search-public', plugin_dir_url(__FILE__) . 'css/book-search-public.css', array(), $this->version, 'all');
    }

    /**
     * Enqueues the necessary JavaScript files for the plugin on the front-end, including a localized script to make AJAX requests
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('jquery-ui', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('jquery-datatables', plugin_dir_url(__FILE__) . 'js/jquery.dataTables.min.js', array('jquery'), $this->version, true);
        wp_register_script('book-search-public', plugin_dir_url(__FILE__) . 'js/book-search-public.js', array('jquery'), $this->version, true);
        wp_localize_script('book-search-public', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);
        wp_enqueue_script('book-search-public');
    }

    /**
     * This is a callback function that is hooked to a shortcode to generate the Book Search form and display search results. 
     * The method uses the get_terms() function to retrieve a list of publishers from the taxonomy called publisher. 
     * The method then generates an HTML form that includes input fields for the book name, author, publisher, rating, and price range. 
     * The method also generates a search button to submit the form. After the form is submitted, the method retrieves all published books using WP_Query, and displays them in a table on the page. 
     * The search query is built based on the form inputs submitted by the user.
     */
    public function book_search_callback()
    {

        $publisher_taxonomies = get_terms(
            array(
                'taxonomy' => 'publisher',
                'hide_empty' => false,
            )
        );

        ob_start();
        ?>
        <h3 class="book-search-title">
            <?php _e('Book Search'); ?>
        </h3>
        <form class="book-search-form" action="#" method="post">
            <div class="book-search-col-6">
                <label for="book-name">
                    <?php _e('Book Name'); ?>
                </label>
                <input type="text" id="book-name" value="John Doe" name="book-name" required>
            </div>
            <div class="book-search-col-6">
                <label for="author">
                    <?php _e('Author'); ?>
                </label>
                <input type="text" id="author" name="author">
            </div>
            <div class="book-search-col-6">
                <label for="publisher">
                    <?php _e('Publisher'); ?>
                </label>
                <select id="publisher" name="publisher">
                    <option value="">
                        <?php _e('-- Select Publisher --'); ?>
                    </option>
                    <?php

                    foreach ($publisher_taxonomies as $publisher_taxonomie) {
                        echo "<option value='" . $publisher_taxonomie->term_id . "'>" . $publisher_taxonomie->name . "</option>";
                    }

                    ?>
                </select>
            </div>
            <div class="book-search-col-6">
                <label for="rating">
                    <?php _e('Rating'); ?>
                </label>
                <select id="rating" name="rating">
                    <option value="">
                        <?php _e('-- Select Rating --'); ?>
                    </option>
                    <option value="1">
                        <?php _e('1 star'); ?>
                    </option>
                    <option value="2">
                        <?php _e('2 stars'); ?>
                    </option>
                    <option value="3">
                        <?php _e('3 stars'); ?>
                    </option>
                    <option value="4">
                        <?php _e('4 stars'); ?>
                    </option>
                    <option value="5">
                        <?php _e('5 stars'); ?>
                    </option>
                </select>
            </div>
            <div class="book-search-col-6">

                <label for="priceRange">
                    <?php _e('Price Range:'); ?>
                </label>
                <div class="priceRange_slider">
                    <input type="text" id="priceRange" readonly>
                    <div id="price-range" class="slider"></div>
                    <input type="hidden" name="priceRange_min" class="priceRange_min" />
                    <input type="hidden" name="priceRange_max" class="priceRange_max" />
                </div>

            </div>
            <div class="book-search-col-12">
                <div class="submit_container">
                    <input type="hidden" class="search_nonce" name="search_nonce"
                        value="<?php echo wp_create_nonce('search_nonce'); ?>">
                    <button type="submit" class="book-search submit-btn">
                        <?php _e('Search'); ?>
                    </button>
                </div>
            </div>

        </form>

        <hr>

        <?php

        $book_args = array(
            'post_type' => 'book',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $books_loop = new WP_Query($book_args);

        ?>

        <div class="book-search-result-area">
            <table class="book-search-table">
                <thead>
                    <tr>
                        <th>
                            <?php _e('No'); ?>
                        </th>
                        <th>
                            <?php _e('Book Name'); ?>
                        </th>
                        <th>
                            <?php _e('Price'); ?>
                        </th>
                        <th>
                            <?php _e('Author'); ?>
                        </th>
                        <th>
                            <?php _e('Publisher'); ?>
                        </th>
                        <th>
                            <?php _e('Rating'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($books_loop->have_posts()) {
                        $index_counter = 1;
                        while ($books_loop->have_posts()) {
                            $books_loop->the_post();
                            get_the_ID();

                            echo "<tr>";
                            echo "<td>" . $index_counter . "</td>";
                            echo "<td> <a href='" . get_permalink(get_the_ID()) . "' > " . get_the_title() . "</a> </td>";
                            echo "<td>" . get_post_meta(get_the_ID(), '_book_price', true) . "</td>";
                            echo "<td>";
                            $book_search_authors = wp_get_post_terms(get_the_ID(), 'author', array('fields' => 'names'));
                            if (!empty($book_search_authors)) {
                                echo esc_html(implode(', ', $book_search_authors));
                            }
                            echo "</td>";
                            echo "<td>";
                            $book_search_publishers = wp_get_post_terms(get_the_ID(), 'publisher', array('fields' => 'names'));
                            if (!empty($book_search_publishers)) {
                                echo esc_html(implode(', ', $book_search_publishers));
                            }
                            echo "</td>";
                            echo "<td>";

                            $book_search_star_rate = get_post_meta(get_the_ID(), '_book_rating', true);                            
                            echo str_repeat("&#9733;", $book_search_star_rate);
                            echo str_repeat("&#9734;", 5 - $book_search_star_rate);
                            echo "</td>";
                            echo "</tr>";

                            $index_counter++;
                        }
                    }
                    ?>

                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    public function get_book_data_callback()
    {
        if (isset($_REQUEST['book_name']) && !empty($_REQUEST['book_name']) && isset($_REQUEST['search_nonce']) && wp_verify_nonce($_REQUEST['search_nonce'], 'search_nonce')) {
            
            $book_name = isset($_REQUEST['book_name']) ? sanitize_text_field($_REQUEST['book_name']) : '';
            $author = isset($_REQUEST['author']) ? sanitize_text_field($_REQUEST['author']) : '';
            $publisher = isset($_REQUEST['publisher']) ? absint($_REQUEST['publisher']) : '';
            $rating = isset($_REQUEST['rating']) ? sanitize_text_field($_REQUEST['rating']) : '';
            $price_start = isset($_REQUEST['price_start']) ? floatval($_REQUEST['price_start']) : '';
            $price_end = isset($_REQUEST['price_end']) ? floatval($_REQUEST['price_end']) : '';

            $book_search_args = array(
                'post_type' => 'book',
                'posts_per_page' => -1,
                's' => $book_name,
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND'
                ),
                'tax_query' => array(
                    'relation' => 'AND'
                )
            );

            if ($author != "") {
                $book_search_args['tax_query'][] = array(
                    'taxonomy' => 'author',
                    'terms' => $author,
                    'field' => 'name'
                );

            }

            if ($publisher != 0) {
                $book_search_args['tax_query'][] = array(
                    'taxonomy' => 'publisher',
                    'terms' => $publisher,
                    'field' => 'id'
                );
            }

            if ($rating != "") {
                $book_search_args['meta_query'][] = array(
                    array(
                        'key' => '_book_rating',
                        'value' => $rating,
                        'compare' => '=',
                    )
                );
            }

            if ($price_start != "" && $price_end != "") {
                $book_search_args['meta_query'][] = array(
                    'key' => '_book_price',
                    'value' => array($price_start, $price_end),
                    'type' => 'numeric',
                    'compare' => 'between'
                );

            }

            $book_search_result = new WP_Query($book_search_args);

            if ($book_search_result->have_posts()) {
                $item_counter = 1;
                ?>
                <table class="book-search-table">
                    <thead>
                        <tr>
                            <th>
                                <?php _e('No'); ?>
                            </th>
                            <th>
                                <?php _e('Book Name'); ?>
                            </th>
                            <th>
                                <?php _e('Price'); ?>
                            </th>
                            <th>
                                <?php _e('Author'); ?>
                            </th>
                            <th>
                                <?php _e('Publisher'); ?>
                            </th>
                            <th>
                                <?php _e('Rating'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($book_search_result->have_posts()) {
                            $book_search_result->the_post();
                            
                            echo "<tr>";
                            echo "<td>" . $item_counter . "</td>";
                            echo "<td> <a href='" . get_permalink(get_the_ID()) . "' > " . get_the_title() . "</a> </td>";
                            echo "<td>" . get_post_meta(get_the_ID(), '_book_price', true) . "</td>";
                            echo "<td>";
                            $book_search_authors = wp_get_post_terms(get_the_ID(), 'author', array('fields' => 'names'));
                            if (!empty($book_search_authors)) {
                                echo esc_html(implode(', ', $book_search_authors));
                            }
                            echo "</td>";
                            echo "<td>";
                            $book_search_publishers = wp_get_post_terms(get_the_ID(), 'publisher', array('fields' => 'names'));
                            if (!empty($book_search_publishers)) {
                                echo esc_html(implode(', ', $book_search_publishers));
                            }
                            echo "</td>";
                            echo "<td>";
                                                        
                            $book_search_star_rate = get_post_meta(get_the_ID(), '_book_rating', true);                            
                            echo str_repeat("&#9733;", $book_search_star_rate);
                            echo str_repeat("&#9734;", 5 - $book_search_star_rate);
                            echo "</td>";
                            echo "</tr>";

                            $item_counter++;
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
        } else {
            echo "Please fill out the required fields and ensure that your nonce is valid.";
        }


        die();
    }


}