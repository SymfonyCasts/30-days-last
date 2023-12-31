# Turbo Drive

It's day 9! Beautiful day 9 where we start to make our app shine. All the
fundamentals are in place - AssetMapper, Tailwind & Stimulus - so today
is... almost a victory lap. We're about to get a huge bang for our buck thanks to
a library called Turbo.

Right now, our site, of course, has full page refreshes. Keep an eye on the logo
in the address bar. When I click, everything is done with a full page refresh. That's
fine. Never mind, that's not fine! I want our site to have a *devastatingly* great
user experience. 

Luckily, we have Turbo on our team: a JavaScript library forged from the depths
of the internet, bent on destroying all full page refreshes. Watch on their site:
you won't see any full page reloads as we navigate. And check out how *fast* that
feels. It feels like a single page application, because, well, it *is*, it's just
not one that we need to build with a frontend framework like React.

## Installing Turbo

Like Stimulus, Symfony has a package that helps us work with this Turbo. Find your
terminal, and run:

```terminal
composer require symfony/ux-turbo
```

When that finishes, do:

```terminal
git status
```

Like the other UX package, this modified `controllers.json` and `importmap.php`.
In `assets/controllers.json`, it added *two* new controllers:

[[[ code('661e2a081c') ]]]

The first is... kind of a fake controller. It loads and activates Turbo - you'll
see what that does in a moment - but it's not a Stimulus controller that we'll
ever use directly. The second controller is optional - we're not going to talk
about it, and it's disabled by default.

The other change, in `importmap.php` is, no surprise: it added `@hotwired/turbo`:

[[[ code('82d54c52e1') ]]]f

The result of this single command is *amazing*. When I refresh, watch the address
bar: we're not going to see *any* more full page reloads! And everything feels
super-duper fast. Uh, I love it. Even the forms! Click edit. Watch: this submits
via AJAX. Or, if I go and create a new one, hit enter, *that* submits via AJAX.
Our site just got transformed into a single page app with one command!

## Turbo: What's the Catch?

You might be thinking:

> This is too good to be true, Ryan. What's the catch?

Ok, there *is* a catch, but minor for new projects: your JavaScript must be written
to work *without* full page refreshes. Historically, we've written our JavaScript
to execute on page load... or run on `document.ready`. And those things just don't
happen after the first page load. But as long as you have everything written in
Stimulus, you're good.

For example: our `celebrate` controller: it doesn't matter how many pages I click
around to, that just keeps on rolling.

If your app *isn't* ready for Turbo yet - because of the JavaScript problem - you
can disable it. In `app.js`, `import * as Turbo` `from '@hotwired/turbo'`. Below,
say `Turbo.session.drive = false`. I'm not going to do that... so I'll comment it out:

[[[ code('46c6ae9099') ]]]

But why would I install Turbo... just to disable it? Because Turbo is actually
*three* parts. The first is called *Turbo Drive*. That's the part that gives us
free AJAX navigation on all link clicks and form submits. And *that's* what this
disables.

But even if you're not ready for Drive, you can still use the two other parts:
*Turbo Frames* and *Turbo Streams*. These are *powerful* and we'll spend a lot
of time in this tutorial doing some wild things with them.

## Preloading Links

Turbo Drive itself is pretty simple, but it *does* have a few other tricks up its
sleeve. And they're constantly adding new things. For example, one feature is called
preloading. To show this off, go into `templates/base.html.twig`. If you're ever
on a page... and you're *really* sure that you know what link the user is going to
click next, you can *preload* that.

For example, on the "voyages" link, add `data-turbo-preload`:

[[[ code('9407ada213') ]]]

Refresh, inspect element, then go to network tools, XHR... and clear the filter.
When I refresh, we immediately see an AJAX request made for the voyages page!
Because of this, when we click this link, watch: it's going to be instant. Boom!

Use this only when you're *quite* sure what the next page will be. We don't want
to trigger a bunch of unnecessary traffic to your site that won't be used.

Oh, and see these JavaScript errors? These come from Symfony's web debug toolbar
and profiler. I'm not sure why... but it doesn't like the preloading. That's
something we need to fix, but the preloading itself works fine. You can ignore
these.

Back in the template, remove the `data-turbo-preload`... because we don't *really*
know what page the user will click to next.

Today was *great*. With one library, we eliminated all full page reloads. What
could be next? Tomorrow we'll talk about Turbo Frames: a way for us to create
Ajax-loading "portions" of our page, without writing a single line of JavaScript.
