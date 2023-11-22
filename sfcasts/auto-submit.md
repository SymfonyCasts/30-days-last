# Auto Submit

Coming soon...

Welcome to day 12. Over the next few days, we're going to transform this table into a
rich data table setup with searching, column filtering, pagination, all happening
with beautiful AJAX, while we're going to write zero JavaScript. This is one of the
parts of this tutorial that I am most excited to get into. So right now, our homepage
does have a search, see, I can... And there's nothing particularly special about it.
I'm hitting enter to submit that, I have the query up there, and it's shortening the
results. Cool. And naturally, thanks to Turbo Drive, it's at least happening via
AJAX. For our first trick, I want to have the form search automatically as I type. So
when I just type this, without hitting enter, I want that to update the list below.
To do this, we're going to borrow a controller from a 30 Days of Hotwire repository.
This is a repository that comes from a really great 30 Days of Hotwire that someone
did from the Rails community. I love this series, there's tons of good stuff in here,
doing lots of crazy cool tricks with just Stimulus and Turbo. So I highly recommend
checking this out. In this case, I'm going to borrow this great auto-submit
controller. This controller is very simple, it just gives us a way in Stimulus to
submit a controller or submit a form. But it does it with also... But it does it in a
debounced way. So one of the requirements is that if I type this really quickly, I
don't want that to submit the form four times, I want it to wait for a slight pause,
and then submit. That's what debouncing is, and this waits for a 300 millisecond
pause. So let's get this put into our app, I'll call it autosubmit-controller.js,
we'll paste that in there. And then head to the homepage so we can use this. So up
near the top, here we go, here's our search form. So on the form, we'll add
data-controller equals autosubmit. Notice I'm getting auto-complete on that, that's
thanks to a Stimulus plugin that I've installed for PhpStorm. And then down here on
our input, we're going to say data-action equals, and we'll say
autosubmit-debounced-submit. So the D inside of our controller, you can call submit
to submit immediately, or you can call debounced-submit, where they have that 300
millisecond debounce worked into it. And in this case, we don't need to say anything
like change arrow, or in this case, input arrow, that's the name of an event. Because
when you apply a data action to an input or a button or a link, Stimulus
automatically figures out which event you want to listen to. So we just need to have,
so most of the time with Stimulus, you're just going to have the controller name, and
then the method name like this. So when we try this, it doesn't seem to work, and
that's because it isn't working, because we have an error, an error that I hope will
feel familiar, fail to resolve module specifier debounce. This is coming from our
code, because it has no idea how to load this debounce, because we haven't added this
to our project. So copy debounce, spin over, on bin console, import map require,
debounce. Now it's in our project. Now the error is gone, and let's try this out,
ready? It's working. So one request after we type the debounce is there. The only
bummer is that we're losing focus when it reloads the entire page. As a workaround,
this is not going to be our final solution yet, we can try putting autofocus. Hey,
focus this input, every time the page loads. This seems to work, except we're losing
our location inside of there. When it resets, it puts us back in the beginning.
That's actually okay, because we're going to solve this in a much better way soon.
And when we do, we're not even going to need the autofocus on there anymore to make
it work. But next, let's make this richer by adding page nation and column sorting.
