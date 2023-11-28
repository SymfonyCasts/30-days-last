# Stimulus

Welcome to lucky day number 7. Today we're talking about Stimulus: a small,
easy-to-understand JavaScript library that lets us create super-organized code
that... just always works. It is one of my favorite reasons to use the Internet.

## Installing StimulusBundle

But even though Stimulus is a JavaScript library... Symfony has a bundle to help
us load it, get it set up, and use it. So, find your terminal and run:

```terminal
composer require symfony/stimulus-bundle
```

One of the most important things about StimulusBundle is its *recipe*. After it
finishes, run:

```terminal
git status
```

## The Recipe Changes

Oooh. It made a number of changes. The first is over here in
`assets/app.js`. On top - I'll remove that comment - we're now importing a new
`bootstrap.js`. *That* file starts the Stimulus application.

Notice that this imports an `@symfony/stimulus-bundle` module. The `@` symbol isn't
important: that's just a character namespaced JavaScript packages use. The
important thing is that this is a *bare* import, which means the browser will try
to find this package by looking at our importmap.

Ok! Open up `importmap.php`. The recipe added *two* new entries here. The first
is for Stimulus itself: that now lives in the `assets/vendor/` directory. The second
is... a kind of "fake" 3rd party package. It says that `@symfony/stimulus-bundle`
should resolve to a file in our `vendor/` directory. This is a bit of
fanciness: we say `import '@symfony/stimulus-bundle'`... and that will ultimately
import this `loader.js` file from `vendor/`.

The recipe also added a `controllers/` directory - the home for our custom Stimulus
controllers - and a `controllers.json` file, which we'll talk about tomorrow.

Oh, and in `base.html.twig`, it added this `ux_controller_link_tags()` line.
Delete it! That was needed with AssetMapper 6.3, but not anymore. We'll talk about
what that did tomorrow anyway.

## Using Stimulus

Ok: so, all we've done is `composer require` this new bundle. And *yet*, when we
refresh the page and look at the console, Stimulus is already working! This
"application #starting" and "application #start" come from Stimulus. That's awesome.

With StimulusBundle, anything we put into the `controllers/` directory will
automatically be available as a Stimulus controller. So, the fact that we have a
`hello_controller.js` means that we can use a controller named `hello`.

In fact, we can see it right now. When this controller is activated, it replaces the
text of the element it's attached to. To prove Stimulus is working, inspect
any element on the page... and hack in a `data-controller="hello"`.

When I hit enter, boom! It activates the controller.

## Creating a Custom Controller

That was fun, but let's create our own, *real* controller. Copy `hello_controller.js`
and create a new file called `celebrate_controller.js`. I'll remove the comments
and the connect method.

Here's the goal: when we hover over the logo, I want to call a method on the
controller that triggers the `js-confetti` library. Start by creating the method.
It could be called anything, but `poof()` sure is a fun name!

Head over to `app.js`, copy the `js-confetti` code and delete it. Pop that into
`celebrate` controller... and move the import statement to the top.

Cool! The last step is to activate this on an element. Do that in `base.html.twig`.
Let's see... here's the logo. Add `data-controller="celebrate"`. And to trigger
the action on hover, say `data-action=""`... and the suggestion is *almost* correct.
The format is, first: the JavaScript *event* that we want to listen to. Instead
of `click`, we want `mouseover`. Then `->`, the name of our controller, `#` and
the method name: `poof`.

That's it! Refresh and celebrate!!! Each time we `mouseover`, it calls the method.
You can see this liberally in the console.

Wow, so, as soon as we add a controller to the `controllers/` directory, it's
loaded and ready to go. Remember, all with no build.

## Lazy-Loading Controllers

But sometimes you might have a controller that's only used on *certain* pages...
so you don't want to force your user to download it immediately on *every* page.
If you have that situation, you can make your controller lazy. It's the best.

To do that, add this special comment above it: `stimulusFetch: 'lazy'`.
Yes, that *is* pretty crazy. But as soon as we do that, instead of downloading this
file on page load, it will wait until it sees an element on the page with
`data-controller"celebrate"`.

Watch: delete the `data-controller` temporarily. Then go over, refresh, and on the
network tools, if I search for `celebrate`, there's nothing there. If I search for
`confetti` - since our controller imports - `js-confetti`, that's *also* not there.
Those have *not* been downloaded.

Clear out your network tools. Then go up to the logo and hack in that `data-controller`.
We're imitating what would happen if we loaded some fresh HTML via AJAX and... that
fresh HTML includes an element with `data-controller"celebrate"`.

As soon as that appears on the page, go back to the network tools.
Two new items showed up! It noticed the `data-controller` and downloaded the controller
*and* `js-confetti`... since that's imported *from* the controller. And it works
brilliantly. I absolutely love this.

Keep this controller lazy, but back in `base.html.twig`, re-add `data-controller`.

One of the great things about Stimulus is that it's used by people all over the
Interwebs! And there are many pre-made Stimulus controllers out there to help us
solve problems. One group of them is called Symfony-UX. We'll dive into one of
its packages tomorrow.
