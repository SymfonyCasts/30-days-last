# View Transitions

Coming soon...

Welcome to day 15. We've made it to the halfway point of the tutorial. And I promise
you, it only gets cooler from here. To celebrate our halfway point today, we're going
to talk about something fun, the View Transitions API, a new feature supported in
most browsers that allows us to have really cool view transitions whenever any HTML
changes on your page. And the good news is, if your user has a browser that doesn't
support it, it just skips the transition and happens immediately. So no big deal. Now
to work with this, you do not need to have an external library. But we're going to
use one called TurboViewTransitions. And this library actually came out of a blog
series where they built a really cool project called TurboMusicDrive. So what we're
going to see today with View Transitions, this is actually something that's going to
come standard in Turbo 8. And so this Evil Martians place, they came out with a two
series of blog posts all about this and all about doing super crazy stuff with Turbo.
And when they built this blog post, they actually made a cool demo page where you can
see this stuff in action. So the View Transitions allow us to transition between
pages, we can control what those transitions look like. And we can even connect
elements before and after so that they kind of do a special transition. So watch
this. When I click this album cover here, it's actually going to move up to the left.
That is happening via View Transitions. Also, it has a nice blur on the rest of the
page. So let's try this out. Step one, get the TurboViewTransitions library
installed. So at our terminal, we'll run bin console import map require
TurboViewTransitions. Perfect. To get this working, we need to do two things. First,
in base.html.twig, we need to add a little meta tag. So meta name equals view
transition. And that's it. That ops our site into view transitions. The second thing
we need to do is activate it in JavaScript. So head to app dot j s, this is going to
be some rare JavaScript that doesn't need to live in a stimulus controller. So we can
copy from their example, paste, I'll move the import to the top of the file. And
that's it. This is going to be enough to make our turbo drive page navigations
happening with view transitions. This leverages an event from turbo called turbo
before render. This should perform transition basically checks to see if the user's
browser supports transitions. If they don't, it does nothing. If it does, then this
perform transition is going to transition between the old body and the new body. And
by default, that's going to be a nice little crossfade. There's also a little code
down here to not use the turbo page cache if there are view transitions. Alright, so
let's see what this looks like refresh the page, watch. Look at that super smooth
little cross transitions. So not only do we have no full page reloads anymore, but we
now have these really nice smooth transitions between our pages. And as I mentioned,
that's going to happen by default in turbo eight without us doing anything. But what
about frames? So if we click here, you can see that that still happens instantly. So
we've activated it for full page navigations, but not for frames. Can we? Absolutely.
Copy this listener. And then down here. And this time, we're going to just change it
to turbo before frame render. And then we just need to adjust this down here. Instead
of document that body, we can use event dot target, that will be the I the turbo
frame. And then the new thing will be event dot detail dot new frame. That's it. So
when we refresh and click a beautiful transition on that frame, and if the transition
wasn't obvious enough, you can open up your browser tools here, click the little dot
dot dot, go to more tools, go to animations, and I looks like I already had it open.
And you can actually change the speed of your animations. So watch. Now it's super
obvious. You can even see how it works. If I scroll up and scroll down, you can kind
of see how it takes a screenshot of the old site and blends it in. It's not normally
a problem because this is happening so fast, but it's kind of cool to see it. So at
this point, we probably don't really need our probably don't need our transitions on
our data tables anymore. So let's spin over there and just take those off. So take
off the class. I'll move over refresh. And nothing happens. Well, I mean, it moves,
but there was no transition. Why? This is a bit subtle. But the transitions break
when you have a frame that advances the URL. And the problem is that when you have a
frame that advances the URL, turbo calls turbo before frame render. And then it also
calls turbo before render right after that. And the fact that these are both called
kind of collide and cause a problem. So once you figure this out, it's a pretty easy
fix. We're gonna create a variable called let skip next render transition equals
false. And then down here, if should perform transition and not skip next render
transition, then actually do it. Where does this come into play? We're going to set
that right down here. So when our frame starts to do its thing, we're going to set
this to true. And then we're going to set a little timeout here and set that to
false. And we're just going to let this go for 100 milliseconds. So it's a little bit
weird. But we're going to set this to true. Then when this is called almost
immediately after it will skip doing it, which will fix things. And then within 100
milliseconds, we'll set this back to false. So now when we refresh, beautiful, it
changes, let's set that back to full speed. So it looks a little more normal.
Awesome. That is a smooth effect right there. Now the last little detail, and
honestly, I'm not sure what's going on here is check that out when we hover over kind
of disappears because of the loading that has to do with the turbo form transition,
there's probably a way to fix it, I couldn't crack it. So we're just going to add a
way to disable the transitions on turbo on a turbo frame if we have this weird
problem, which I think is just going to apply to the popover. So on the homepage, I'm
going to search for turbo dash frame, here we go. Here's our lazy one right here. And
we're going to add a new attribute here called data, skip transition, I'm totally
just making that up. And then over an app dot j s, we can just look for that. So if
should perform transition, and not event dot target has had as attribute data skip
transition, then do the transition. Now, perfect. So that works. Well, we have
transition on that, we have transitions on this, we have transitions on the whole
page. It's crazy. Again, all done with such little JavaScript. Next up tomorrow,
let's handle having, let's add a really cool little toast notification system on our
site that's going to be easy to activate, no matter where and how we need to add
them.
