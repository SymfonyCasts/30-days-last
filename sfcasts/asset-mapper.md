# Asset Mapper

Coming soon...

Okay, so how are we gonna bring CSS and JavaScript into our application? Are we gonna
bring in some sort of build system like Vitae or Webpack? Definitely not. That's one
of the fun things about this. We are gonna build something amazing with zero build
system. And to do that, we're going to install a new Symfony component called
asset-mapper. So let's spin over to our terminal and run composer require Symfony
slash asset-mapper. This is the new alternative to Webpack Encore. It can do
everything pretty much that Webpack Encore can do, but better and it's simpler, so
you should definitely use it on new projects. Now when I run git status here, you're
gonna see it made a number of different changes. So for example, .gitignore is
ignoring a public assets directory and assets vendor directory. We're gonna talk more
about those later, but on production, this is where your assets are gonna be written
to and this is where we're going to pretty soon actually start installing third-party
JavaScript assets. The most important thing really, and then it also actually made a
change to our base.html twig, ignore that, import map, ignore that. This is all
related to JavaScript and modules and we're just gonna pretend that that doesn't
exist right now. For right now, what I want you to imagine is that when we installed
that, all it gave us was a new assetmapper.yaml file and an assets directory. So
let's go check out that config file, config packages, assetmapper.yaml. So the idea
between assetmapper is dead simple. You define paths here like the assets directory
and you'll probably only define that one directory. What this means is that
everything that lives inside of this assets directory, we can basically reference as
if they are public files. Normally we can only reference things inside the public
directory, but assetmapper allows us to refer to these and when we do, it's gonna
actually give us free asset versioning. But let me show you. If you download the
course code, you should have a tutorial directory with an assets directory. This is
just some codes that we can copy out of here. So I'm gonna copy this logo.png. Inside
assets, we can make this look however we want. So I'm gonna create a new directory
called images and I'm gonna paste that in. As I mentioned, since this is inside the
assets directory, we should be able to reference that from our code. So let's do that
in our base layout, templates base.html.twig. And here I'll say image. Curly curly.
And for this, we're gonna use the normal asset function. And what you do is you
reference the path relative to the assets directory. This is called the logical path.
So images slash logo.png. In fact, before I do that, one of the ways you can see all
of the assets that are available for you to reference is via a bin console debug
asset command. So very simply, this just looks through all of your asset directories.
We just have one and it says, hey, here are the three files that you can reference
and here's the logical path that you should use to reference them. So I can even be
lazy and copy that, paste that in here, and we're done. Now, if you try this, it's
not gonna work. It's gonna tell us we need to run composer requires symphony slash
asset. The asset mapper component doesn't technically need the asset components, but
if we're gonna use the asset function in Twig, then we need it. And now it works. So
the really cool thing is that if you inspect the element here, look at that file
name. It's slash assets slash images. So slash logo and then this long hash. This
hash is actually comes from the file contents. So if we, this is called asset
versioning. If we change our logo, it's gonna automatically generate a new hash here
and that's gonna help, that's gonna take care of busting our cache for our users. Now
in development mode, the way this works is this is actually being served via a
symphony listener. So this is actually going through symphony and it's going and
looking up this path and it's finding logo.png inside of our assets images directory
and it's returning it to us. And that's really the big point of asset mapper. It's
just to allow you to have public assets, but those public assets get free asset
versioning so that when we update them, it's going to bust the user's cache. So on
production though, that's not gonna be fast enough. So when you deploy, you're gonna
run an import map, an asset map compile command. And very simply, it's just gonna
write all of our files into the public assets directory. So now you can see in here
public assets, it just literally has those physical files. So when I go over here and
refresh now, that's not going through symphony anymore, that's loading a real
physical file. Now, if you ever do that locally, make sure you delete that directory
afterwards so that it stops using the compiled versions. All right, so that's part
one of asset mapper. We put things into an assets directory and we can refer to them
with free asset versioning. The second part of asset mapper is all about JavaScript
modules, which is going to allow us to write modern JavaScript with zero build time
and zero build system. Booyah.
