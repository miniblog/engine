# Miniblog vs Dead Simple Blog

[Dead Simple Blog (DSB)](https://github.com/paintedsky/dead-simple-blog), by [@paintedsky](https://github.com/paintedsky), is a great example of a minimal app and is also written in PHP.  It provided some of the inspiration for Miniblog; indeed, the Miniblog project started out as a fork of DSB.  Dead Simple Blog may be better suited to you, so be sure to take a look if you're at all interested.

These are the fundamental differences between Miniblog and DSB:

- Miniblog is arranged to allow you to easily version-control just your content.

- Miniblog supports essential front-matter in Markdown files.  This allows the app to create more meaningful markup for the benefit of search engines&mdash;and thus users.  It also gives increased flexibility when working with templates.  Front-matter is written in JSON, which is supported natively by PHP.

- Templates can be overridden.

- Unlike DSB, Miniblog requires Composer to manage its dependencies.  However, in production, Miniblog uses *only one* more library than DSB&mdash;and a small one at that.  From my perspective, Composer is an invaluable development tool, and since Composer is also ubiquitous in hosting environments nowadays, it isn't hard to justify requiring it.

- I prefer user-friendly URLs, so Miniblog requires URL-rewriting in the web server.  URL-rewriting is ubiquitous in hosting environments.

- Miniblog is written in object-oriented PHP and is unit-tested.  It was designed to be basic, but robust and extensible.  As time goes by, I will continue to prune and simplify the code, to keep things as basic as possible.
