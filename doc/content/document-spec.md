# Content / Document Spec

A Miniblog Document embodies a single item of content, a ["Thing"](https://schema.org/Thing) in Schema.org parlance.  The type of the content is inferred from the location of the Document in the data filesystem.  For example, a Document in `data/Thing/CreativeWork/Article/SocialMediaPosting/BlogPosting/` is a blog post.

A Document is written in plain text, in a single file with the suffix `.md`.  The basename of the file (the part of the filename before the extension) is the 'ID' of the Document and *must* comprise only lowercase characters, digits, and dashes (`/^[a-z0-9-]+$/`).

A Document comprises at most two parts:

- front matter (information about the Thing);
- and the body (full textual content) of the Thing.

As an example, here's what a blog-post Document looks like:

```json
{
    "headline": "Lorem Ipsum Dolor",
    "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua",
    "datePublished": "2022-12-13T14:00:00+00:00",
}

Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sapien faucibus et molestie ac feugiat sed lectus vestibulum mattis.
```

> :warning: At present, Miniblog will simply ignore invalid Documents (i.e. Documents that do not follow this spec): invalid Documents will not be listed, nor will it be possible to request them directly.

## Front Matter

The first part of a Document *must* be front matter, encoded in a single [JSON](https://en.wikipedia.org/wiki/JSON) object.  The front matter *must* start with a left curly brace ("{") on a line by itself and *must* end with a right curly brace ("}") on a line by itself.

Take a look at the [reference on Miniblog content types](content-types.md) to find out about the available content types and the front-matter JSON elements supported by each of them.

## Body

The remainder of the file *may* be the body (full textual content) of the Thing.  Miniblog does not allow for a body in all [types of content](content-types.md).  If present, though, the body *must* be separated from the front matter by at least one blank line.  The body can be formatted using [GitHub-flavoured Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax).

[Next: Content Types &rarr;](content-types.md)
