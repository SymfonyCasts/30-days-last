# Turbo Frames

On this, day 10: we're going to talk about an ancient concept: frames. If you're
old enough on the Internet, like me, you might remember iframes. They were these
weird things where you could separate your site into different pieces. And when
you clicked a link inside of a frame, the navigation *stayed* inside the frame.
It was like having separate web pages that you cobbled together into one.

The second part of Turbo is Turbo Frames... which is a *not* weird, modern way to
break your page down into parts - similar to iframes.

See this left sidebar? When we click one of these planets, it takes us to the show
page for that planet. Cool. But cool enough! Instead, when I click a planet,
I want that content to load right inside of this sidebar *without* changing pages.

## Adding the `<turbo-frame>`

To do that, find the sidebar: it's over in `templates/main/homepage.html.twig`...
up near the top. This partial renders that planet list. To make this a frame,
find the element that surrounds it and change it to a `<turbo-frame>`. And the one
rule of frames is that they all need to have an `id` attribute. It should be something
unique that describes what it holds. How about `planet-info`.

Ok: what does that do? At first, nothing. A `<turbo-frame>` is just an HTML element
like a `div`, and so it renders normally. Though, for styling purpose, `turbo-frame`
is an *inline* element by default.

However, when we click a link... it breaks! It says "Content missing". And in the
console:

> The response did not contain the expected `<turbo-frame id="planet-info">`.

As soon as you surround something by a `<turbo-frame>`, here's what happens. When
we click this link, it makes an Ajax request to the show page... like it normally
would with Turbo. But because the link is inside a `<turbo-frame>`, it grabs
the HTML and looks for a *matching* `<turbo-frame>` with `id="planet-info"`. If it
finds that, it grabs the content inside and puts *just* that in the `<turbo-frame>`
over here.

## Adding the Matching `<turbo-frame>`

This means is that each link inside of a `<turbo-frame>` - whatever page it goes
to - that page *also* needs to have a matching `<turbo-frame>`.

Copy this `<turbo-frame id="planet-info>` and then open `planet/show.html.twig`.
Put this around the content that we want to load into the sidebar. I don't really
want the `h1`. Let's put it around this table. Add the closing `</turbo-frame>`
at the bottom.

Refresh! And click. How cool is that? It makes an AJAX request to this page, grabs
*just* the `<turbo-frame>` content and puts it over here.

You know what else would be great? A "back" link to get back to the list. To add
that, still inside the `<turbo-frame>`, add a `<div class="mt-2">` then an `a`
tag with `path`. And... link to the homepage.

Try it out. I don't even need to refresh. We have a back link! Whoops, let's make
that more of an arrow. When we click it... it works! In this case, that made
an AJAX request to the home page and looked for the matching `<turbo-frame>`.
And guess what that holds? The list of planets.

We're on a roll. Before we finish today, add one more link: an edit link. The
route is `app_planet_edit`... with `id` set to `planet.id`.

Cool! this time, if we click a plant... then edit... it doesn't work! And I bet you
can guess why. This time, it made an AJAX request to the *edit* page.... but there
is *no* matching `<turbo-frame>` on that page. And so, we get this error.

But this time, I *don't* want to add a `<turbo-frame>` to the edit page. The
form wouldn't fit into the sidebar anyway. Nope, when I click this link, I want
it to result in a "full page" Turbo navigation.

So as soon as you add a `<turbo-frame>`, you need to keep track of the links that
you have inside of it and either make sure that each goes to a page that has a
matching `<turbo-frame>`.... or that you target the link or form to do a *full*
visit.

## Targeting Links to the Full Page

How do you do that? Find the link, and add `data-turbo-frame` - that's a typo Ryan -
set to `_top`.

Now, without refreshing, hit edit. And... it still breaks. I did the wrong thing.
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

Both approaches are fine - your call. But adding `target="_top"` to each
frame is a bit safer.

## Hiding Content Not in a Frame

So this is working *super* well! Except for the fact that if the user *does* ever
get to the planet show page, we have an extra set of links. We really only want to
show those when we're inside the `<turbo-frame>`. How can we do that?

When Turbo sends an Ajax request for a `<turbo-frame>`, it does add a request header
that *tells* your app that this is a Turbo Frame request. And you can use that inside
of Symfony to conditionally do different things... perhaps conditionally render these
links.

We *are* going to do that one time later in the tutorial. However, I try to minimize
that: it adds complexity. Another option is to hide extra stuff with CSS! So for
example, we could add a class onto the sidebar... then only show these links if we
are *inside* that class.

However, Tailwind doesn't really work like that. In Tailwind, you can't change
styling conditionally based on your parent. At least not out-of-the-box. But we
*can* do this with a trick called a variant.

The first thing to notice is that a `<turbo-frame>`, by default, looks like this:
exactly like we have in our template. But as soon as we click a link, it has
a `src` attribute. We can take advantage of that by adding a way inside of Tailwind
to style elements *conditionally* based on whether they are inside of a `<turbo-frame>`
that has a `src` attribute. Because it will have a `src` attribute over here...
but won't have a `src` attribute inside of this `<turbo-frame>`... which never
navigates. In fact, it would be a good idea to add a `target="_top'` to *this*
frame, since we don't need fancy frame navigation on this page.

Anyway, Tailwind variants are a bit more advanced, but simple enough. Import
this `plugin` module, then go down to `plugins`. I'll paste in some code.

This adds a variant called `turbo-frame`: you'll see how we use that in a second.
It basically applies to element that's inside a `<turbo-frame>` that has a `src`
attribute.

So because we called this `turbo-frame`, copy that. Now, in `show.html.twig`,
add a `hidden` class to hide this `div` by default.

When we refresh, it's gone. But then, if we are inside a `<turbo-frame>`, change
to display `block`.

Check it out. When we refresh, those links are still hidden. But over here... we've
got them! Because we're inside  a `turbo-frame` with a `src` attribute, our variant
activates and the display `block` takes over.

Turbo Frames *do* add some complexity, but we've only started to scratch the
surface on what they make possible.

Tomorrow, when I hover over each planet, I want to get a cool popover with more planet
info. To make that happen, we're going to install *another* third-party Stimulus
controller.
