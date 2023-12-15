# Fancier Toasts: Auto-close & Fading

Thanks to our work yesterday on day 16, we have a nice Toast notification system
that's powered entirely with CSS. Ok, just a *tiny* bit of JavaScript to, boop, close
it.

Today we're going to take this to the next level. I want these toasts to be amazing.

## Adding Auto-Close

The first feature we'll add is auto-close: it's pretty common for toasts to close
themselves after a few seconds. But I also want to keep our closeable controller
reusable. There may be other parts of the site where we want to be able to close
something... but not have it close itself automatically.

So, we need a way to *activate* the auto-close on a case-by-case basis. The way
to pass info into a controller is via values. Add `static values` equals... and I'll
invent a new value here called `autoClose`, which is going to be a `Number`.

Now add a `connect()` method. The idea is that if we have `this.autoCloseValue` -
that's who you reference this value - then that's actually perfect! We'll use
`setTimeout` to close after that many milliseconds.

Next up: go to our usage of this controller in `_flashes.html.twig` and pass in
the new `autoCloseValue`. We do that on the same element as the `data-controller`.
Add `data-closeable-auto-close-value` equals and use 5,000 for 5 seconds.

The format is `data-` the name of the controller - `auto-close` - that's the name
of the value `autoClose`... but because we're in an HTML attribute, we use the
"dash case" - then the word `value` equals and finally what we want to pass in.
This format here is a bit harder to remember than just `data-controller`. But as
you saw, if you have this Stimulus plugin for PhpStorm, it auto-completes it, which
helps a lot.

Let's do this. Edit this record, save and 1, 2, 3, 4, 5. Gone!

## Auto-close Timer Bar

What's next on our quest for an ultra rich toast? What a about a timer bar that
shows when the toast will close? A little bar that animates smaller and smaller then
finally disappears right as the toast auto-closes itself.

That sounds cool! Here's the plan: we're going to add an element down here then
animate its width from 100% to 0% over those 5 seconds. To be able to *find* that
element, inside the controller, we're going to use a target. Add
`static targets = ['timerbar']`.
 
Then down in `connect()`, check for that: if `this.hasTimerbarTarget`, then
`this.timerbarTarget.style.width = 0`.

Assuming we've added a CSS transition to this element, that should animate the
change from full width to 0. Oh, but one other detail: add a `setTimeout` and put
this inside with a 10-millisecond delay.

This will allow the element to *establish* itself on the page with a full 100% width,
before changing it to 0. If you add or unhide an element and *immediately* change
a CSS property on it - like its width - the CSS transition won't work. You need to
let the property *be* on the page with 100% width for 1 animation frame, *then*
change it. It's a tricky part of transitions.

Ok, we're good here, so let's add the timer bar itself. At the bottom of
`_flashes.html.twig`, I'll paste it in. This has an absolute position on the bottom,
left of the parent with a height and green background. It also has an explicit
width: that's the `w-full`. That's important for the transition.

To make this a target, add `data-closeable-target="timerbar"`.

Ok! Let's see what this looks like. Hit edit, save, and it opens... but no animation.
Let's do some debugging! No errors in my console. Ah... here's the problem: I should
have listened to my editor: `timerbarTarget`.

Let's close this. Save and... *that's* what I want to see! And right as it gets to
0, boop, it closes.

Ok, I *love* how this is looking. But there's one last detail that I think our
toast deserves: no matter how it closes, I want it to fade out instead of, boom,
happening automatically.

## CSS Transition on Close

Fading things out is a bit tricky. You can use CSS transitions - and we will - to
go from opacity 100 to 0. But then you also need some JavaScript to *wait* for that
CSS transition to finish so that it can finally remove the element from the page
or at least set its display to hidden.

To help us with this, we're going to use a library called `stimulus-use`. Stimulus
Components - as we saw earlier - are a list of reusable stimulus controllers.
`stimulus-use` is a group of *behaviors* that you can add to your Stimulus
controllers. And there's a lot of interesting stuff here.

The one we're going to use is called `useTransition`. So step one, let's get this
installed. Run:

```terminal
php bin/console importmap:require stimulus-use
```

Awesome! Then over in the controller, import that with
`import { useTransition } from 'stimulus-use'`.

The activate a behavior, you call the it from `connect()`: `useTransition(this)`
then pass any options you need. I'll paste a few in.

Here's what this means. While this element is "leaving" or hiding, the library will
add these three classes to the element. That will establish that, in case any
CSS properties change on this element, we *want* to have a 200 millisecond transition.
The `leaveFrom` means that, at the moment it *starts* hiding, the library will give
it this class: setting its opacity to 100. Then, one millisecond later, it will
remove this class and add `opacity-0`. That change will trigger the 200 millisecond
transition. Finally, `transitioned` true is a way for us to tell the library that
we are *starting* in a visible state... because you can also use this library to
start hidden and then transition *in* your element to visible.

Now that we've initialized this behavior, our controller magically has two new
methods: `leave` and `enter`. So down here in `close`, instead of removing the element
ourselves, say `this.leave()`.

Let's try this! Spin over, refresh, and save. Watch. Ah, it was quick, but that is
*exactly* what we want to see! Our toast notification is polished and done.

Tomorrow: we're going to explore the third and final part of Turbo: Streams. These
are the Swiss army knife of Turbo and will let us solve a whole new set of problems.
