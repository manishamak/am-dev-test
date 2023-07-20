# Overview
This is a simple plugin that retrieves data from a remote API endpoint, and makes that data accessible/retrievable from an API endpoint on the WordPress site this plugin is installed on. The data will be displayed via a custom block and on an admin WordPress page as described. A simple WP CLI command is also required.

# Specifications

* Using the GET HTTP Method accessible endpoint https://miusage.com/v1/challenge/1/ (there are no parameters to/from required), created an AJAX endpoint in WordPress that calls the above listed API endpoint to get the data return. The AJAX endpoint is usable whether the user is logged in or not (authentication of the AJAX endpoint is not required). The endpoint is always returning the data when called, but regardless of when/how many times the AJAX endpoint is called, it is never requesting the data from the miusage.com endpoint more than 1 time per hour.
* Created a custom (Gutenberg) block, that when loaded uses Javascript to contact the AJAX endpoint and display the data returned formatted into a table-like display. The block is having custom controls in the block settings to toggle the visibility of the table columns.
* Created a WP CLI command that is used to force the refresh  (override the 1 time per hour limit described above) of this data the next time the AJAX endpoint is called.
* Created a WordPress admin page which displays this data in the style of the admin page of the WordPress plugin WP Mail SMTP and added a button to refresh the data.