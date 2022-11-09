# WP Sync

A WordPress plugin to sync pages and menus between GitHub and Wordpress.

# Motive

Content creation and version control can be done natively in Wordpress. However, it's not as powerful as a full-fledged version control system like `git`. Moreover, contribution from the public requires sharing the source code of the content, and with it comes the hassle of synchronization. This plugin aims to take advantage of the [VC](https://en.wikipedia.org/wiki/Version_control) for content management, enabling the public and/or a team to contribute to a Wordpress site through a GitHub repository without ever requiring to have access to the [WP Admin Dashboard](https://wordpress.com/support/dashboard/).

# Features

- Sync with GitHub
- Sync pages, and menu
- Support hierarchical organization of pages

# Sync

One-directional sync is supported - changes can be pushed from the _git host_, such as GitHub, to Wordpress. For convenience, a GitHub hook, triggered upon any commit, can be created in the repository, which will automatically alert the plugin for sync.

On sync, the plugin will look for creation of 2 types of content:

- [Page](https://wordpress.org/support/article/pages/)
- Menu

# Convention

The repository where the WP content will be housed needs to follow a strict convention, since this plugin is built on the idea of _convention over configuration_. The convention is mostly based on the file system - the directory structure and the naming convention.

With that in mind, the repository is expected to have the following directories in the root:

- menus
- pages

> File/directory names are case-sensitive. E.g. naming the directory "Menus" instead of "menus" makes a difference.

## Pages

Pages are stored in the _pages_ directory as HTML files. However, they are not kept in the root of the _pages_ directory, but encapsulated by another directory that represents a category.

For example, a file named _First-Day.html_, which is inside a directory named _Thailand-Trip_ in the _pages_ directory would represent a page named _First Day_ in the _Thailand Trip_ category.

> As you can guess, the hyphen is translated into a space, but can be escaped with a backslash (\\) if the hyphen is desired to be preserved. For example, a file named _Year\\-end-Trip.html_ results in a page named _Year-end Trip_.

## Menus

The _menus_ directory holds the menus, with each menu being defined through a JSON file, and the file name being the name of the menu.

The JSON file holds information about the topology/hierarchy of the menu items.

For example, a file named _Trips.json_ defines a menu named _Trips_. Its content may look like this:

```json
[
    { "label": "Thailand"
    , "original": "/thailand"
    , "children":
        [
            { "label": "First day"
            , "original": "/thailand/first-day"
            }
        ]
    }
]
```

> The order of the items is preserved.

As you can see, each menu item is defined by a JSON object, which has the following properties:

- label 
- original (_optional_)
- children (_optional_)
