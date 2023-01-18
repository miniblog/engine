# Installation

## Requirements

- Linux
- Apache / LiteSpeed / OpenLiteSpeed with `mod_rewrite`
- PHP 7.4+
- Composer

> :information_source: If you're looking for a Web host, we can highly recommend [Eco Hosting](https://www.ecohosting.co.uk/).  Not only are they carbon-neutral and powered by sustainable energy, but also their support is first-rate.  Please let them know we recommended them.

## Method

Follow these instructions to create a Miniblog-powered blog that can be version-controlled and customised.

1. Assuming Composer is installed globally, run:\
`composer create-project miniblog/blog-project <target-directory>`\
Replace `<target-directory>` with the name of the directory you want to create.
1. At the root of the project you just created, update the few values in `config.php` and then run `bin/console refresh-content`.
1. Make `public/` the document root of your website.

> :warning: Always run `bin/console refresh-content` after updating `config.php`.

You should now see the Miniblog homepage when you navigate to the root of your website.  You can safely remove `installer/` if you wish.  Either way, the directory you just created can be version-controlled in its entirety.
