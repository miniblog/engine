# Miniblog vs DSB

- @todo Miniblog is arranged to allow you to easily version-control your content.

- Miniblog supports essential front-matter in Markdown files.  This allows the app to create more meaningful markup for the benefit of search engines&mdash;and thus users.  @todo It also gives increased flexibility when customising templates.  Front-matter is written in JSON, which is supported natively by PHP.

- @todo Templates can be customised.

- Unlike DSB, Miniblog requires [Composer](https://getcomposer.org/) to manage its dependencies.  However, in production, Miniblog uses *only one* more library than DSB&mdash;and a small one at that.  From my perspective, Composer is an essential development tool, and since Composer is also ubiquitous in hosting environments nowadays, it is not hard to justify requiring it.

- Miniblog is written in object-oriented PHP and is fully unit-tested.
