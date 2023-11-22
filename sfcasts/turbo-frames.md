# Turbo Frames

Coming soon...

Day 10, and we're going to talk about an ancient concept, frames. If you're old
enough on the internet, like me, you might remember iframes. They were these weird
little things where you could separate your site into different pieces. And then when
you clicked a link inside of a frame, it stayed inside of that frame. It only loaded
that one frame. It was like having separate web pages that you cobbled together into
one. So the second part of Turbo is TurboFrames, which is a not weird, super modern
way to get that same thing. We're going to be able to organize our pages into small
pieces and have the navigation stay inside that area. In our case, we have this left
sidebar. And when we click one of these planets, it takes us to the show page for
that planet with all that information. Cool. So what I would like to do instead is
when I click a planet over here, I want that content to load right inside of this
sidebar without changing pages. To do that, we need to find this sidebar, which is
over here in templates, main homepage.html.twig up near the top. Here we go. So this
partial is actually what renders that planet list. So to make this a frame, we're
going to find the element that surrounds it and change it to a Turbo-frame. And the
one rule of frames is that they all need to have an ID attribute. It should be
something unique that describes what it holds. So how about planet arrow info,
planet-info. All right, what does that do? At first, nothing. A TurboFrame is just an
HTML element like a div, and so it renders normally. However, when we click it
breaks, you can see content missing. And down here it says the response did not
contain the expected TurboFrame ID equals planet-info. So as soon as you surround
something by a TurboFrame, here's what happens. When we click this, it does go over
to our show page here. It then looks for a matching TurboFrame ID equals planet-info.
It finds that on this page, grabs whatever content is inside of it, and puts that
inside the TurboFrame over here. So what this means is that your links inside of your
TurboFrame, whatever page they go to, that page also needs to have a matching
TurboFrame. So I'm going to copy this TurboFrame ID equals planet-info, and then go
to planet, show.html.twig. So we're going to put this around the content we want to
load in the sidebar. So I don't really want the H1 over there in the sidebar. I
basically just want this big table of info. So I'm going to paste that there. And
then put the TurboFrame on the bottom. Now refresh, and it works. Look how cool that
is. When we click this, it makes an AJAX request to this page, grabs just the
TurboFrame contents, and puts them right inside. So what else would be great is to
have a little backlink where I click the backlink, and it goes back to this list of
planets. So to do that, still inside my TurboFrame so that it loads, let's add a
little div class equals mt-2, and I'll put an a tag with path, we're actually going
to link to the home page right here. Now let me go over and try it. I don't even need
to refresh. Check it out. We have a backlink. I had to make that a little bit more
like an arrow. The point is when we click it, it works. So in this case, when we
click back, that makes an AJAX request to the home page, looks for the TurboFrame on
the home page. What's the TurboFrame on the home page? It's the list of planets right
here. All right, so we're on a roll. Let's add another link here. Let's add an edit
link to edit the planet. So that's going to be app planet edits, I'll do id
planet.id, cool. And this time, if we go click on one of these and go to edit, it
doesn't work. And I bet you can guess why. This time I made an AJAX request to the
edit page, but there's no matching TurboFrame on the edit page. And so we get this
error. And in this case, I don't want to add a TurboFrame to the edit page because it
wouldn't fit in the sidebar anyway. In this case, I want this edit link to be a full
page navigation. So as soon as you introduce TurboFrames, you need to keep track of
the links that you have inside of them and either make sure that they go to pages
that have that matching TurboFrame or that you make them full page navigations. And
the way that you do that is on a link, you add data turbo target, and to target a
full page, you do underscore top. So now again, I don't even need to refresh, hit
edit. And it still breaks, but you know what, it's because I use the wrong thing. So
target turbo frame equals top. There we go. Now hit edit, and we get the full page
navigation. The other way to do that is if you want on the TurboFrame itself, you can
also say, Hey, I want all the links by default to be full page reload. So you can say
target equals underscore top. And then everything inside there will be full page
reloads. And then if you have a specific link that you want to load in just this
frame, you can add data TurboFrame equals and then the ID of the frame planet info.
So it's up to you whether you want to, how you want to do it. If you want to navigate
on the full page by default, or if you want to navigate on the frame by default
doesn't really matter. So this is working super duper well, except for the fact that
if the user does ever get to the planet show page, we have actually an extra set of
links here. So really only want to show those when we're inside of the TurboFrame
context. So how can we do that? Well, when Turbo sends an Ajax request for a
TurboFrame, it does add a request header that tells you this is a TurboFrame request.
And you can use that inside of symphony to conditionally do different things like
conditionally render these links. We are going to do that one time later in the
tutorial. However, I try to minimize that because it just adds complexity to have all
these conditionally rendering things. So another way to hide these links is just via
CSS. So for example, we could add a class or onto our sidebar here, and then only
show these links if we are inside of that class. However, Tailwind doesn't really
work like that. In Tailwind, you can't really change your styling conditionally based
on your parent out of the box. But we can do this with a trick called a variant. So
the first thing to notice is that a TurboFrame by default looks like this, exactly
like we have it in our template. But as soon as we click one of those links, and it
navigates, it now has a source attribute on it. So what we're going to do is add a
way inside of Tailwind to style elements conditionally based on whether a TurboFrame
has a source attribute or not, because it will have a source attribute over here. But
it's not going to have a source attribute inside of this TurboFrame. So the way to
do, so we're going to do this, we're going to add a Tailwind variants a little bit
more advanced, but it's a super powerful concept. So we're going to import this
plugin thing, we're going to go down to plugins. And I'm just going to paste in some
code here. So this adds a variant called TurboDashFrame, you'll see how we use that
in a second. And it basically applies to anything that's inside of a TurboFrame that
has a source attribute. So because we call this TurboDashFrame, I'll copy that. And
what this allows us to do in show.html.twig is we can add a hidden class here, it's
going to be hidden by default, which means I want to refresh, it's gone. But then if
we are inside of a TurboFrame, we are going to add a block. So now over here, if I
refresh, those links are still hidden. But over here, we've got them because we're
inside of that TurboFrame. Alright, next up, when I hover over these planets, I want
to get a cool pop over with more planet information. To make that happen, we're going
to install another third party pack stimulus controller.
