# Turbo Frames

On this, day 10: we're going to talk about an ancient concept: frames. If you're
old enough on the Internet, like me, you might remember iframes. They were these
weird things where you could separate your site into different pieces. And when
you clicked a link inside a frame, the navigation *stayed* inside that frame.
It was like having separate web pages that you cobbled together into one.

The second part of Turbo is Turbo Frames... which is a *not* weird, modern way to
break your page down into parts... kinda similar to iframes.

See this left sidebar? When we click a planet, it takes us to the show
page for that planet. Cool. But not cool enough! Instead, when I click a planet,
I want that content to load right inside of this sidebar *without* changing pages.

## Adding the `<turbo-frame>`

To do that, find the sidebar: it's over in `templates/main/homepage.html.twig`...
up near the top. This partial renders that planet list. To make this a frame,
find the element that surrounds it and change it to `<turbo-frame>`. And the one
rule of frames is that each needs to have an `id` attribute. It should be something
unique that describes what it holds. How about `planet-info`:

[[[ code('05db661c1b') ]]]

Ok: what does that do? At first, nothing. A `<turbo-frame>` is just an HTML element
like a `div`, and so it renders normally. Though, for styling purpose, `turbo-frame`
is an *inline* element by default.

However, when we click a link... it's busted! It says "Content missing". And in the
console:

> The response did not contain the expected `<turbo-frame id="planet-info">`.

When we click this link, it makes an Ajax request to the show page... like it
normally would with Turbo. But because the link is inside a `<turbo-frame>`, it grabs
the HTML and looks for a *matching* `<turbo-frame>` with `id="planet-info"`. If it
finds that, it grabs the content inside and puts *just* that in the `<turbo-frame>`
over here.

## Adding the Matching `<turbo-frame>`

This means that each link inside a `<turbo-frame>` - whatever page it goes
to - that page *must* have a matching `<turbo-frame>`.

Copy this `<turbo-frame id="planet-info">` and then open `planet/show.html.twig`.
Put this around the content that we want to load into the sidebar. I don't really
want the `h1`... so put it around this table. Add the closing `</turbo-frame>`
at the bottom:

[[[ code('abb6ae6b38') ]]]

Refresh! And click. How cool is that? It makes an AJAX request to this page, grabs
*just* the `<turbo-frame>` content and puts it here.

You know what else would be great? A "back" link! To add
that, still inside the `<turbo-frame>`, add a `<div class="mt-2">` then an `a`,
`href` set to `{{ path() }}`. Link to the homepage:

[[[ code('d6cbba9f7c') ]]]

Try it! We don't even need to refresh. Behold! A back link! Whoops, let's make
that more of an arrow. When we click it... it goes back! That made
an AJAX request to the homepage and looked for a matching
`<turbo-frame id="planet-info">`. And guess what that holds? This list of planets.

We're on a roll! Before we finish today, add one more link: an edit link. The
route is `app_planet_edit`... with `id` set to `planet.id`:

[[[ code('418e66c400') ]]]

Cool! this time, if we click a planet... then edit... it doesn't work! And I bet you
can guess why. It made an AJAX request to the *edit* page.... but there
is *no* matching `<turbo-frame>` on that page. And so, we get this error.

But... I *don't* want to add a `<turbo-frame>` to the edit page. The
form wouldn't fit into the sidebar anyway. Nope, when I click this link, I want
it to result in a "full page" Turbo navigation.

As soon as you add a `<turbo-frame>`, you need to keep track of the links that
you have inside of it and either make sure that each goes to a page that has a
matching `<turbo-frame>`.... *or* that you target the link or form to do a *full*
visit.

## Targeting Links to the Full Page

How do you do that? Find the link, and add `data-turbo-frame` - that's a typo Ryan -
set to `_top`:

[[[ code('eb0db440c3') ]]]

Now, without refreshing, hit edit. It still breaks. I did the wrong thing.
It's `data-turbo-frame="_top"`. There we go.

Now hit edit. Full page navigation! It's still Ajax-powered, but the whole page
changes.

The other way to target links or forms to the full page is on the `<turbo-frame>`
itself. You might say:

> Hey! I want *all* links in this `<turbo-frame>` to be full page navigation by
> default.

To do that, set `target` to `_top`. Then, if you have a *specific* link that you
want to load in this frame, add `data-turbo-frame` equals and then the id:
`planet-info`.

Both approaches are fine: your call. But adding `target="_top"` to each
frame is a bit safer.

## Hiding Content Not in a Frame

So this is working *super* well! Except for the fact that if the user *does* ever
get to the planet show page, we have an extra set of links. We really only want to
show those when we're inside the `<turbo-frame>`. How can we do that?

When Turbo sends an Ajax request for a `<turbo-frame>`, it does add a request header
that *tells* your app that this is a Turbo Frame request. You can use that inside
Symfony to conditionally do different things... like conditionally render these
links.

We *are* going to do that one time later in the tutorial. However, I try to minimize
this: it adds complexity. Another option is to hide extra stuff with CSS! For
example, we could add a class onto the sidebar... then only show these links if
we're *inside* that class.

However, Tailwind doesn't really work like that. In Tailwind, you can't change
styling conditionally based on your parent. At least not out-of-the-box. But we
*can* do this with a trick called a variant.

The first thing to notice is that a `<turbo-frame>`, by default, looks like this:
exactly like we have in our template. But as soon as we click a link, it has
a `src` attribute. We can take advantage of that by adding a way inside of Tailwind
to style elements *conditionally* based on whether they are inside of a `<turbo-frame>`
that has a `src` attribute. Because, it *will* have a `src` over here...
but won't have a `src` inside of this `<turbo-frame>`... because it never
navigates. In fact, it would be a good idea to add a `target="_top'` to *this*
frame, since we don't need fancy frame navigation on this page.

Anyway, Tailwind variants are a bit more advanced, but simple enough. Import
this `plugin` module, then go down to `plugins`. I'll paste in some code:

[[[ code('229fdf30a6') ]]]

This adds a variant called `turbo-frame`: you'll see how we use that in a second.
It basically applies to an element that's inside a `<turbo-frame>` that has a `src`
attribute.

Because we called this `turbo-frame`, copy that. Now, in `show.html.twig`,
add a `hidden` class to hide this `div` by default.

When we refresh, it's gone. But then, if we match our `turbo-frame` variant, change
to display `block`:

[[[ code('ce77a64553') ]]]

Check it out. When we refresh, those links are still hidden. But over here... we've
got them! Because we're inside  a `turbo-frame` with a `src` attribute, our variant
activates and the display `block` takes over.

Turbo Frames *do* add some complexity, but we've only started to scratch the
surface on what they make possible.

Tomorrow, when I hover over each planet, I want to add a cool popover with more planet
info. To make that happen, we're going to install *another* third-party Stimulus
controller.
