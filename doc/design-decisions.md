# Design Decisions

Here I will document some of the decisions I've made, and why.  I think this is helpful because it will enable me&mdash;and others&mdash;to more easily review the code and make changes, and having to explain yourself is helpful in determining whether a decision is sensible&mdash;as a guide, think "if you can't describe something succinctly then you likely need to decompose further".  This is the experiment: finding what really matters and abandoning the things that are wasteful/unhelpful.

## Error Handling

- The error pages are static and deliberately basic.  Yes, there are times when you could do some interesting things to help the user, but I doubt many will care: I expect the vast majority simply press the back-button, return to the homepage, or go elsewhere, as soon as they realise something's gone wrong.  I can't remember ever receiving a message from a user about a missing page, for example.  In the absence of fancy functionality, there is no need to repeatedly render error pages.  Thus, Miniblog's error pages are generated offline by a command-line script.  They contain little markup, the CSS is inlined, and there are no images to download; in that sense, they are completely self-contained and demand little.

- Miniblog is small and, in its off-the-shelf capacity, doesn't need to expect change in quite the same way.  A simple list of the error pages to render is fine in this case: there's no need to waste resources scanning the filesystem each time the script is called.
