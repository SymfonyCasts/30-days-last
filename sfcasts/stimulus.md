# Stimulus

Coming soon...

Welcome to Day 7, which is all about Stimulus, a small, easy-to-understand JavaScript
library that allows us to create super-organized JavaScript that just always works.
It is one of my favorite reasons to use the Internet, honestly. So, Stimulus is a
JavaScript library, but Symfony has a bundle to help us load the JavaScript, get it
set up, and use it. So, find your terminal and run compose-require
Symfony-stimulus-bundle. One of the most important things about Stimulus Bundle is
actually its recipe. So, after it finishes, I'll run git status, and you can see that
it did a number of different changes to our code. So, the first is over here in
assets-app.js. On top, I'll remove that comment, it's importing a new bootstrap.js.
And bootstrap.js actually starts the Stimulus application. Now, notice it imports
this at Symfony-stimulus-bundle. This at symbol is not important, that's just a
character that's used to namespace packages. So, this is a bare import, the kind of
thing we use when we're referring to third-party packages. But in this case, it's not
actually a third-party package. If you open up import-map.php, the recipe also added
two new entries here. The first thing is it added a true third-party entry for
Stimulus itself. So, that now lives in our assets-vendor directory. The second thing
it did is added kind of a fake entry in here. So, this says that at
Symfony-stimulus-bundle is actually pointed to just a file in our vendor directory.
So, there's a little bit of fanciness going on here. We can say import at
Symfony-stimulus-bundle. And that's actually just loading this loader.js file from
our vendor directory. The recipe also added a controllers directory and a
controllers.json file. The controllers directory is going to be where our Stimulus
controllers live. And the controllers.json file, you can ignore that for now. We'll
talk about that tomorrow on day eight. One other change this made was in
base.html.twig. It added this UXControllerLinkTags, which I want you to delete. That
was needed with AssetMapper 6.3. It's not needed anymore. Remove it. We'll talk about
what it did tomorrow anyway. All right. So, all we've done is compose, reacquire this
new bundle. And yet, when you refresh the page and go look at your console, Stimulus
is working. This application starting and application start is coming from Stimulus.
And it tells us it's actually running. That's awesome. Now, with our setup, anything
we put into the controllers directory is going to be available as a Stimulus
controller. So, the fact that we have a hello underscore controller means that we are
allowed to use a controller called hello. And, in fact, you can see right now this
controller just replaces whatever element it's attached to with some text. So, to
prove this is working, we can actually just inspect element on any element on our
site. And I'm going to hack a little data-controller equals hello onto here. Check
this out. When I hit enter, boom, it loads and activates our controller. That is so
cool. Now, the way that the controllers are executed is a little bit magic. Do I even
want to go into that? No, I don't. So, let's hook up our own custom controller. I'm
going to copy hello controller and create a new one called celebrate controller. I'll
remove the comments and the connect method. And instead of that, we're going to
create our own special method called poof. The goal here is that we are going to,
when we hover over this logo, I want the JS confetti poof thing to happen. So, we're
going to start by creating a poof method. That could be called anything. You'll see
how that's called in a second. And then go to app.js. There we go. Let's copy our JS
confetti code. I'll delete that. And pop that into celebrate controller. And then
make sure you move that import statement to the top of the file. Cool. To activate
this somewhere, we just need to add data-controller equals poof. Let's do that in
base.html twig. Let's see. Awesome. So, here's our logo. So, on here, I'm going to
add data-controller equals celebrate. And then data-action equals, and that's almost
correct. So, the format here is, the first thing here is going to be which JavaScript
event we want to call the method on. So, is it click? Is it? In our case, it's going
to be mouse over. And then you do an arrow, and then you do the name of your
controller, and finally the name of your method, which is poof. That's it. So,
refresh, and we've got it. Celebrate. Every time I hover over that, it's calling that
method, and you can see it inside of our controller. Wow. So, as soon as you add a
controller in your controllers directory, it's going to be loaded on every page. Now,
sometimes you might have a controller that's only used on some pages, so you don't
want to force your user to download it on every page. If you have that situation, you
can make your controller lazy. It's super cool. So, you just add a little comment on
top. I know, it looks a little crazy. Call it stimulus-fetch-lazy. Now, as soon as we
do that, instead of downloading this file on page load, it's going to wait until it
sees an element on the page that has data-controller-celebrate. So, check this out.
I'm going to delete that temporarily. I'm going to hover over refresh, and on the
network tools, if I search for celebrate, there's nothing there. If I search for
confetti, since we are importing JSConfetti, that's also not there. So, I'm going to
clear out my network tools. Let's go up to our logo here, and I'm going to hack in
that data-controller. I'm imitating what would happen if we loaded some fresh HTML
via AJAX. Maybe we load some fresh HTML via AJAX, and it includes an element that has
data-controller-celebrate. So, as soon as that appears on the page, if we go back to
our network tools, boom! We have two new items show up. It noticed that element, it
downloaded the controller, it downloaded JSConfetti, since that's imported from the
controller, and it works. Oh, I absolutely love that. So, I'm going to go, I'll keep
this as lazy, but back in base.html, I'll re-add data-controller. So, one of the
great things about Stimulus is that it's used by lots of people on the internet, and
there are many pre-made Stimulus controllers out there to help us solve problems. One
group of them is called Symfony-UX. Let's add an autocomplete select package next.
