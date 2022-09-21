# Miniblog

Miniblog is a minimal blogging system, written in object-oriented PHP, aimed primarily at developers.

As a developer, you mightn't mind getting a little oily; however, you do still want a certain level of convenience.  You don't want to faff with HTML every time you write an article, but you don't need a fancy admin UI.  You do want a website that looks half decent, but the focus should be the content.

Miniblog couldn't be simpler&mdash;if you think it could be then please do get involved :slightly_smiling_face:  Installation is quick, with very little configuration.  Articles are written in Markdown with a sprinkling of JSON; pop those files in the content directory in your project and you're done!

## Installation

### Requirements

- Linux
- Apache / LiteSpeed / OpenLiteSpeed with `mod_rewrite`
- PHP 7.4+
- [Composer](https://getcomposer.org/)

### Method

Follow these instructions to create a Miniblog-powered blog that can be version-controlled and customised.

1. Assuming Composer is installed globally, run:\
`composer create-project miniblog/blog-project <target-directory>`\
Replace `<target-directory>` with the name of the directory you want to create.
1. Update the few values in `config.php`.
1. Make `public/` the document root of your website.

You should now see the Miniblog homepage when you navigate to the root of your website.  You can safely remove `installer/` if you wish.  Either way, the directory you just created can be version-controlled in its entirety.

## Read More

- [Content](doc/content.md)
- @todo Customisation
- [About](doc/about.md)
- @todo Design Decisions
- @todo Contributing
