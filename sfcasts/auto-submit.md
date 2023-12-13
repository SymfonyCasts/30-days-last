# Auto-Submitting Forms

Day 12 already? Over the next 3 days, we're going to work on one, big goal: transforming
this table into a rich data-table setup, with searching, column filtering, pagination,
all happening with beautiful AJAX. This is one of the parts I'm *most* excited
to dive into.

Our homepage *does* have a search. And there's nothing particularly special
about it. I hit enter to submit the form, the query parameter is in the URL, and it
filters the results. Naturally, thanks to Turbo Drive, it all happens via AJAX.

For our *first* trick, watch as we make the search update automatically as we type.
So we type and, without hitting enter, the list should update.

To do this, we're going to borrow a controller from a [30 Days of Hotwire repository](https://github.com/ilrock/thirty_days_of_hotwire).
This comes from a *fantastic* [30 Days of Hotwire](https://twitter.com/ilrock__/status/1631315562390519809)
challenge that someone from the Rails community did. I *love* this series and it
has a ton of good stuff. I highly recommend checking it out.

## The autosubmit Stimulus Controller

Anyway, I'm going to borrow this great "auto-submit" controller. It's dead-simple:
it gives us a way to submit a form... with optional debouncing. If I type really
quickly, I don't want to submit the form four times. I want it to wait for a slight
pause... and *then* submit. That's called debouncing. This waits for a 300 millisecond
pause.

So let's roll up our sleeves and get this into our app. Create a new file
called `autosubmit_controller.js`... then paste:

[[[ code('b6c0e8c72f') ]]]

Then head to the homepage to use it. Near the top... here's our search form. On the
form, add `data-controller"autosubmit"`:

[[[ code('41bb95feb9') ]]]

Notice I'm getting auto-complete on that. That's thanks to a Stimulus plugin I have
for PhpStorm.

Next, down on the input, say `data-action` equals `autosubmit#debouncedSubmit`:

[[[ code('50d8de3da6') ]]]

In the controller, you can call `submit` to submit the form immediately or
`debouncedSubmit()` to wait for the pause. And we don't need to include the
event name this time - like `input->` to listen to the `input` event. When you apply
a `data-action` to an `input`, a `button` or a `link`, Stimulus figures out which
event you want to listen to. Most of the time, life will be simple like this.

## Installing the Missing Package

Does it work? No! Because we have an error... an error that I hope will feel familiar!

> Failed to resolve module specifier `debounce`.

This comes from our code! Our copied code is using a `debounce` package... and
we don't have that installed! Cool! Copy `debounce`, spin over and run:

```terminal
php bin/console importmap:require debounce
```

Now it's in our project... and now the error is gone. Ready for the magic? Hey,
it's working! Just one request after I finished typing thanks to debounce!

The only bummer is that we're losing focus when it reloads the entire page. As a
workaround - this is *not* going to be our final solution - we can try putting
`autofocus`:

[[[ code('631e2f24a3') ]]]

This... *almost* works... except we're losing the cursor location: it puts us back
at the beginning. That's okay: we're going to solve this in a much better way soon.
And when we do, we're not even going to need the autofocus.

Tomorrow, let's make this richer by adding pagination and column sorting.
