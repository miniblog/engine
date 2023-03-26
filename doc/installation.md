# Installation

## Requirements

- Linux
- Apache / LiteSpeed / OpenLiteSpeed with `mod_rewrite`
- PHP 7.4+
- Composer

> :information_source: If you're looking for a Web host based in the UK, we can highly recommend [Eco Hosting](https://www.ecohosting.co.uk/).  Not only are they carbon-neutral and powered by sustainable energy, but also their support is first-rate.  Please let them know we recommended them.

## Quick Start

Follow these instructions to quickly get a Miniblog-powered website up and running:

1. Assuming Composer is installed globally, run:\
`composer create-project miniblog/blog-project <target-directory>`\
Replace `<target-directory>` with the name of the directory you want to create.
1. Make `public/` the document root of your website.

You should now see the Miniblog homepage when you navigate to the root of your website.

> :information_source: After successfully installing Miniblog, you can safely remove `installer/` if you wish.  Either way, the directory you just created can be version-controlled in its entirety.

## Basic Customisation

In Miniblog there is no configuration as such: everything is content.  Thus, to begin customising the website, you will need to update the Document containing the website metadata, and the Document containing details of the owner/principal-author of the content.  If you want to keep the about-page, you'll need to update the Document containing its content.

1. Update website metadata in `data/Thing/CreativeWork/WebSite/this.md`.
1. Update the details of the website's owner/principal-author in `data/Thing/Person/owner.md`.
1. Update/remove `data/Thing/CreativeWork/Article/about-this-website.md`, which contains the content for the about-page.
1. At the root of the project, run `bin/console refresh`.

> :warning: Always run `bin/console refresh` after updating content.

Now you've been rooting around in `data/`, it's likely you'll understand enough to just get on with adding/editing blog posts.  You can always come back and read the [gory details of Miniblog's content model](content.md) if you really need to 🙂
