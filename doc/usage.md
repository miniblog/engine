# Usage

## Writing Articles

:information_source: Miniblog supports articles written for [Dead Simple Blog (DSB)](https://github.com/paintedsky/dead-simple-blog) but you are encouraged to use the Miniblog format because the result will be richer, more search-engine-friendly HTML.

A Miniblog article is written in a text file and comprises two parts:
- The first part *must* be front matter (information about the article) encoded in a single JSON object.
- The remainder of the file *must* be the body of the article; the body can be formatted using Markdown.

In terms of data, an article:
- *must* have a title, body, and published date
- *may* have a description

Here's what a complete article looks like:

```markdown
{
    "title": "Writing Articles for Miniblog",
    "description": "Information on how to structure article files, format content, for your Miniblog blog.",
    "publishedAt": "2022-09-03"
}

This part of the file contains the body of the article.  It *must* be separated from the front matter by at least one blank line.

Front matter is encoded in a single JSON object.  The front matter *must* start with a left curly brace ("{") on a line by itself and *must* end with a right curly brace ("}") on a line by itself.

There is no need to add a heading: Miniblog will automatically create one using the title in the front matter.

The title, and the description, if present, will be used in the meta tags.
```
