# Bonus: More on Flowbite

A bonus topic! Yeah, because I started to get questions - good questions - about
Flowbite. On day 5 we added Tailwind and I introduced Flowbite as a site where
you can copy and paste visual components. For example, you copy this markup,
paste, and boom! You have a dropdown. The classes are all standard Tailwind classes.

And so, I mentioned that you don't need to install anything. However, depending
on what you want, that's not the full story... and I confused people. So let's
fix that!

## Installing The Flowbite JavaScript

Beyond being a source to copy HTML, Flowbite itself has two other features.
First, it has an optional JavaScript library for powering things like tabs and
dropdowns: a little JavaScript so that when we click, this opens and closes.

We're *not* using this at Symfonycasts... and it doesn't play well with Turbo. At
least not out of the box. We prefer to create tiny Stimulus controllers to power
things like this. But, we *can* get the Flowbite JavaScript to work.

Grab that dropdown markup and zip over to `templates/base.html.twig`. Just
inside the `body`, paste. If we go over and refresh, you can see what I mean: it
just works. Well, *visually*. But if we click, nothing happens.

To get the Flowbite JavaScript, find your terminal and run:

```terminal
php bin/console importmap:require flowbite
```

This installs `flowbite` and it dependency `@popperjs/core`. It also
grabbed the Flowbite CSS file... which is only needed if you didn't have Tailwind
properly installed. Having it hanging around in `importmap.php` is harmless, but
let's kick it out before it confuses me.

To use the JavaScript, open `assets/app.js`. On top `import 'flowbite'`.

Ok, refresh and... it works!

But there are two... quirks. Check out the console. We have a bunch of errors about
modal and popover. If you use the modal component from Flowbite, it requires a
`data-modal-target` attribute to connect the button to the target. The problem is
that *we* have a modal *Stimulus* controller.... and we're using `data-modal-target`
to leverage a *Stimulus* target. Those two ideas are colliding. You would need to
work around this by using Flowbite's modal system or renaming your modal controller
to something else. The same is true for popover.

## Fixing Flowbite JS & Turbo

The second quirk is that, though the Flowbite JavaScript works right now, as soon
as we navigate, it breaks! Flowbite initializes the event listener on page load,
but when we navigate and *new* HTML is loaded onto the page, it's not smart enough
to reinitialize that JavaScript. That's why, in general, we write our JavaScript
using Stimulus controllers.

Flowbite *does* ship with a version of itself for Turbo... but it doesn't
*quite* work: it doesn't reinitialize correctly on form submits.

That's ok! We've got the skills to patch this up ourselves. Import
`initFlowbite` from `flowbite`. Then at the bottom, I'll paste in two event
listeners. Flowbite handles initializing on the first page load. Then
anytime we navigate with Turbo, this method will be called and will reinitialize
the listeners. Or if we do something inside a Turbo frame, *this* will be called.

Let's try it. Refresh. And... it doesn't work: Look: `initFlobite`. Typo! Fix
that then... ok. On page load, it works. And if we navigate, it *still* works.

## The Flowbite Tailwind Plugin

So the first installable feature of Flowbite is this JavaScript library. The
second is a Tailwind plugin. It adds extra styles if you use tooltips, forms, and
charts.... as well as a few other things. You can find the package on npmjs.com
and navigate its files to find the plugin: `plugin.js`.

If you're using tooltips, it adds new styles, same thing for forms... then *all* the
way at the bottom, it tweaks some theme styles. This isn't necessarily something
that you *need*, even if you're using some of the JavaScript from Flowbite.

But if you *do* want this plugin, you need to install it with npm. So far, we haven't
had to do *anything* with npm... and that's been great! But if you *do* need
a few JavaScript libraries, that's ok: that's npm's job. The most important
thing is that we don't have a giant build system: we're just grabbing a library
here or there that we need.

Find your terminal and run `npm init` to create a `package.json` file.

```terminal-silent
npm init
```

I'll hit enter for all the questions. Then run:

```terminal
npm add flowbite
```

To use this, open `tailwind.config.js`... here it is. Down in the `plugins` section,
`require('flowbite/plugin')`. This is straight from their docs.

Whe we refresh, it works... but we don't see any difference. Like I said, it's not
something that we *necessarily* need. Though if you open a form, huh: our labels
are suddenly black! That's because Tailwind now thinks we're in light mode... and
I was a bit too lazy to style my site for light mode.

By default, Tailwind reads whether you want light mode or dark mode from your
operating system preferences. But Flowbite overrides that and changes it to read
a `class` on your `body` element. It has documentation on their site on how you can
use this and even make a dark mode, light mode switcher.

But I'm going to change this back to the old setting. Say `darkMode`, `media`.

Check it: refresh and... we're back to normal! So that's the Tailwind plugin.

## The Datepicker

In addition to these 2 Flowbite features, I've also seen people wanting to use
their cool datepicker plugin. So let's get that working!

This datepicker is part of the main `flowbite` library. But if you want to import
it directly from JavaScript... then, down here, you're supposed to install a different
package. This confused me to be honest. But copy that, spin over and run:

```terminal
php bin/console importmap:require flowbite-datepicker
```

Back at the top of the docs, it says that you can use the datepicker simply by
taking an input and giving it a `datepicker` attribute. And that's true... except
once again, it won't work with Turbo. It'll work at first... but stop after the
first click.

Instead, we're going to initialize this with a Stimulus controller, and it's going
to work great!

In `assets/controllers/`, create a new `datepicker_controller.js`. I'll paste in
the contents. We're going to attach this controller to an `input` element. In
`connect()`, this initializes the date picker and passes `this.element`. The
`format` matches the default format that the Symfony `DateType` uses. And
`autohide` makes the date picker close when you choose a date, which I like.

I'm also changing the `type` attribute on the `input` to `text` so that we don't
have both the datepicker from Flowbite *and* the native browser date picker.
In `disconnect`, we do some cleanup.

We're going to use this on the voyage form: for "leave at". Open the form type
for this: `VoyageType`. Here's the field. Pass an `attr` option with `data-controller`
set to `datepicker`.

Let's try this! Refresh and... that's fantastic!

## Fixing the Datepicker in a Modal

Though... there's a catch. Go back and open this form in the modal. It doesn't work!
Well, it kind of does. See it? It's hiding behind the modal. The datepicker
works by appending HTML at the bottom of the `body`. But because that's
not inside the `dialog`, it correctly appears *behind* the modal. It's kind of a
shame that it doesn't work better with the beautiful native `dialog` element, but
we can fix this.

In `datepicker_controller.js`, add a new option called container. This tells
the datepicker which element it should add its custom HTML *into*. Say
`document.querySelector()` and look for a `dialog[open]`. So if there's a `dialog`
on the page that's open, then use that as the container. Else use the normal
`body`.

## Making the Modal Click Outside Smarter

And *that* little detail takes care of our problem! Though... it does expose one
other small issue. See how the datepicker extends the dialog vertically? If we
click here, we're technically clicking on the `dialog` element directly... which
triggers our click outside logic.

To fix that, let's make our `modal` controller just a *bit* smarter. At the
bottom, I'll paste in a new private method called `isClickInElement()`. If you
pass this a click event, it will look at the physical dimensions of this element
and see if the click was inside.

Up here in `clickOutside()`, let's change things. Copy this, then if
the `event.target` is *not* the `dialog`, we're definitely not clicking outside.
So, return.

And if not, `this.isClickInElement()` - passing `event` and `this.dialogTarget` -
so if we did not click inside the `dialogTarget` - then we definitely want to close.

A bit more logic, but a bit smarter. Try it. Open the modal and if
we click down here... the calendar closes - which is correct - but the modal stays
open. Love that!

So I hope that explains Flowbite a bit more. Personally, I don't want most of this
stuff, so I'm going to remove it. Inside `tailwind.config.js`, remove the plugin...
then delete `package.json` and `package-lock.json`.

I also don't want the JavaScript. In `importmap.php`, remove `flowbite` and
`@popperjs/core`. But that datepicker is cool, so let's keep that.

In `app.js`, remove the import from `flowbite` and the two functions at the bottom.
Finally, in `base.html.twig`, get rid of that random dropdown.

Now... no more JavaScript errors! But because that datepicker was pretty cool,
we still have it.

Ok, bonus chapter done! Now back to work - seeya later!
