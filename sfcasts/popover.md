# Popover!

For day 11, where going to build our first big, beautiful, fully-functional feature:
a popover. As I mentioned, open source Stimulus controllers already exist to
solve lots of different problems. And one of the best resources for them is
Stimulus Components: a rich collection of controllers. We're going to work with the
one called popover.

And if you don't know, a popover is when you hover over something and a nice box
pops above, or over the element. It's like a tooltip, except they're usually
bigger and you can hover over the box itself.

## Installing & Setting up stimulus-popover

This is a *pure* JavaScript library. But we're not going to install it with
`yarn` or `npm`. Instead, run:

```terminal
php bin/console importmap:require stimulus-popover
```

Because this is a pure JavaScript package, there's no Flex recipe. The only change
this made was to `importmap.php`. So we have *access* to the code, but this time,
we need to register the controller manually.

That's okay. Copy these lines from the documentation... then open `assets/bootstrap.js`.
Paste this on top. We don't need this `Application.start()`... and move this
`application.register()` after... and call it `app`.

Congrats! We have a new controller called `popover`.

## Using the Controller

The goal is to hover over this planet and to get a little popover with extra
info. To get the popover to work, head down on the docs. There's some Rails
documentation for server-side stuff.... we don't need that. This is what we need.
So we need an element with `data-controller"popover"` and, inside, a link that,
on `mouseenter` calls a `show` method and, on `mouseleave` calls `hide`. Below,
this is the content that will be shown in the popover.

Copy this then, head to `homepage.html.twig`, and  search for planets. Here's the
`td` and here's the planet image. Paste... then I'll move my `img` inside.

Lovely! And then we need to put this `data-action` somewhere. It's tempting to put
it on the `img` itself. But, library adds the popover content *inside* the element
that triggers it... and since you can't put content inside an `img`, it's a no-go.
Instead, put it directly on the parent `div`. So on `mouseenter` of this div, show
the popover, on `mouseleave` of this div, hide the popover.

That should be enough! It's going to look terrible... but let's try it! It...
works! It's weird and jumpy, but it's doing it!

## Styling the Popover

time to make this look better. I'll select this entire `div` and paste. That
didn't do anything too big: I added a class `relative` on the outer `div`... and
down here, I've made the popover absolutely positioned and printed out some planet
info.

Now... look at that! You know what would be cool? A little arrow! And we can
add one in pure CSS by adding an `:after` element on the popover `card` target.
This is a standard CSS strategy for adding arrows that you can find on the
web, or you can use AI to help generate it Tailwind.

Open `app.css` and I'll paste in some code. You *can* do this in Tailwind, but
this was easy enough.

Go check it out! Love it!

## Lazy-Loading with a Turbo Frame

At this point, the popover works great and looks great. Are you up for a challenge?
Instead of loading all of this markup on page load, I want to load this content
via Ajax only once the user hovers. The popover library *does* have a way to do
this. But as an extra, extra challenge, I want to do it with regular ol' Turbo
frames. Because, Frames are *really* good at loading stuff via AJAX! Plus, we'll
see a couple of extra frames features that we haven't talked about yet.

To start, we need an endpoint that renders this planet info. In the homepage
template, copy that code, then delete it. In `templates/planet/`, create a new file
called `_card.html.twig`, and paste.

Next: create an endpoint for this. In `src/Controller/PlanetController.php`, anywhere,
I'll paste in a a route and controller.

Nothing special: it queries for the `Planet` and passes it to the template. *In*
that template, adjust the variables. Instead of `voyage.planet` just use `planet`
in each spot.

We now have an AJAX endpoint that returns the content. Now here's the magic part.
Over in `homepage.html.twig`, we want to load that content right here. We can do
that with a `turbo-frame`. For the id, let's use `card-` then `{{ voyage.planet.id }}`
so it's unique on the page. Add this same frame in `_card.html.twig`... with the
closing tag.

Usually, a `<turbo-frame>` will be one part of a whole page. But it's perfectly
ok to make an endpoint that *just* returns a single frame.

Back over in `homepage.html.twig`, we have the basic setup. The trick is that...
we're not waiting for somebody to click a link inside this frame that will then
load the card page. Nope, we want it to load immediately.

To do that, add a `src` attribute set to `{{ path() }}`... and... that's almost
correct. The route is `app_planet_show_card`.

Done! When a turbo frame appears that *already* has a `src` attribute, it will
make the AJAX call to load that content immediately.

Let's try it. Refresh and... and content missing. I messed something up! That's
ok - we can debug! The call failed with a 500 error. This is where the web debug
toolbar comes in handy. We can't see the error easily... but we *can* see the AJAX
and can click to see the big beautiful exception page. Ah, variable `voyage` does
not exist... because I need to adjust that to `planet.id`.

All right. And now... got it! There *is* a moment when there's no content, and
we'll fix that. But it *does* load via Ajax!

## Lazy-Loading Turbo Frames

And, by accident, our turbo frame is even being loaded *lazily*. What I mean is:
when we have a `<turbo-frame>` with a `src` attribute, that AJAX call is made
*immediately*. With that in mind, shouldn't we see 30 Ajax calls happening all at
once? Yea... but we *don't*. It only happens when we hover. Why?

Inspect that element. Ah. It's by accident thanks to the `template` element. The
`template` element is special in your browser: anything behaves... as if it's
*not* on the page: almost like it's a string instead of an element. So, when we
first load, the `<turbo-frame>` is *technically* not part of the page. But the
moment we hover, it copies that onto the page, the `turbo-frame` comes alive
and the Ajax call is made. Pretty cool!

But there may be other situations in the future when we want a `turbo-frame` to
load its content *only* once that frame becomes visible. And we can do that!
To show this off, change this `template` to a `div` temporarily.

Now this is going to look awful... because every card will be visible all at once.
Yup! They're all on the page and it made 30 Ajax calls immediately! Yikes! To
tell these frames to no load until they become visible on the page, add `loading="lazy"`.

Refresh now. 3 ajax calls... because only 3 frames are visible! The other elements
are all on the page, but below the scroll. Watch this number as I scroll. See that?
As they become visible, each makes its AJAX call. So cool.

Change the element back to a `template` so that things work nicely again.

## Adding Loading Content

I'm really happy. But I want to *perfect* this new feature. One thing I don't like
is that it's empty before the Ajax call finishes. I'd like to add some loading
content.

This is easy: when you have a `turbo-frame` with a `src` attribute, whatever content
is inside will be shown by default while it loads. I'll paste in a `div` here with
an SVG. This SVG comes from tabler icons. That's a great source to find an icon
that you copy into your project. I've coupled that with an `animate-spin` class from
Tailwind.

Let's check it. Quick, but lovely!

## Remembering the Ajax Call

Do we have time for one more thing? When we hover over this element, it makes the
AJAX call. And... it repeats that *every* time we hover. It doesn't remember the
content from the AJAX call.

That's due to how the popover controller works... and if I had been less stubborn
and used *its* way of Ajax-loading content, it wouldn't be a problem. Anyway,
each time we hover, it creates the `turbo-frame`, destroys it, creates a new one,
etc.

So: how can we make the controller *remember* the content? I have no idea! So
let's go look inside the code. In `assets/vendor/stimulus-popover/`, open this
up. This is minified... but a quick Cmd+L to reformat the code fixes that. How
cool is that? We can now read this core file - and even add temporary debugging
code if we needed to. I think I see a way that we can make this work.

Just like with Symfony controllers, we can override Stimulus controllers. Inside
of the `controllers/` directory, create our own `popover_controller.js`. Inside,
Ill paste in the content.

Notice: normally we import `Controller` from Stimulus and extend that. But in
this case, I'm importing the popover controller directly and extending *that*.
Then we override the `show` method and `hide` method so we can toggle a `hidden`
instead of fully destroying the element.

And now that we our own controller named `popover`, in `bootstrap.js`, we don't need
to register the one from Stimulus components anymore. The `popover` controller
will be *our* controller... then we leverage it via inheritance.

Moment of truth! It loads once... then *remembers* its content.

Not *only* did we create the perfect popover, we now easily repeat this on other
parts of our site. If you're wondering if we could reuse some of the popover markup,
stay tuned for Day 23 when we talk about Twig components.

Ok, get some rest! Tomorrow we'll create one of the smallest and most valuable
Stimulus controllers: auto-submit.
