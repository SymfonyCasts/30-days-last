# Css

Coming soon...

Welcome to day five, where it's time for us to add some CSS to our site. So how does
that work inside of the asset mapper system? Well, we do already have a assets styles
app dot CSS file. And there's nothing stopping us from going into our base study HTML
twig, and just adding a link tag for that link rel equals style sheet, then we can
use asset and then the logical path to that file, which is going to be styles slash
app dot CSS. We go over refresh and look at the page source. It is right there link
rel equals style sheet, and we have it and it works great and it's super boring.
However, if we remove this line and go and refresh the page, notice we still have
this blue background, that blue background is actually coming from our app dot CSS
file. And if you look at the page source, there is still an always was a link rel
equals style sheet for that CSS file. But over in base dot HTML twig, there's nothing
here. So how the heck is that being loaded? The answer is that's being loaded inside
the import map function. So here's how this works. And it's because it's being
imported from app dot j s on top. So importing a CSS file. This is probably something
you got used to with Webpack Encore, you import a CSS file, and ultimately, it's
rendered on the page as a link link tag. However, this is not something that
ECMAScript modules actually do. This is not something that works in a browser. The
only thing you can import in a browser are JavaScript files. So this should fail
spectacularly. spectacularly. However, it doesn't. So this is something we built on
top of asset mapper, that is a totally extra feature. And here's how it works. In
base that HTML twig, we say import map app that's called the entry point, we know
that app actually refers to our assets slash app dot j s file. So what asset mapper
does is it goes into this and finds all the import statements for JavaScript and CSS
files. For every CSS file it finds, it adds that as a link tag. And it's basically
just that simple. However, there is one kind of little complication that's actually
kind of interesting. If you go to the network tab, and I'll go to all search for app.
There we go. If you look at the app dot j s file, it actually does still have that
import statement. So if you think about it, when our browser executes this line, that
should still fail, it should go and download our CSS file, our CSS files, not a
JavaScript file. So it should fail with a syntax error. But it's not. The reason for
that is a little trick inside of asset mapper. When you import a CSS file, we add an
import map entry for it. So even though this starts with dot slash, our browser does
look to see if there's a matching path for that inside of our import map. And there
actually is. So because it finds this matching entry, it downloads this file, which
is not a real file. It's a totally fake file that does absolutely nothing. So it
makes that line not error out. Alright, to see how powerful this is, I want to create
a second CSS file to support my alien greeting. So let's say that when our alien
greeting code is executed, we also want to import a CSS file. So I'm gonna create a
new CSS file called alien greeting dot CSS. We'll make it body background of dark
green. And then over alien greeting that j s, we'll just import dot dot slash styles
slash alien greeting dot CSS. And when we go and refresh, we get a dark green
background. And it works like you expect, we have a second style sheet here. And we
have a second entry down here for alien greeting dot CSS, which points to do to
nothing. Now what gets really interesting is with JavaScript modules, you can also
load, load them asynchronously. So instead of loading a CSS or JavaScript file,
always, we can load it only when we need it. So I'm going to copy this. And let's
pretend that we only want to load that CSS file when in pieces equal to false. So
I'll say if not in piece, I'm gonna call set timeout here. And let's do it for four
seconds. So we'll do a little four second delay. And then I'm going to import that.
Except as soon as you import things not at the top of your file, you need to call
them as a function. Cool. So this time, when we refresh the page, blue background
234, and then we get the dark green background, it loaded it lazily. What's really
cool about this is, of course, there's no alien greeting link tag being output
anymore. So the way this works is that we actually wait for our browser to execute
this JavaScript line. And when it does, it looks for this in the import map, it finds
it in the import map. But this time, instead of just having a line that does nothing,
this line here adds a new link tag with rel equals style sheet and href set to our
alien greeting and actually adds that to the head of our page. And we can totally see
this. So over here, under the head tag, you can see here's our style sheet right
here. So if I refresh and real quick, go and open that see it's not there. And it
gets added. How cool is that? So as fancy as this is, I want to use tailwind for my
CSS. But doesn't tailwind require building a node? Nope. Let's get it set up and
style our site next.
