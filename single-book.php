<?php

get_header();

if (have_posts()):
    while (have_posts()):
        the_post();

        ?>

        <div class="book_search__cotainer">

            <div class="book_search__row">

                <h1>
                    <?php the_title(); ?>
                </h1>

                <p class="book_search__description">
                    <?php the_content(); ?>
                </p>

                <div class="book_search__metadata">
                    <div class="price_value">
                        <?php $book_price = get_post_meta(get_the_ID(), '_book_price', true);
                        if (!empty($book_price)) {
                            echo "Price: " . esc_html($book_price);
                        } ?>
                    </div>
                    <div class="book_rating">
                        <?php
                        $book_search_star_rate = get_post_meta(get_the_ID(), '_book_rating', true);
                        echo "Rating: ";
                        echo str_repeat("&#9733;", $book_search_star_rate);
                        echo str_repeat("&#9734;", 5 - $book_search_star_rate);
                        ?>
                    </div>
                    <div class="auther_name">
                        <?php
                        $book_search_authors = wp_get_post_terms(get_the_ID(), 'author', array('fields' => 'names'));
                        if (!empty($book_search_authors)) {
                            echo "Author: " . esc_html(implode(', ', $book_search_authors));
                        }
                        ?>
                    </div>
                    <div class="publisher_name">
                        <?php
                        $book_search_publishers = wp_get_post_terms(get_the_ID(), 'publisher', array('fields' => 'names'));
                        if (!empty($book_search_publishers)) {
                            echo "Publisher: " . esc_html(implode(', ', $book_search_publishers));
                        }
                        ?>
                    </div>
                </div>


            </div>

        </div>

        <?php

    endwhile;
endif;


get_footer();