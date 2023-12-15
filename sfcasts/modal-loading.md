# Fantastic Model UX with a Loading State

Coming soon...

All right, so
let's try to submit this and something goes wrong. It went to a page that didn't have
a TurboFrame ID = modal. And that's because the response was an error. If we look
down here on the web debug toolbar, there was a 405 status code. Let's open that up.
So it says no route found for post /voyage, which is weird because we're submitting
the new voyage form. But here's the problem. When you generate, when I generated the
crud for my voyage system, it generated form elements that don't have an action
attribute. That's fine when you were on that page, because it means it submits right
back to the same URL. But as soon as you start embedding your forms in other places,
you need to be responsible and always set the action attribute to do that, open up
our src/Controller, the voyage controller. And at the bottom of this, I'm going to
paste in a really simple private method. That's going to handle this for us. Okay. To
add the use statement. So we can pass a voyage or not pass a voyage. And all this
does is create our normal voyage form, but it sets the action attribute. If the
voyage has an ID, it sets the action to the edit page else it sets it to the new
page. So thanks to this up in the new action, we can say this->create voyage form
voyage, and I'll copy that line because we need the exact same thing down here in
edit. Perfect. All right. So now I don't even need to refresh. I'm just going to open
the modal, save, and it works. It's submitted. And we got the response right back
inside the modal because of course that's the whole point of a turbo frame. It keeps
the navigation right inside this area. Now, before we talk about what happens on
success, let's perfect this. My second requirement for the opening the modal was that
it needed to open immediately over in our new action. Add a sleep = two to mimic our
site being so cool that it's being slammed by traffic.  Now, when we hit the button
click, nothing happens. No user feedback at all until the Ajax request actually
finishes. It's not good enough. Instead, I want the modal to open immediately with
some loading content. To do this over in the modal controller, we're going to add a
new target called loading content. So the idea is that in our actual HTML in a
second, we will define the, what we want the loading content to look like inside of
our modal. Then down here, we're going to create a new method called showLoading. And
if this.dialogTarget.open, so if we're already open, we don't need to show the
loading, I'm just going to return. Otherwise say this.dynamicContentTarget. So in our
case, this is going to be the turbo frame that we're waiting to load the Ajax into.
.innerHTML = this.loadingContentTarget.innerHTML. So it's going to copy whatever
we've defined as our loading content into our dynamic content. Finally, let's add
that target. So in base.html.twig, after my dialog, I'll add a template element. Yes,
my beloved template element. It's perfect for this situation because anything inside
of a template element isn't visible on the page. Let's add a data modal target =
loading content. And then inside of this, we're going to add a template element that
we're going to And then inside of this, I'm going to paste in some content. This is
just some tailwind classes that are going to give us this nice pulsing animation.
Now, if we tried this right now, it still doesn't work because nothing is calling our
new show loading method. And this is where we can use a little trick from a turbo. So
over in base.html.twig, find the iframe. I'm going to move this on to multiple lines.
What we effectively want to do is the moment the turbo frame starts loading, we want
the loading content to show. And turbo dispatches an event when it starts an AJAX
request that we can listen to. So add a data action to listen to that and we'll say
turbo before fetch request. That's the name of the event. Then->modal pound sign show
loading. All right, let's check out the effect here. I'm going to refresh the page
and, oh, I love that opens instantly. We see that loading content. It's perfect.  And
it's really cool how it works. When this calls show loading, show loading puts
content into our dynamic content target. And do you remember what happens? The moment
any content goes into a turbo frame that triggers the modal to open. So there's some
really cool teamwork going on here to get that modal to open immediately. And then
once the turbo frame actually finishes loading, its final content overrides the
loading. All right, so we're almost there to making this perfect, but I'm not
satisfied yet. And while I have that sleep in there, I'm going to hit save. Look it,
nothing happens. There's no feedback while that's loading. Fortunately, that's easy
to fix, and we've already done it on a different turbo frame. I'm going to add class
area-busy:opacity 50, and then transition opacity. This time, let's reload this.
Actually, I should reload the page. There we go. And we've got the nice loading
animation. And yes, we get the low animation. Low opacity, so we can see that it's
doing something. All right, so with that, I will happily remove our sleep. At this
point, there's just one last detail I want to talk about, and it's this extra padding
here. The reason that extra padding is there is because we're going to be using it to
edit the page. So the reason that extra padding is there is because the content from
the page has an M4 and a P4 that adds that. So the modal has some padding, and then
there's extra padding coming from that on the page. And on the page, that margin and
padding makes sense. So this comes from over here in new.html-twig. And when that
same content is inside of the modal, we want that to hide. So to help us do this,
we're going to add, we're going to use a tailwind trick. So it's really easy for us
to customize this. So in tailwind.config.js, let's add one more variant. We're going
to call this one modal, and it's going to activate whenever we are inside of a dialog
element. Thanks to that, over in new.html-twig, we'll keep the margin for and the
padding for in normal situations. But if we're in a modal, use M0, and if we're in a
modal, use P0.  All right, so over here on this page, there shouldn't be any change.
That looks good. But now, oh, look at that. That is the modal padding and margin we
want. So that's it. With a fully reusable modal system, it loads instantly, it
submits into it, and it even closes itself when the modal, when the content
disappears. Watch. First voyage from a modal. And I'll choose this. Let's close this
for dramatic effect and save. Look at that. It closed. How? The new action redirects
to the index page, and the index page doesn't extend modal base. It extends the
normal base.html-twig. So there is a modal frame on the index page, but it's the
empty frame at the bottom of the page. So that means that the modal lost its content.
What happens when the modal loses its content? The modal is smart enough to close
itself. Now, the only thing we are missing, if you're watching closely, is there is
no toast notification. So tomorrow, we'll talk all about handling form success when a
form is submitted inside a frame. By the end, we're even going to move the edit into
a modal and add a few other effects that are simply amazing.
