# Toast

Coming soon...

An important part of any functional beautiful site is a notification system. In
Symfony we often think of flash messages, success messages that we render near the
top of the page after the user successfully submits a form. And yes, that is what I'm
talking about. But just rendering them on the top of the page is not good enough for
me. Instead, I want to render them as rich, beautiful, toast-style notifications in
the upper left that disappear automatically with CSS transitions. But first, let's at
least render the flash messages. So on our CRUD controller, I'm already saving flash
messages when I submit the form, we're just not rendering them anywhere. So in the
templates directory, let's create a new flashes.html.twig. And for right now, I'm
just going to loop over the success flash messages with for message in
app.flashes.success, we'll add the end for. Now for the inside, I'm going to post a
very simple flash message, which is actually going to be fixed to the bottom of the
page by default. And then in base.html.twig, instead of rendering this somewhere near
our body block, I'm gonna put this all the way at the bottom of the page. I'm going
to say div ID equals flash container, and then curly curly include underscore
flashes.html.twig. Now this ID flash container is not important yet, but it will be
useful later when we talk about turbo streams. So let's see if this works, I'm gonna
hit Save. And there we go. It's in kind of a weird spot, but it is showing up. So to
get this to look nicer, we can go to flow byte. And here, if I search for toast,
actually have some nice examples for some different style toast notifications that we
can take advantage of. So I'm going to paste in some code that is heavily inspired by
these flow byte examples. So back in underscore flashes.html.twig, I'll paste over
that. And there we go. So nothing really changed. We're still looping over the same
collection. I'm still dumping out message. We've just got some nice markup around
this. All right, let's try it. I'll go to edit, save. Oh, and that is beautiful. It's
up in the upper right. It doesn't auto close yet. In fact, it doesn't close at all
yet. But that's great. That's just done with CSS. So let's hook up this close button.
It's pretty likely that we're going to need to close things from other places. So
let's create a standalone reusable stimulus controller for closing things. So in
assets controller, let's create a closeable controller.js file. I'll cheat and grab
the code from another controller and clear that out. Perfect. And what we have in
here is just a close method. That when it's called is going to remove the entire
element that's attached to this controller. Next up, over in underscore
flashes.html.twig, we will attach the controller to the top level element because
this is the element that we want to remove when we close it. Then down here on the
button, we'll say data-action equals closeable pound sign close. We don't need the
click because this is a button. So stimulus knows that we want this to trigger on
click. All right, let's try it. Hit edit. Hit save. It's there. It's gone. So just a
couple minutes, we have a rich toast notification system, but it's not cool enough.
So tomorrow we're going to make it fancier with an auto-close functionality in CSS
transitions.
