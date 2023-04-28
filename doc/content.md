# Content

Practically all content that appears on a Miniblog website comes from "Documents", text files comprising at least some [JSON](https://en.wikipedia.org/wiki/JSON) and, in many cases, a 'body' of [GitHub-Flavoured Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax)-formatted text.  Documents have the extension `.md` and are stored under `data/`.

For convenience, Miniblog's content model largely follows the [Schema.org ontology](https://schema.org/docs/full.html).

As an example of how this works, let's take a look at how to begin customising a Miniblog website.  Here we're talking about changing things like the website's title and information about the owner/principal-author of the content.

This information describes two discrete things: a website; and a person.  In the Schema.org hierarchy, the 'path' of the ["WebSite" type](https://schema.org/WebSite) is `Thing/CreativeWork/WebSite`.  Hence, the Miniblog Document detailing the Miniblog website is stored in `data/Thing/CreativeWork/WebSite/`; the Document, itself, has the ID `this-website`.  As for the person responsible for the website's content: the path of the ["Person" Schema.org type](https://schema.org/Person) is `Thing/Person`, and the Document is `data/Thing/Person/owner-of-this-website.md`.

Following this logic, you can understand that blog posts, of the [type "BlogPosting"](https://schema.org/BlogPosting), are stored in `data/Thing/CreativeWork/Article/SocialMediaPosting/BlogPosting/`.

[Next: Document Spec &rarr;](content/document-spec.md)
