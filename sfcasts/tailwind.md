# Tailwind

Coming soon...

I really love using Tailwind for my CSS. If you've never used it before, or maybe
only heard of it, you might hate it at first. And that's because you use classes
inside of your HTML to define everything. And so your HTML can end up looking what
seems like a little bit ugly. But give it a chance. I have absolutely fallen in love
with it. So here's how Tailwind works. It's not a traditional CSS framework where you
download a big giant CSS file. Instead, Tailwind actually has a little binary that
parses all of your templates, finds all the classes you're using, and then dumps the
CSS for just those classes. So it keeps your final CSS as small as possible, only
containing exactly what you need. So it requires a build step. And that's okay. Just
because we don't have a build step for our entire JavaScript system doesn't mean we
can't add one for a specific purpose. And there's a super easy friendly bundle to
help us do this with Asset Mapper. It's called symphony cast Tailwind bundle. Hey,
I've heard of those guys. Let's head down here, go to the documentation. And I'll
copy the composer require line. Let's spin over, run that. So what this bundle is
going to help us do is run that build command in the background, it's actually going
to build a specific CSS file and kind of add the CSS code into that that's needed.
Now if you run bin console, debug config symphony casts Tailwind, you can see the
default configuration for the bundle. And by default, when it goes to build the CSS
file, it actually use assets styles app dot CSS as the default CSS file, which is
great because we actually already have an asset styles app dot CSS file. So it's
basically going to build the code right into here and you'll see that in a second.
Now to get things set up, we're gonna run one other command called bin console
Tailwind. And it's this downloads that buying the binary in the background, which is
awesome. That binary is standalone, it doesn't require node, it just works. This
command also did two other things. The first thing it did is it added the three lines
needed inside of our app dot CSS. When this file is built, these are basically going
to explode into all of the actual CSS that we need. The second thing they did is a
greater Tailwind config dot j s file. And the really important thing about this is it
tells Tailwind where to look for all the classes. So this will find any classes using
our JavaScript files or inside of our template files. Now to actually run Tailwind,
we're going to run bin console Tailwind build dash w for watch. So run that it
builds, it just sits there. And here's the really cool thing. Remember, we already
have app dot CSS being included on our page. And when we go over here and refresh,
you can see it actually looks a little bit different. And the reason for that, if you
view the page source, and click our app dot CSS file is it already works. Our app dot
CSS file no longer is just a is no longer just literally this source file, that
Tailwind bundle is is finding all of the Tailwind classes that we're using inside of
our templates, which aren't any right now, and actually putting them into here. So
all this stuff we're seeing right now is actually kind of the base Tailwind CSS
that's kind of resetting everything. This is actually coming because I've already pre
styled our crud templates. So it's actually finding this stuff there. All right,
let's see this in action for real. So if we refresh the page right now, this is my
h1, you can see it's kind of small. So let's go into templates main homepage dot html
twig. And I'm gonna add a little text dash three XL, move over refresh, it works. If
that text three XL wasn't already in our app dot CSS file, Tailwind just added it
because it's running in the background. So Tailwind is working, we are going to
celebrate by pasting in a new layout. So if you downloaded the course code, you
should have a tutorial directory with a couple of files here, I'm going to actually
move these into my directory and override them. So it's over at base dot html twig.
And these other two files go into the main directory. Perfect. And when you refresh,
if you don't see anything different, that's actually because at least on a Mac,
because I copied those files, twig didn't notice that they were updated. So I'm gonna
run a little cache clear. And got it, here is our site styled up and ready to go. And
we're gonna look at all the templates that I just moved into the second, but there's
nothing special about them. So we do have a list of voyages right here. But this is
all just really boring, just normal twig code with tailwind classes, nothing special
happening at all. Now we do have some broken planet images right here. And to fix
those go into the tutorial assets directory. And there's gonna be some planet images
there. We're just going to move those into the assets slash images directory. I get
this one, I can delete that tutorial directory. Awesome. And then open up this little
planet list partial that we just created. And you can see down here, there's an image
tag. That's this image tag up here. And I kind of left that for us to finish. So we
know that we can do curly curly assets. And from here, we can say something like
images slash, let's see, planets, one dot png. That would totally work. In this case,
the planet one dot png part is actually a dynamic property on my planet entity. So we
can say tilde planet dot image file name. And it works. I love that. Okay, so this
looks awesome. This looks cool. But I want to make this look awesome. So I'm going to
tweak a few other things. The first thing I don't love is this date. It's just
boring. I want a cooler looking date over there. So to fix this, we're going to
install one of my favorite bundles called say compose require a campy labs slash
campy time bundle. Oops, was required. Simply campy lab slash campy time bundle.
Probably seen me use this before. This gives us a nice little a go filter. So as soon
as this finishes, spin over and go to homepage dot html twig. And if you search for
leave at, here we go. Here's the spot that's actually rendering that y dash m dash d.
Place that with a go. Much cooler. Love that way trendier looking dates. Now, one
nice thing is I added some links on top to the crud. So these are crud areas for
managing our voyages and our planets. And these were basically just generated via
maker bundle. But as you can see, I pre loaded them with tailwind classes so that
they look good out of the box. And boy, there's a lot of celebrating happening right
now. Except if you go to a form, it looks terrible. And that makes sense. This is
coming from symphonies form theme, so it's not outputting with any of the tailwind
classes. So what do we do? Do we need to create a form theme? Fortunately, no. One of
the great things about tailwind is there's an entire ecosystem set up around it. For
example, flow byte is a flow byte has a pro version, but it also has a bunch of open
source, basically examples of different elements. So for example, there's an alert
page where you can just get CSS to make different alerts, you don't need to install
anything with flow byte. This is all just native tailwind, you can just copy this
into your project. And it's gonna look like this up here. Nothing special about it.
So I absolutely love tailwind. This also has a forms section where it shows how you
can make your forms look really nice. So you can see in theory, if we got our form
rendering like this, it would look good. And fortunately, there's a bundle that can
help us do that. It's called tails from a dev flow byte bundle. Let's click
installation, get this guy installed, copy the composer acquire line. Boy, we're
getting a lot of stuff installed today. This asks if we want to install the contrib
recipe, let's say yes, permanently. Cool. And there's back on the installation
instructions, we don't need to do the register the bundle that happens automatically.
We do need to copy this line for the tailwind configuration file. So open up our
tailwind dot config dot j s. And let's paste that in there. This bundle comes with
its own form theme templates with its own tailwind classes on there. So we want to
make sure that tailwind sees those so it generates the CSS for them. And the last
step over the docs is going to be to tell our system to use this form theme by
default. So this happens down in config packages tweak that yaml. I'll paste that
form themes on there, fix the indentation. And that's it. Go back, refresh. It looks
beautiful. Eureka, we've got tailwind everywhere. Everywhere. So next up, let's turn
it back to JavaScript. Our app has some JavaScript, but it's kind of randomly just
written about. Let's make a JavaScript first class with stimulus.
