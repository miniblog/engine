{
    "title": "Welcome to My New Blog",
    "description": "An introduction to my new blog and the website software I'm using",
    "publishedAt": "2022-09-14 08:55:00"
}

This is the first post on my new blog.  I'm using [Miniblog, a minimal blogging system built in PHP](https://github.com/miniblog/engine).

With Miniblog, I have only to concentrate on writing content.  Miniblog was quick to install, requiring barely any configuration, and all I need do to publish a post is upload a file.

A Miniblog post is written in plain text, in a single file with the suffix `.md`.  The basename of the file (the part before the extension) is used as the slug.  The title and published date&mdash;and, optionally, a description&mdash;are written in a single JSON object at the top of the file.  The remainder of the file is the body of the article and that can be formatted using [(GitHub flavoured) Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax).

I can easily version-control my content, and I have the option to override the default templates.

Here's what the content of this post looks like:

```markdown
{
    "title": "Welcome to My New Blog",
    "description": "An introduction to my new blog and the website software I'm using.",
    "publishedAt": "2022-09-15 13:20:00"
}

This is the first post on my new blog.  I'm using [Miniblog, a minimal blogging system built in PHP](https://github.com/miniblog/engine).

With Miniblog, I have only to concentrate on writing content.  Miniblog was quick to install, requiring barely any configuration, and all I need do to publish a post is upload a file.

A Miniblog post is written in plain text, in a single file with the suffix `.md`.  The basename of the file (the part before the extension) is used as the slug.  The title and published date&mdash;and, optionally, a description&mdash;are written in a single JSON object at the top of the file.  The remainder of the file is the body of the article and that can be formatted using [(GitHub flavoured) Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax).

I can easily version-control my content, and I have the option to override the default templates.
```
