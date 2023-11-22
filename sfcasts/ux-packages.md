# Ux Packages

Coming soon...

Head over to ux.symfony.com. This is the site for the Symfony UX Initiative, which is
really a group of PHP and JavaScript packages that give you free stimulus
controllers. So there's a stimulus controller that can render chart.js, there's a
stimulus controller that can add an image scrapper, and so on. What we're going to
focus on today is this autocomplete one. So very simply, you can see down here, this
adds a nice little searchable autocomplete type of thing that you can add to a select
element. It's just that nice. On our site, if we go over to the voyages and hit edit,
we have a little planet dropdown, which is fine, but this could use a little extra
fanciness. So we're going to add it right there. So let's get this package installed.
So I'll scroll up, copy that, copy the compose require line, and paste. And then when
that finishes, I'm going to clear it and run git status. And there's two interesting
things that have modified here. It's controllers.json and import map.php. We know
that everything in the assets controllers directory is going to be available as a
stimulus controller. In addition to that, anything in our controllers.json is also
registered as a stimulus controller. So the recipe added this entry right here, which
basically means that it's looking in the symphony UX autocomplete PHP package we just
installed, and it's registering a stimulus controller called autocomplete. The end
result of this code is that we now have a third stimulus controller in our
application. The other thing that the recipe did is down here on import map.php,
added a new vendor entry for Tom select. So Tom select is a JavaScript package. And
that's actually what does the heavy lifting of adding all the autocomplete. So this
stimulus controller is just a small wrapper around Tom select. And so since we need
Tom select, it was added here. Now when we refresh the page, we are greeted with a
lovely error. It says the auto import Tom select dot default dot CSS could not be
found in import map.php. Try running import map require and then that path. So if you
look back into that controllers.json, sometimes these controllers have an extra
section called auto import. So the idea is that sometimes a JavaScript controller, a
stimulus controller might have a CSS file that goes along with it. This section
allows you to activate or deactivate those CSS files. So for example, with Tom
select, there's a default one, or if you're using bootstrap, you can use the
bootstrap five one. So for example, we can set this to false in this set to true if
we are using bootstrap. Now, one difference between using JavaScript modules in a
browser and an environment like webpack is that with something like webpack, when you
install a Tom select package, you get the entire package. So you get the JavaScript
files, any CSS files, the whole thing is there. So in this system, when you install
Tom select, that actually installs just a single file, just the JavaScript file. So
the CSS files are not there. However, one really cool thing about this system is that
when you run import map require, so bin console import map require, you can import
map require a package name like this. Totally cool. But you can also import a
specific file path within that package. And of course, with asset mappers, since we
support CSS files, you can also that path can be to a CSS file. So that's a long way
of saying that we need this vendor file. So we're going to run bin console import map
require and paste that. And now we have it. So over in our assets vendor directory,
you can see we have that CSS file. And in our import map dot PHP, we have it right
there. So it's now available for the stimulus controller to import. And the end
result is it works. And when you view the page source, you can see we have the CSS
file right there. All right, let's add this stimulus controller to our code. So as I
mentioned, UX packages are usually a mixture of PHP and stimulus controllers. So in
this case, what the PHP code allows us to do is go to source form voyage type dot
PHP. And this is a entity type on any entity type or choice type, we can say
autocomplete. True. As soon as we do that, boom, okay, the styling is not right yet.
But what that does is it activates a stimulus controller on that element, you can
even inspect element and see it. So here's the select that was rendered on the page.
And here you can see data dash controller. And then it has a kind of an ugly long
name to the controller, but that's the controller being activated. And then Tom
select is actually adding all this extra markup down there. So it works, it works
beautifully. It just doesn't look quite right yet. So this is where the auto import
comes into play. It is bringing in this Tom selected dot default dot CSS, which makes
it at least look somewhat okay. But it's not really styled for our dark mode yet. So
if we were using bootstrap, as I mentioned, we could just set this to true this to
false, and then import map require this file and would be good. There's no official
support right now for tailwind. So we're just going to style this manually over in
assets styles app dot CSS, I'll remove this body. In addition to the tailwind stuff,
you can totally paste in whatever custom styling you want. So this is just a little
thing that I put in here to override some of the styles for a nice dark mode. And now
it looks nice. Love that. Earlier we talked about how in our assets controllers
directory, if we want to make one of these controllers lazy, we can add a comment.
And we can do the same thing with the controllers loaded in controllers dot JSON. And
that's good, right? Because we're only using this autocomplete on one page. So
ideally, we wouldn't need to download this stimulus controller, or Tom select or this
CSS file until we're actually on a page that needs it. So the way to do that is very
simple, fetch, lazy. That's it. So over here, if we for example, go to the voyages
page, I'll go to my network tools and refresh. So if we search here for autocomplete,
or Tom select nothing. But as soon as we go to the edit page where that's being used,
search for autocomplete. There we go. There is the stimulus controller that's housing
autocomplete. And Tom select also got downloaded along with the CSS file. Alright,
next up on day eight, we're going to transform our entire site into a single page
application all at once with turbo.
