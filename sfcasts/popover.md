# Popover Part1

Coming soon...

All right, welcome to day 11 where we're really going to build our first big,
beautiful, fully functional feature. We're going to build a popover. As I mentioned,
there are open source stimulus controllers out there to solve lots of different
problems. And one of the best resources for them is Stimulus Components, which is a
collection of stimulus controllers to solve various problems. And we're going to work
with the one called popover. And if you don't remember, popover is when you hover
over something, and you get this nice little box here. So it's like a tooltip, except
they're usually a little bigger, and you can hover over the tooltip themselves. So
this is just a pure JavaScript library. So we are going to install it. Of course, not
yarn add. import map, require stimulus popover. Now this is not a PHP package, this
is just a JavaScript package. So the only change that made is inside of our import
map dot PHP, we have access to that stimulus controller. So this time, we actually
need to register this manually into our application, there's no automatic
registration of this in stimulus. That's okay. Let's go copy these lines from the
documentation. And this for us is going to be done in assets bootstrap.js. So I paste
this on the top, we don't need, we don't need this application start. And then we'll
move this application that register after, we'll call it app dot register. That's it,
we now have a new controller called popover. So the goal is for us to be able to
hover over this planet and to get a little popover with some extra information. We
get the popover to work, we can head down here, there's some rails documentation for
some server side stuff, we don't need that. This is what we need down here. So we're
going to have a data controller popover. Inside you have your link on hover. And then
the controller is activated via a mouse enter, which calls a show method on popover
and mouse leave, which calls a hide on popover. And down here, this is the actual
content that will be shown in the popover. So I'm going to copy this, and then head
to homepage.html twig, and let's search for planets. Perfect. Here we go. So here's
the TD. And here's the planet image. So I'm going to paste this in here. And then
I'll move my image inside. Perfect. And then we just need to put this data action
somewhere. It's tempting to put this data action on the image itself. The reason you
can't do that is this library adds the popover content inside the image. And you
can't put content inside of image and image. So instead, we'll just put it directly
on this div up here. So on mouse enter of this div, show the popover on mouse leave
of this div, hide the popover. That should be enough to get this to work. It's going
to look terrible. But why don't we refresh? It works. So super weird and jumpy, but
it's doing it. All right, so let's make that look better. To do that, I'm going to
select this entire div here and paste. And that didn't do anything too bad. I added a
class relative on the outer div. And down here, I've given our little popover, made
it absolute positioned, and I've printed out some planet info. So just some styling
stuff. And now, look at that. It works. You know what would be really nice though is
one of those little arrows that goes right here. Well, it turns out adding those
arrows is something you can do in pure CSS. And the way that you do it is we can
target with CSS, our little popover target card here. And we're going to add a little
after content with a specific border that's going to make it render as an arrow. This
is kind of a standard CSS strategy for adding arrows. You can find it on the
internet, or you can use AI to help you generate it with Tailwind. So I'm just going
to paste it in. I'm going to put it into app.css. So again, this is something that's
easier to do not in Tailwind. And that's it. So you can see data popover target card.
So this is targeting this element right here, add a little after content position
absolute. And this down here is using Tailwind. But what we're basically doing is
adding a top border, and then all the other borders are transparent. And that's gonna
be enough to give us a triangle stripe. Yeah, look at that. Love it. So at this
point, it works great. But let's keep going for an added challenge. Instead of
loading all of this markup on page load of our homepage, I want to load this content
only once I hover, so I want to make an AJAX call once this hovers. Now the popover
library does have a way to do this out of the box. As an extra challenge, I actually
want to do it with just turbo frames, because turbo frames are really good at loading
things via AJAX, and that'll show off a couple extra features of turbo frames that we
haven't talked about yet. So the first thing is we're going to need an endpoint that
renders just this content right here. So I'm going to go in the homepage, let's
actually grab the content right inside. I'm going to delete that. And in templates
planet, let's create a new file called underscore car dot html dot twig. And I'm
going to paste that in there. Now we'll create an endpoint for this. So source
controller, planet controller, doesn't matter where you go inside of here. And I'm
going to paste in a new endpoints, nothing special about this. It's slash ID slash
card, it grabs the planet, we pass that into the template as a variable planet. So
inside of here, we just need to adjust some of our variables, we don't have a voyage
variable anymore. So it's just planet dot light years. Cool. So we now have an AJAX
endpoint where we can return content. Now here's the magic part. Over in homepage dot
html, twig, we want to load that content right here. And we can do that with a turbo
frame. So check this out, turbo frame. Of course, turbo frame needs an ID. So let's
call this dash card dash curly curly voyage dot planet dot ID. So the ID needs to be
unique on the page, so we'll make a different one for each planet. And then before I
finish that, let's go over to our card and let's put a turbo frame right here.

މ Hybridise this program in a manner сн point that just returns a turbo frame as well if that's what you need. Now back over in homepage.html twig we have the basic setup. The tricky thing is that this time we're not waiting for somebody to click a link inside this frame which will then load this page. We want it to happen immediately. And to do that you can add a source attribute. So source equals curly curly path. And that's actually correct at planet card at planet show card almost. That's it. When a turbo frame appears on the page that already has a source attribute, it loads it and makes the AJAX call immediately. All right, so let's try this refresh and content missing. So we messed something up. Ah, 500 errors. This is actually really great. So I did something wrong and so it's not doing a 500 error. And when this happens, this is where the Web Depot toolbar comes in handy because I can't see the error easily, but I can see my AJAX calls right there and variable voyage does not exist because I need to adjust that to planet.id. All right, and now got it. So you see there's a little moment there where there's no content. We're going to fix that. That does load via AJAX. That's awesome. Now right now, almost by accident, our turbo frame is being loaded lazily. What I mean is when we normally have a turbo frame with a source equals on it, that AJAX call is made immediately as soon as this appears on the page. So from that perspective, we should see about 30 AJAX calls happening immediately. And we don't. You can see the AJAX call only shows up here's AJAX call. As we hover, that's almost happening by accident. Let me try to inspect that element. Yes, yes, yes. There we go. It's almost happening by accident because of the template element, the template element is really special in your browser. Anything inside your template element is basically really like not a real element. It's almost just like text on your page. So the turbo frame inside of here is not actually on the page yet. However, the moment that we hover over this, it copies that onto the page. And as soon as the turbo frame truly appears in the page, turbo takes over and does the AJAX call. But that's not always going to be the case. And one of the great things about turbo frames is you can actually put them into a mode where they won't make their AJAX call until they're visible. So just to show this off, I'm going to change this template to a div temporarily. Now this is going to look awful because it's going to load this on the page all at once. So I'm going to refresh. There we go. See, they're all loaded. And you can see that it just made 30 AJAX calls. So the 30 turbo frames in the page, they all made their AJAX call immediately. If you had this situation and you wanted them to be lazy, we can do that by saying loading equals lazy. So thanks to this, the turbo frame is only going to make the AJAX call once the turbo frame becomes visible on the page. So you can still see the elements are all still showing, but there's only three AJAX calls because there's only three visible. All the other ones are hiding down here. And watch this number as I scroll. See that? As they become visible, they all make their AJAX call. All right, so turbo frames can be lazy. That's super useful. But I want to change this back to a template so that things actually work nicely again. Now we still need to fix the problem that it's kind of missing its content at first because it's empty. And it would be really nice to have some loading content inside of there. This is easy to do because when you have a turbo frame that has a source equal, whatever content is inside of that turbo frame is going to be shown by default before it loads. So I'm going to paste in a div here with an SVG. This SVG comes from tabler icons. Really great way to just find some icons and you can just copy the SVG right there. And then I've coupled that with an animate spin from Tailwind. So we should get a little spinning animation. So sure enough, we try it. Got it. So we get the spinning animation and we no longer have kind of that empty element when we go over it. All right, final thing that I want to fix here is that when we hover over this element, it makes the AJAX call. And it does it every single time. It doesn't remember the content from the AJAX call. And that's just due to how the popover controller works. It creates the turbo frame, then destroys it, then creates a new one, then destroys it and creates a new one. So how can we make the controller remember what it's doing? Well, to answer that question, I'm going to go look at the controller. So assets, vendor, stimulus popover, I'm going to open this up. And I noticed this is kind of all on one line, but if I do command option L for reformat code, I can actually read this, which is pretty sweet. And by looking in here, you can kind of figure out what your options are for overriding stuff. So just like in Symphony, you can actually override stimulus controllers. So what I'm going to do is override show and hide to actually just kind of hide and show the element, the turbo frame on the page, instead of removing it and adding it every single time. So inside of my controllers directory, let's create our own popover controller.js. In here, I'm going to paste in the content. Now notice, normally, when we have a controller, we import controller from stimulus, and that's what we extend. In this case, I'm actually importing popovers controller directly, and we're extending that. Then we're overriding the show method and the hide method so that we can remove a hidden class and add a hidden class so that we don't actually lose the element. And now that we have a controller called popover down on bootstrap.js, we don't need to register the one from stimulus components anymore. The popover controller is going to be our controller. And then we're leveraging the core popover controller by extending it. So let's say that we should see it load once it does, and then it remembers. Love that. That's really cool. All right, next up, we're going to transform this area here into a super rich data tables with searching, filtering, pagination, all happening via Ajax. Let's get that started next.
