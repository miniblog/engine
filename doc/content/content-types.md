# Content / Content Types

## `BlogPosting`

<dl>
  <dt>Schema.org type<dt>
  <dd><a href="https://schema.org/BlogPosting">BlogPosting</a></dd>
  <dt>Miniblog Document path<dt>
  <dd><code>data/Thing/CreativeWork/Article/SocialMediaPosting/BlogPosting/</code></dd>
  <dt>Body?<dt>
  <dd>Yes, required</dd>
</dl>

The ID (basename) of a BlogPosting Document is used as the slug of the blog post.

### Front Matter

| Name            | Type   | Format                        | Required? | Notes                                                                         |
|-----------------|--------|-------------------------------|-----------|-------------------------------------------------------------------------------|
| `headline`      | string |                               | Yes       | Used to automatically create a heading.  Used in the meta tags.               |
| `description`   | string |                               | Yes       | Used in the meta tags                                                         |
| `datePublished` | string | ISO 8601 date-time[^1]        | Yes       |                                                                               |
| `dateModified`  | string | ISO 8601 date-time[^1]        |           |                                                                               |
| `inLanguage`    | string | IETF BCP 47 language code[^2] |           | Overrides the language specified in `data/Thing/CreativeWork/WebSite/this.md` |

## `Person`

<dl>
  <dt>Schema.org type<dt>
  <dd><a href="https://schema.org/Person">Person</a></dd>
  <dt>Miniblog Document path<dt>
  <dd><code>data/Thing/Person/</code></dd>
</dl>

At present, `Person` is used only to capture details of the owner, or principal author, of the website's content: these data are stored in `data/Thing/Person/owner.md`.

### Front Matter

| Name         | Type   | Format | Required? | Notes                      |
|--------------|--------|--------|-----------|----------------------------|
| `givenName`  | string |        | Yes       | Also known as "first name" |
| `familyName` | string |        | Yes       | Also known as "last name"  |
| `email`      | string |        | Yes       |                            |

## `WebSite`

<dl>
  <dt>Schema.org type<dt>
  <dd><a href="https://schema.org/WebSite">WebSite</a></dd>
  <dt>Miniblog Document path<dt>
  <dd><code>data/Thing/CreativeWork/WebSite/</code></dd>
  <dt>Body?<dt>
  <dd>Yes, optional</dd>
</dl>

At present, `WebSite` is used only to capture details of the Miniblog website: these data are stored in `data/Thing/CreativeWork/WebSite/this.md`.

The body&mdash;if present&mdash;is used as the blurb, the introductory text displayed at the top of the homepage.

### Front Matter

| Name            | Type   | Format                        | Required? | Notes                                                           |
|-----------------|--------|-------------------------------|-----------|-----------------------------------------------------------------|
| `headline`      | string |                               | Yes       | The name of the website                                         |
| `description`   | string |                               | Yes       | Used in the meta tags                                           |
| `datePublished` | string | ISO 8601 date-time[^1]        | Yes       | The date the website was launched; used in the copyright notice |
| `inLanguage`    | string | IETF BCP 47 language code[^2] | Yes       | The principal language of the website's content                 |
| `url`           | string |                               | Yes       | The canonical URL of the website                                |
| `dateModified`  | string | ISO 8601 date-time[^1]        |           |                                                                 |

[^1]: [ISO 8601 format](https://en.wikipedia.org/wiki/ISO_8601) (e.g. `2023-02-24` or `2023-02-24T10:22:39+0000`)
[^2]: [IETF BCP 47 language code](https://en.wikipedia.org/wiki/IETF_language_tag) (e.g. `en-GB` for British English or `en-US` for American English)
