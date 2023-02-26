# Design Decisions

Here I will document some of the decisions I've made, and why.  I think this is helpful because it will enable me&mdash;and others&mdash;to more easily review the code and make changes, and having to explain yourself is helpful in determining whether a decision is sensible.  This is the Miniblog exercise: finding what really matters and abandoning the things that are wasteful/unhelpful.

## Error Handling

- The error pages are static and deliberately basic.  Yes, there are times when you could do some interesting things to help the user, but I doubt many will care: I expect the vast majority simply press the back-button, return to the homepage, or go elsewhere, as soon as they realise something's gone wrong.  I can't remember ever receiving a message from a user about a missing page, for example.  In the absence of fancy functionality, there is no need to repeatedly render error pages.  Thus, Miniblog's error pages are generated offline by a command-line script.  They contain little markup, the CSS is inlined, and there are no images to download; in that sense, they are completely self-contained and demand little.
- Miniblog is small and, in its off-the-shelf capacity, doesn't need to expect change in quite the same way.  A simple list of the error pages to render is fine in this case: there's no need to waste resources scanning the filesystem each time the script is called.

## Miscellaneous

- Front-matter is written in JSON, which is supported natively by PHP.
- Code syntax-highlighting is carried out *on the server*.  (Currently only PHP is highlighted, using the built-in function `highlight_string()`.)  The alternative is to do syntax-highlighting in JavaScript, on the client.  The problem with that is the same work&mdash;of syntax highlighting&mdash;will be done repeatedly, by different clients.  Since my aim is to eventually cache the HTML generated from Miniblog Documents, it will then be far less wasteful to do the work on the server.
- I started out using a particular naming convention I follow while working with Symfony in my day job&mdash;thinking it'd be more developer-friendly to do so.  Since this resulted in Miniblog doing extra work to translate certain names, I switched to a new, 'effortless' naming scheme.  In the end, the replacement is almost certainly more intuitive.
