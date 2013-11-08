CoreSysWordpress
================

A Wordpress 3.7.1  vendor bundle for Symfony2
This wordpress will be modified such that specified templates will return Twig templates back to symfony to be integrated into views.
There will be modifications done to the core files which will not interfere with normal operation of wordpress.

This version should theoretically be able to run as a standalone copy of wordpress and/or be fully integrated into Symfony2.
This is the core files only, and will require the CoreSysWordpressBundle(under development) to be able to properly integrate wordpress.

So far, I have ran into a few issues.
Even though the front-end themes can be successfully converted to output twig templates, the admin area of wordpress is a completely different story.
It seems that the admin uses a template engine much like twig, relatively similar syntax which interferes with sending twig templates back.
Looking into alternative ways to fully import the wordpress admin into a useable twig template to plug into existing symfony2 bundles

Please do not pull or use this repo until it has been test and works.
Again, this will require CoreSysWordpressBundle, CoreSysSiteBundle, CoreSysAdminBundle, possibly misd/guzzle (all of which will be available once completed)
