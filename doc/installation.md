# Installation

## Requirements

- Linux
- Apache / LiteSpeed / OpenLiteSpeed with `mod_rewrite`
- PHP 7.4+
- [Composer](https://getcomposer.org/)

## Instructions

Follow these instructions to create a Miniblog-powered blog that can be version-controlled and customised.

1. Assuming Composer is installed globally, run:\
`composer create-project miniblog/blog-project <target-directory>`\
Replace `<target-directory>` with the name of the directory you want to create.
1. Update the few values in `config.php`.
1. Make `public/` the document root of your website.

You should now see the Miniblog homepage when you navigate to the root of your website.  You can safely remove `installer/` if you wish.  Either way, the directory you just created can be version-controlled in its entirety.
