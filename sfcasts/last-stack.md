# Hello LAST Stack!

Hey everyone! Welcome to 30 days of LAST Stack! I gotta say, this might
be my favorite tutorial ever. I had an absolute blast building this, because unlike
usual around here, I'm going to go a little bit less into teaching deep
concepts and instead focus on making a rich, polished, beautiful product. And I think
you're going to love it.

But first, LAST Stack, what the heck is that? It's an acronym that... I made up.
I wanted something fun to match a whole new paradigm. It stands for Live Components,
AssetMapper, Stimulus, and Turbo. It's a front-end stack that'll let us
build a *truly* rich user interface - like a single-page application, with modals
and AJAX everywhere - but entirely with Symfony, Twig...
and just a bit of JavaScript. Oh, and this will require *no* build step and no Node.js.
Woo!

By the end of this tutorial, we're going to have reusable patterns that we can
leverage in on our projects to get things done really quickly but that *work*
and *feel* incredible.

At the core of this whole system is Hotwire: a collection of libraries
that include Turbo, Stimulus and Strada. Strada is the new kid on the block and
it looks *cool*. We won't have time to talk about it, but it promises to let you
take the same project that we're about to build and use it to power a mobile app.
Woh.

One other cool things about Hotwire is that... it's *not* unique to Symfony.
It's used, for example, by the Ruby on Rails community. And many of the things that
we're going to build come from patterns I learned from people in that community.
The fact that we're all using the same tool means we get to share libraries, share
ideas and build on top of each other's shoulders. That's massive.

## Project Setup

So let's get into this! Because it's fun to make pretty things that pop onto the
screen, you should absolutely download the course code and code along with me. When
you unzip the file, you'll find a `start/` directory, which has the same files that
you see here, including the all-important README.md! This tells you all about
how to get the project set up.

The last step will be to open a terminal, move into the project, and run:

```terminal
symfony serve -d
```

To start a local web server at ... oh, in my case, `127.0.0.1:8001`. I must
already have something running on port 8000. I'll click the link to see a big, ugly
page of... nothing! That's on purpose!

What we're starting with is a Symfony 6.4 project. I've pre-installed Twig and we
have two different entities - `Planet` and `Voyage` - because we're going to build
a trip-planning site for aliens. I also have some data fixtures and I used
MakerBundle to generate a CRUD for each entity. This `PlanetController`,
`VoyageController` and these templates come from MakerBundle, with just a few styling
adjustments.

But basically... there's nothing special going on! We do have a `MainController`,
which powers this homepage:

[[[ code('fb904fa3fa') ]]]

It contains a query that will help us later... but the template, right now,
is doing a whole lot of nothing:

[[[ code('6bb0a02edd') ]]]

No CSS, no JavaScript, no assets of any kind... and the site doesn't *do*
anything. But in 30 short lessons, we'll transform this into a small digital
masterpiece. 

That's it for day 1. Tomorrow, we'll install AssetMapper: a system for handling CSS,
JavaScript and other frontend assets with batteries include... but absolutely
*no* build step.
