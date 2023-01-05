# Miniblog

[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://stand-with-ukraine.pp.ua)

Miniblog is a minimal blogging platform, written in object-oriented PHP, aimed primarily at developers.  There are no plugins or themes, and there is no complicated setup&mdash;take a look at [the installation instructions](#method) to see what we mean.  The focus is blogging.

As a developer, you mightn't mind getting a little oily; however, you do still want a certain level of convenience.  You don't want to faff with HTML every time you write an article, but you don't need a fancy admin UI.  You do want a website that looks half decent, but the focus should be the content.

Miniblog couldn't be simpler&mdash;if you think it could be then please do get involved :wink:  Installation is quick, with very little configuration.  Articles are written in Markdown with a sprinkling of JSON; pop those files in the content directory in your project and you're done.

> :information_source: If you're coming from [Dead Simple Blog (DSB)](https://github.com/paintedsky/dead-simple-blog) then you may like to read about [the fundamental differences between Miniblog and DSB](doc/miniblog-vs-dsb.md).

## Installation

### Requirements

- Linux
- Apache / LiteSpeed / OpenLiteSpeed with `mod_rewrite`
- PHP 7.4+
- Composer

### Method

Follow these instructions to create a Miniblog-powered blog that can be version-controlled and customised.

1. Assuming Composer is installed globally, run:\
`composer create-project miniblog/blog-project <target-directory>`\
Replace `<target-directory>` with the name of the directory you want to create.
1. At the root of the project you just created, update the few values in `config.php` and then run `bin/console refresh-content`.
1. Make `public/` the document root of your website.

> :warning: Always run `bin/console refresh-content` after updating `config.php`.

You should now see the Miniblog homepage when you navigate to the root of your website.  You can safely remove `installer/` if you wish.  Either way, the directory you just created can be version-controlled in its entirety.

## Read More

- [Content](doc/content.md)
- Background
    - [Miniblog: An Ongoing Exercise in Responsible Software Development](https://justathought.dev/blog/miniblog-an-ongoing-exercise-in-responsible-software-development)
    - [The Future Is Minimal](https://justathought.dev/blog/the-future-is-minimal)
    - [Miniblog Guiding Principles](https://justathought.dev/blog/miniblog-guiding-principles)
- [Design Decisions](doc/design-decisions.md)
