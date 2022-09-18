# Content

The base content-type in Miniblog is 'article'.  All user content is stored under `content/`, and *blog-post* articles, in particular, live in `content/post/`.

## Writing Articles

A Miniblog article is written in plain text, in a single file with the suffix `.md`.  The basename of the file (the part before the extension) is used as the slug and *must* comprise only lowercase characters, digits, and dashes.

The content of an article file *must* comprise two parts:
- front matter (information about the article);
- and the body of the article.

Here's what a complete article looks like:

```markdown
{
    "title": "Lorem Ipsum Dolor",
    "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
    "publishedAt": "2022-09-03"
}

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quis molestie lorem. Nullam non quam leo. Mauris eu nibh at quam pellentesque posuere. Aliquam consequat ipsum eu fringilla venenatis. Nam ante massa, sagittis volutpat ipsum vel, vulputate consectetur odio. Proin in tortor sed mi tincidunt tristique.

Cras pharetra eu nulla eget convallis. Nam mollis ligula sem, in dictum nunc fringilla suscipit. Nam vel nulla et lacus laoreet condimentum non non arcu. Aliquam lacus quam, imperdiet non convallis nec, tincidunt non massa. Nulla sit amet pulvinar purus, quis ultricies nibh.
```

:warning: At present, Miniblog will simply ignore invalid articles (i.e. articles that do not follow this spec): invalid articles will not be listed, nor will it be possible to request them directly.

### Front Matter

The first part of an article *must* be front matter, encoded in a single JSON object.  The front matter *must* start with a left curly brace ("{") on a line by itself and *must* end with a right curly brace ("}") on a line by itself.

- The front matter *must* include a title and the published date.
- The front matter *may* contain a description.
- The published date *must* be in [ISO 8601 format](https://en.wikipedia.org/wiki/ISO_8601).

:information_source: Miniblog will use the title in the front matter to automatically create a heading for the article.

:information_source: The title, and the description, if present in the front matter, will be used in the meta tags.

### Article Body

The remainder of the file *must* be the body of the article and *must* be separated from the front matter by at least one blank line.  The body can be formatted using [(GitHub flavoured) Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax).

:information_source: Remember: there is no need to add a heading to the body: Miniblog will automatically create one using the title in the front matter.
