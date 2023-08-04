## Book Search 
Contributors: Ramiz Theba
Tags: books, library, book search
Requires at least: 5.5
Tested up to: 6.1.1
Requires PHP: 7.4 or Higher

This plugin creates a book search functionality for a library website. The plugin allows users to search for books based on book name, author, publisher, price range, and book rating. On activation, the plugin registers a custom post type of Book, and two taxonomies of Author and Publisher.

## Description
This plugin creates a book search functionality for a library website. The plugin allows users to search for books based on book name, author, publisher, price range, and book rating. On activation, the plugin registers a custom post type of Book, and two taxonomies of Author and Publisher.

To display the book search tool on the front-end, you can either use a shortcode or a Gutenberg block. To use the shortcode, add [book_search] to any page or post. 

Once the search tool is displayed, users can enter their search criteria and click on the \'Search\' button. The plugin will display a list of books that match the search criteria. Each book name will contain a link to its detail page.

You can add books to the library by going to the WordPress admin area and navigating to \'Books\' > \'Add New\'. You can add 20-25 manually added records and assign author, publisher, add price, and rating in custom fields. The following fields are available for each book:

* Book Title
* Short Description
* Author (taxonomy)
* Publisher (taxonomy)
* Price (custom field)
* Star Rating 1 to 5 (custom field)

When a user clicks on a book title link, they will be taken to the book\'s detail page. On this page, all the fields will be displayed, including the book name, author (linked to author archive page), publisher (linked to publisher archive page), description, rating, and price.


## Installation 
1. Upload the plugin files to the /wp-content/plugins/book-search directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress.

## Screenshots
1. Screenshot_1.jpg

## Changelog

* 1.0.0

    * Initial Release