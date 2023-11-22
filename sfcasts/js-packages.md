# Js Packages

Coming soon...

Welcome to day four. We now know that we can write modern JavaScript modules, which
is a fancy word for writing import statements and export statements. We do that
entirely without a build system. Our browser just knows how to do that. Rejoice. So
what about using third party packages? Well, let's head over to npm.js.com. Let's
search for a very important package called JS confetti. Which is all about
celebrating, which is what we're doing in this tutorial. So down here it says you can
use yarn to install it. We are not going to do that. But we will skip right down to
its usage example. Copy that. Head over to our app.js and we will paste that in. Just
move this down there. Side note, import statements always go at the top of your file.
So you'll see me do that. If you don't do that and do something, I don't know, weird
like this, you can, but your browser is actually going to move this up to the top
when it executes it anyways. So we might as well just code it correctly. Alright, so
is this going to work? Probably not because we haven't installed anything. And in
fact, when we try it, we get an error, a very important error. It says failed to
resolve module specifier j s dash confetti. Relative references must start with
either slash dot slash or dot dot slash. So what this is saying is that your browser
found an import statement and has no idea how to load that file. So if an import
statement starts with dot slash or dot dot slash, then your browser knows how to
handle that it actually looks for a file relative to this file. Easy peasy. But if
there is no dot slash or dot dot slash, you have what's called a bear module. And in
this case, what your browser does is it looks in your import map. And you can see
right now our import map looks like it did before we don't have a j s confetti. And
that's why you get this error. So this is probably one of the most important errors
you're going to see when you're coding with modules inside of your browser. And it
will look a little bit different based on what browser using Chrome, Firefox, Firefox
has a slightly different error. But what this almost always means is that you're
trying to use a third party package. And to fix this, you just need to install that
package. How do we install that package? So glad you asked. Copy the package name,
spin over and run bin console import map, require and paste. That's it. Check this
out. Spin over celebration. It works mad refreshing. How does that work? Well, not
surprisingly, if you view the page source, we have a new entry inside of our import
map that points to some file on an asset slash vendor directory. Interesting. So when
we ran that command, it really did just one thing. It updated our import map dot PHP
file and added this entry right here. So behind the scenes, it went out and found
what the latest version was, and it kind of put that version right here. And because
we have a j s dash confetti item in our import map dot PHP file means that we're
going to have a j s confetti item inside of our import map on our page. Now, where
does that file actually live? It lives up here in a new assets slash vendor
directory. This works pretty much exactly like the normal composer vendor directory
in a project. So you can see here, here is the actual file that's being loaded. Now
two important things about this vendor directory. The first is it's ignored from get
so you can see slash assets slash vendor. Just like the composer vendor directory is
not something that you need to commit to your repository. This net and the next
question is then how do I get these like if I clone a project and there is no vendor
directory? How do I get it? So glad you asked. Let's delete that vendor directory.
And you know what, just try refreshing. We get an air the j s confetti vendor asset
is missing. Try running the import map install command. Don't mind if we do. So spin
over and run bin console, import map install. And just like composer install, that's
going to download whatever you need into your assets slash vendor directory. And it's
just going to work. That's it. Third party packages are available. They're solved.
They're easy. We don't even need a giant node modules directory. Alright, so next,
our site needs some CSS. So how does that work with asset mapper?
