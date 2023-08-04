jQuery(document).ready(function () {
  book_sear_chdatatable = jQuery(".book-search-table").DataTable();

  jQuery("#price-range").slider({
    step: 100,
    range: true,
    min: 0,
    max: 3000,
    values: [0, 3000],
    slide: function (event, ui) {
      jQuery("#priceRange").val(ui.values[0] + " - " + ui.values[1]);
      jQuery(".priceRange_min").val(ui.values[0]);
      jQuery(".priceRange_max").val(ui.values[1]);
    },
  });
  jQuery(".priceRange_min").val(jQuery("#price-range").slider("values", 0));
  jQuery(".priceRange_max").val(jQuery("#price-range").slider("values", 1));
  jQuery("#priceRange").val(
    jQuery("#price-range").slider("values", 0) +
      " - " +
      jQuery("#price-range").slider("values", 1)
  );

  jQuery(".book-search-form").on("submit", function () {
    jQuery(".book-search-form .book-search.submit-btn").text("Searching...");
    jQuery(".book-search-form .book-search.submit-btn").prop("disabled", true);

    book_name = jQuery(".book-search-form #book-name").val();
    author = jQuery(".book-search-form #author").val();
    publisher = jQuery(".book-search-form #publisher").val();
    rating = jQuery(".book-search-form #rating").val();
    search_nonce = jQuery(".book-search-form .search_nonce").val();

    price_start = jQuery(".priceRange_min").val();
    price_end = jQuery(".priceRange_max").val();

    jQuery.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: {
        action: "get_book_data_ajax_call",
        book_name: book_name,
        author: author,
        publisher: publisher,
        rating: rating,
        price_start: price_start,
        price_end: price_end,
        search_nonce: search_nonce,
      },
      success: function (response) {
        jQuery(".book-search-result-area .book-search-table").html(response);
        jQuery(".book-search-form .book-search.submit-btn").text("Search");
        jQuery(".book-search-form .book-search.submit-btn").prop(
          "disabled",
          false
        );
        jQuery(".book-search-result-area .book-search-table")
          .DataTable()
          .destroy();
        jQuery(".book-search-result-area .book-search-table").DataTable({
          paging: true,
          searching: true,
          ordering: true,
          info: true,
        });
      },
      error: function (jqXHR, exception) {
        var msg = "";
        if (jqXHR.status === 0) {
          msg = "Not connect.\n Verify Network.";
        } else if (jqXHR.status == 404) {
          msg = "Requested page not found. [404]";
        } else if (jqXHR.status == 500) {
          msg = "Internal Server Error [500].";
        } else if (exception === "parsererror") {
          msg = "Requested JSON parse failed.";
        } else if (exception === "timeout") {
          msg = "Time out error.";
        } else if (exception === "abort") {
          msg = "Ajax request aborted.";
        } else {
          msg = "Uncaught Error.\n" + jqXHR.responseText;
        }
        alert(" Opps!...  " + msg);
        jQuery(".book-search-form .book-search.submit-btn").text("Search");
        jQuery(".book-search-form .book-search.submit-btn").prop(
          "disabled",
          false
        );
      },
    });

    return false;
  });
});
