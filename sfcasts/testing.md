# Functional Testing

Welcome back to part 2 of day 28. I bent the rules today and made it a double
feature. We talked about testing Twig & Live components... but we *also* need to
talk about functional - or end-to-end - testing in general. That's where we
programmatically control a browser, have it click links, fill out forms, etc.

Two things about this. First, we're going to create a system that I
*really* like. And second, the road to *get* there is going to be... honestly,
a bit bumpy. It's *not* a smooth process and that's something we as a community
should work on.

## zenstruck/browser

Symfony has built-in functional testing tools, but I like to use another library.
At your terminal, install it with:

```terminal
composer require zenstruck/browser --dev
```

Next, in the `tests/` folder, I'll create a new directory called `Functional/`...
then a new class called `VoyageControllerTest`. And I guess I *could* put that
into a `Controller/` directory also.

For the guts, I'll paste in a finished test.

Ok, we're using `ResetDatabase` and `Factories`... it extends the normal
`WebTestCase` for functional tests... and then `HasBrowser` comes from the Browser
library and gives us the ability to call `$this->browser()` to control a browser
with this really smooth API. This goes through the flow of going to the voyage
page, clicking "New voyage", filling out the form, saving and asserting at the bottom.
The test starts with a single `Voyage` in the database, so after we create a new
one, we assert that there are *two* on the page.

To run this, use the same command, but target the `Functional/` directory:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

And... it actually passes! Sweet!

## Testing JavaScript with Panther

But hold your horses. Behind the scenes, this is *not* using real browser: it's
just making fake requests in PHP. That means it doesn't execute JavaScript. We're
testing the experience a user would have if they had JavaScript *disabled*.
That's fine for many situations. However, *this* time, I want to test all the
modal fanciness.

To run the test using a *real* browser that supports Javascript - like Chrome -
change to `$this->pantherBrowser()`.

Try it:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

No dice! But a nice error: we need to install `symfony/panther`. Let's do that!

```terminal
composer require symfony/panther --dev
```

Panther is a PHP library that can programmatically control *real*
browsers on your machine. To use it, we *also* need to extend `PantherTestCase`.

Try it again:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

We don't *see* the browser - it opens invisibly in the background - but it's now
using Chrome! And the test fails - pretty early:

> Clickable element "New Voyage" not found.

Hmm. It clicked "Voyages", but didn't find the "New Voyage" button. A fantastic
feature of `zenstruck/browser` with Panther is that, when a test fails, it takes
a *screenshot* of the failure.

Inside the `var/` directory... here it is. Huh, the screenshot shows that we're
still on the homepage - as if we never clicked "Voyages"... though you can kind of
see that the voyages link looks active.

The problem is that the page navigation happens via Ajax... and our tests don't
know to *wait* for that to finish. It clicks "Voyages"... then immediately tries
to click "New Voyage". This will be the *main* thing that we need to fix.

## Loading a "test" Dev Server

But before that, I see a bigger problem! Look at the data: this is *not* coming from
our test database! This is coming from our dev site!

Even though we can't see it, Panther *is* controlling a *real* browser. And... a real
browser needs to access our site using a real web server via a real web address.
Because we're using the `symfony` web server, Panther detected that and... used it!

But... that's *not* what we want! Why? Our server is using the `dev` environment
and the `dev` database. Our tests should use the `test` environment and the
`test` database.

To fix this, open up `phpunit.xml.dist`. I'll paste in two environment variables.
The first... is kind of a hack. That tells Panther to *not* use our server. Instead,
Panther will now silently start its *own* web server using the built-in PHP web
server. The second line tells Panther to use the `test` environment when it
does that.

Over in the test, to make it even easier to see if this is working, after we click
voyages, call `ddScreenshot()`: take a screenshot, then dump and die.

Run it:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

It hits that... and saved a screenshot! Cool! Find that in `var/`. And... ok.
It looks like the new web server is being used... but it's missing all the styles!

## Debugging by Opening the Browser

Time for some detective work! To understand what's going on, we can temporarily
tell Panther to *actually* open the browser, like, so we can see it and play with
it.

After we visit, say `->pause()`.

Then, to open the browser, prefix the test command with
`PANTHER_NO_HEADLESS=1`:

```terminal-silent
PANTHER_NO_HEADLESS=1 symfony php vendor/bin/simple-phpunit tests/Functional
```

And... woh! It popped up the browser then paused. *Now* we can view the page source.
Here's the CSS file. Open that. It's a 404 not found. Why?

In the dev environment, our assets are served through *Symfony*: they're not real,
physical files. If you prefix the URL with `index.php`, it works. Panther uses
the built-in PHP web server... and it needs a rewrite rule that tells it to send
these URLs through Symfony. Honestly, it's an annoying detail, but we can fix it.

Back at the terminal, hit enter to close the browser. In `tests/`, create a
new file called `router.php`. I'll paste in the code.

This is a "router" file that will be used by the built-in web server. To tell Panther
to use it, in `phpunit.xml.dist`, I'll paste in another env var:
`PANTHER_WEB_SERVER_ROUTER` set to `../tests/router.php`.

Try it!

```terminal-silent
PANTHER_NO_HEADLESS=1 symfony php vendor/bin/simple-phpunit tests/Functional
```

And now... it works! Hit enter to finish. Then remove the `pause()`.

Run the test again, but without the env var:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

## Waiting for the Turbo Page Load

Cool: it hit our screenshot line. Pop that open. Ok, we're back to the original
problem: it's not waiting for the page to load after we click the link.

Solving this... isn't as simple as it should be. Say `$browser =`, close that and
start a new chain with `$browser` below. In between, I'll paste in two lines.
This is lower-level, but waits for the `aria-busy` attribute to be added to the
`html` element, which Turbo does when it's loading. Then it waits for it to go
away.

Try the test now:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

Then... pop open the screenshot. Woh! It *is* now waiting for the Ajax call to
finish. But remember: we're also using view transitions. The page loaded... but
it's still in the middle of the transition. We'll fix that in a minute.

## Custom Browser & Base Test Class

But first, we need to clean this up: this is *way* too much work. What I would
*love* is a new method on the browser itself - like `waitForPageLoad()`. And
we can do that with a custom browser class!

In the `tests/` directory, create a new class called `AppBrowser`. I'll paste in
the guts.

This extends the normal `PantherBrowser` and adds a new method which those same
two lines.

When we call `$this->pantherBrowser()`, we now want it to return *our*
`AppBrowser` instead of the normal `PantherBrowser`. To do that, you guessed it,
it's an environment variable: `PANTHER_BROWSER_CLASS` set to `App\Tests\AppBrowser`.

To make sure this is working, `dd(get_class($browser));`. Run the test:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

And... yes! We get `AppBrowser`! Unfortunately, while the new method *would* work,
we don't get autocompletion. Our editor has no idea that we swapped in a
sub-class.

To improve this, let's do one last thing: in `tests/`, create a new base test
class: `AppPantherTestCase`. I'll paste in the content.

It extends the normal `PantherTestCase`... then overrides the `pantherBrowser()`
method, calls the parent, but changes the return type to be *our* `AppBrowser`.

Over in `VoyageControllerTest`, change this to `extend` `AppPantherTestCase`, then
make sure to remove `use HasBrowser`. Then we can tighten things up:
reconnect all of these spots... then use the new method: `->waitForPageLoad()`...
with auto-complete! Remove the `ddScreenshot()`... and let's see where we are!

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

Further!

> Form field "Purpose" not found.

So it clicked Voyages, clicked New Voyage... but couldn't find the form field. If
we look down at the error screenshot, we can see why: the modal content is still
loading! You *might* see the form in your screenshot - sometimes the screenshot
happens *just* a moment later, so the form is visible - but this *is* the problem.

## Disabling View Transitions

Oh, but before we fix this, I also want to disable view transitions. In
`templates/base.html.twig`, the easiest way to make sure view transitions don't
muck up our tests is to remove them. Say if `app.environment != 'test`, then
render this `meta` tag.

## Waiting for the Modal to Load

Anyway, back to our failure. When we click to open the modal, what need wait for
the modal to open - that's actually instant - but *also* wait for the `<turbo-frame>`
inside to finish loading.

Open `AppBrowser`. I'll paste in two more methods.

The first - `waitForDialog()` - waits until it sees a dialog on the page with an `open`
attribute. And, *if* that open `dialog` has a `<turbo-frame>`, it waits for that
to load: it waits until there aren't any `aria-busy` frames on the page.

In `VoyageControllerTest`, after clicking "New Voyage", say `->waitForDialog()`.
And now:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

So close!

> table tbody tr expected 2 elements on the page but only found 1.

That comes from all the way down here! What's the problem this time? Back to
the error screenshot! Ah: we filled out the form, it looks like we even hit Save...
we're asserting too quickly!

Remember: this submits into to a `<turbo-frame>`, so we need to wait for that
frame to finish loading. And we have a way to do this:
`->waitForTurboFrameLoad()`. I'll also add a line to assert that we cannot
see any open dialogs: to check that the modal closed.

Run the test one more time:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

It passes. Woo! I admit, that was some work, too much work! But I do love the
end result.

Tomorrow - for our final day - we're going to talk about performance. And unlike
today, things are going to quickly fall into place - I promise.
