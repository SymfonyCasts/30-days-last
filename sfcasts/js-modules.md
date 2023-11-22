# Js Modules

Coming soon...

So if you inspect element on this page and head over to your console, we've got a
little console that log in there says it's coming from assets slash app digest. And
sure enough, if we spin over and go to assets slash app digest, we have the log right
there. The more interesting thing is, how is this file being loaded? So to answer
that view the page source, and there's some interesting stuff going on here. But I
want you to zoom in down here to this script type equals a module import app. It
turns out that all modern browsers basically everything except for IE 11. And you
should not be supporting IE 11 anymore. Nobody does unless you work for some bank.
Turns out that all browsers now support JavaScript modules. And what a JavaScript
module means, it's just basically that system where you're able to use import and
export statements in your JavaScript, you've probably gotten used to doing that in
Webpack Encore. Well, it turns out browsers can just do that. If you open any HTML
page and say script type equals module, then the code inside of here can just be
normal JavaScript using import and export statements. So the second question is, what
the heck is app? How does how does it know to to map app over to this asset slash app
dot j s file. This is also a new trick of browsers called import maps. So this has
nothing to do with symphony, nothing to do with asset mapper. This is just how
browsers work. If on your page, you have a script type equals import map, this
becomes like a key value map that's used by your browser when it loads modules. So if
we say import app, it actually looks inside of this list sees app there. And this is
actually the file that it opens to that, which happens to be the file that's being
served by asset mapper. So it's all this nice little teamwork together that's doing
this. This import map functionality is also something that all modern browsers
support, it has slightly less support than the actual JavaScript modules. But
fortunately, there is a shim or a polyfill. So that if you your user does happen to
have a browser that doesn't support import maps, that will take care of that it will
work, you don't need to worry about it. So the second thing is, where the heck is
this all coming from? This is coming from templates slash base dot html twig. And
it's entirely coming from this one line right here, import map app. So this, of
course, because we passed app there, this is going to generate a script type equals
module import app. But this is also dumping the polyfill a couple of things that are
less important. And of course, the import map. And the import map is primarily though
not entirely, we'll get to that, but primarily generated from this import map dot PHP
file. So when we installed asset mapper, it gave us this file. And this is the reason
that we basically have an app key inside of our import map, pointing to our assets
slash app dot j s file. So by the way, this, when we talk about JavaScript modules,
these are called, they're technically called ECMAScript modules. And one of the
things you'll see commonly is the acronym ESM, ECMAScript module. So if you see as
ESM, that's just a fancy way of saying code that uses import and export statements.
And it's really not any more important than that. So I want to play a little bit with
this new system. Because this is really cool. The fact that we can use import and
export statements just in our browser, that's a really big key for us not needing a
build system. So inside the assets directory, we can organize this however we want.
I'm going to create a lib directory with an alien greeting dot j s file. And it's not
here, I'm gonna write some awesome new JavaScript. So experts fault function, let's
have a function of a message argument, we'll have an in piece argument equaling
defaulting to false. And then I'm just going to put a console dot log in there even
use these fancy little tick things. Then I'll use some fun emojis. And close that
out. By the way, you do see a little bit of like auto completion happening. And here
I am for the first time ever, actually leaving co pilot enabled, because it's just so
important for coding. I want this tutorial to be as real as possible. So great. This
is now a file that we've created. It lives in the assets directory. So technically,
it's publicly available to the user, but nobody is actually using it yet. But we can.
So something we can do, we won't do this often. But just to prove that we can, we can
go right into our base layout anywhere. And we can say script type equals module. And
inside here, whoops. It's a import alien greeting. I'll hit tab on there. And from
and notice it puts this dot dot slash assets, that's not actually going to work. But
what will work is we can use the asset function, we can point to its logical path,
which is going to be a lib slash alien greeting dot j s. And then down here, let's
try to use that say alien greeting. And we will not come in peace. So cool. So let's
see this works. Close that. And it doesn't check this out. I actually thought that
what would work, we get a 404 down there for lib slash alien greeting dot j s. Now it
works, give us all your candy. So just writing normal JavaScript just works. And one
thing I do want to point out when you view the page source is if I refresh this I
don't want to do that is that of course, we get this nice version file name when we
do our import. So you can import just things like app and rely on the import map, but
you can also just import entire paths like that. And that works fine, too. It's super
duper friendly. Alright, so in reality, we're not usually going to be just writing
code in line like this, I'm going to copy this code, get rid of that script type
equals module. And we're going to going to go into app dot j s. And we're going to
put the code right there. And in this case, when we refer to the path, we're just
gonna write normal JavaScript, which means we're gonna say dot slash lib slash alien
greeting dot j s. This is me the exact same code that you have in Webpack Encore.
With one difference, I will point out with Webpack, you don't have to have the dot j
s on there. Turns out that's like a node specific thing. And sort of real JavaScript,
you do need to have the extension on there. So you do need to add the dot j s. But it
works. And by the way, if you were ever using autocompletion, like if you want
phpStorm to autocomplete that for you can actually go into your settings. And let's
see here. We go to editor extensions. I just searched that editor. There we go
editor. code style, JavaScript. And right here, if you just change this use file
extension to always then it's going to like autocomplete with the dot j s instead of
without it, which is kind of nice. So yeah, we can just kind of code like normal
JavaScript and asset mapper takes care of all of that for us. And one thing that we
kind of already saw, I don't need to do that. All right, so next, let's talk about
bringing in third party packages.
