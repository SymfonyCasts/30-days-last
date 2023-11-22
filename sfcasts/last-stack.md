# Last Stack

Coming soon...

Hello everyone, welcome to our tutorial about Last Stack, and I gotta say, this might
be my favorite tutorial ever. I just had an absolute blast building this, because
unlike some of our other tutorials, I'm gonna go a little bit less into teaching deep
concepts during this tutorial, and instead we're actually gonna make a full, rich,
polished, beautiful product, and I think you're going to love it. Now, Last Stack,
what the heck is that? That is an acronym that, honestly, I made up. I wanted
something cool. It stands for Live Components, Asset Mapper, Stimulus, and Turbo, and
what this is, is a front-end stack that's gonna allow us to build a truly rich user
interface, like single-page application, modals popping up, AJAX everywhere, but
we're gonna build it entirely just with Symfony and Twig, and in fact, we're gonna
have to write very little JavaScript to get it done. We are gonna have to write some
JavaScript and understand some JavaScript concepts, but the amount of reward that
we're going to get for just a little bit of work is truly exciting, and at the end of
this tutorial, we're gonna have reusable patterns that we can leverage in other parts
of our code, like modals, to get things done really quickly. Now, at the core, also I
wanna mention, at the core of this whole system is Hotwire, which is a collection of
Turbo. We'll talk about that. Stimulus, we'll talk about that, and also, I just wanna
mention they also have Strata. This is relatively new, and it's part of a set of
tools here where you can actually use the same application that we're building right
now and use it to power a mobile application. Totally not something we're gonna talk
about right now, but I wanted to point that out. This is a really powerful stack to
have here. I also wanna think, one of the other cool things about this is that all of
Hotwire is not unique to Symfony. It's used, for example, by the Ruby on Rails
community, and a lot of the things that we're going to build are things that I
learned from various people in the Ruby on Rails community because the fact that
we're all using the same tool means we get to share libraries, share ideas, build on
top of each other's shoulders, and that is a massively powerful concept. All right,
so let's get into this. This is so fun to make things pop up, so absolutely download
the course code and code along with me. When you unzip the file, you'll find a start
directory, which has the same files that you see here, including the all-important
readme.md file with details on how to get the project set up. The last step in there
is going to be to open a terminal, move into the directory, and run symfony serve-d
to start a built-in web server at ... Oh, in my case, port 8001. I must already have
something running on port 8000. We'll click that to see a big, ugly page of nothing.
This is by design. To give you a little intro here, this is a symphony 6.4 project,
even though that's not released the moment I'm recording this. There's not too much
in here. I've pre-installed Twig. We have two different entities called Planet and
Voyage because what we're going to build is a trip planning site for aliens. I also
have some data fixtures, and I've also used Maker Bundle to generate me two CRUDs.
This Planet controller and this Voyage controller and these templates down here,
these are straight out of Make CRUD. I have made some adjustments. I've added some
classes so they'll look good with Tailwind. We'll talk about that later, but
basically, there's nothing very special going on. I do have a main controller, which
powers this homepage. I have a little query in here to help us with some search we're
going to be doing, but this template right now isn't actually even doing anything.
The point is, there is no CSS, no JavaScript, no assets of any kind, and our site
doesn't really do anything yet or render anything yet. That's going to be up to us.
How do we bring CSS and JavaScript? Oh, I need to mention that. This is going to all
be about no build. Next, let's install Asset Mapper, which is going to give us the
ability to bring CSS and JavaScript into our application in a super rich way. No
build system, no node. Let's get to it.
