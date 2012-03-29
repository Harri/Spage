Spage
=

Simple flat file CMS written in PHP. Each page gets its own `*.html` file which is served for users and `*.txt` file is kept for maintenance purposes. 

* Create new content by using Markdown
    * Live preview is shown while creating new pages using Showdown.js
* Templates are done using Mustache.php
* Individual pages can be edited and deleted
    * Deleted pages are just moved to `trash` directory.
* All pages can be rebuilt if there is a need to change the markup

Configuration and usage
-
There is no need to do any configuration, but protecting `spage.php` and trash directory using `.htaccess` or similar means is recommended.

Editing `templates.php` and `style.css` is also something you might want to do.

All pages are created to the same directory where `spage.php` is, so all pages must have unique name.

Licensing
-
Spage is distributed under MIT license. Showdown, Mustache and Markdown have their own licenses (can be found in those files).