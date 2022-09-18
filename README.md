# Miniblog

Miniblog is a minimal blogging system, written in object-oriented PHP, aimed primarily at developers.  It is an ongoing experiment in sustainable software design.  My aim is to create a blogging system that provides only the features that are really needed, only those that bring real value.

Several (obvious) needs must be balanced:
- The software should help its owner maintain focus on the task in hand; in this case, the task is publishing articles.
- The website should be helpful to the end user, which means it should be easy to use and be easily consumed by search engines.
- The software should use only the resources it absolutely needs to&mdash;every computation costs electricity.

## Background

The idea for an experiment really came together when I was looking for a PHP blogging system to use for my own blog.  I thought it'd be possible to find a really minimal blogging system, but I was faced with the usual problem: lots of features and, thus, a proportionately-steep learning curve.  I abandoned even the most promising-looking option because I hadn't been able to get it working satisfactorily within a couple hours.  All I wanted was to publish articles to a smart-looking website and be able to version-control my content!

Later, I was really excited to stumble on [Dead Simple Blog (DSB)](https://github.com/paintedsky/dead-simple-blog) by [@paintedsky](https://github.com/paintedsky): I was intrigued by the author's rationale, and his project met almost all my requirements.  It was through developing a fork of DSB that I decided to create&mdash;initially, at least&mdash;a spin-off.  You can [read about the fundamental differences between Miniblog and DSB](doc/miniblog-vs-dsb.md).

## Now What?

If you're ready to get stuck in then [read the installation guide](doc/installation.md).  Otherwise, [the documentation starts here](doc/README.md).
