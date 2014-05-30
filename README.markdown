Spage
=
Simple flat file CMS written in PHP. Each page gets its own `*.html` file which is served for users and `*.spage` file is kept for maintenance and search purposes. 

* Create and edit pages using Markdown
  * Live preview is shown while creating and editing pages using Showdown.js
* Save pages as drafts or unlisted
* Templates are done using Mustache.php
* RSS and Sitemap
* Search
* Comments
  * Moderation for admins

Configuration and usage
-
There is no need to do any configuration, but protecting `spage.php` and `trash` directory using `.htaccess` or similar means is recommended.

Editing `templates.php` and `style.css` is also something you might want to do.

All pages are created to the same directory where `spage.php` is, so all pages must have unique name.

Why flat files and not database?
-
Most websites are read often and updated rarely. So it just feels wrong when servers have to do any heavy work just to serve a page for users. If adding a new page eats more resources, who cares? That is not done often anyway, at least in most cases. Why should changing markup be the easiest thing, when that is done like once a year? I believe that letting web servers do what they are good at - serving web pages - is the right thing to do.

Also, taking backups of your whole site is so much easier with plain files. So is transferring to another host. No need to worry about configurations, application support, etc. And when you stop maintaining your site, just copy all files to your local hard drive and you get to save your precious writings.

Licensing
-
Spage is distributed under MIT license. Showdown, Mustache and Parsedown have their own licenses (can be found in those files).
