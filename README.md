AVT Libraries for Arcanist
============

A collection of custom libraries and scripts for [Phabricator](http://phabricator.org/), a tool originally open sourced by Facebook for task management and code review.

The scripts and documentation included in this repository were originally inspired by the [disqus-arcanist repository](https://github.com/disqus/discus-arcanist/).

Installing Arcanist Globally
============================

You'll need write access to /usr/local:

    $ curl -L https://raw.github.com/diablomedia/avt-arcanist/master/getarcanist.sh | sudo sh

This will give you access to the following additional commands:

**create-arcconfig [project_id] [path]**

Generates a .arcconfig based on the global arcanist installation at the given path.

If path is not set, it generates it in the current working directory.

**update-arcanist**

This executes the same script which you used to originally install Arcanist.
