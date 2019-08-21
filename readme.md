### What is it?
This helper class lets you create the settings page for your plugin using the Wordpress Settings API without having to deal with the API directly.
 
No deeper dependencies, No framework, just a light weight helper class!

require the class, hook into `admin_menu` and pass the config array to class object to build your plugin settings page.

### Why should i use it?
If you want to create plugin settings menu that is following WordPress best practices without having to deal with complex WorPress Settings API, then this helper class can be used. 

The Benefits:
* Take away the pain of dealing with Settings API
* One config array to create everything: admin menu, settings page, sections, fields.
* Fields input is auto sanitized
* Can be used to make Tabs or Tab-less Settings page
* Can be used to add plugin action links
* Ability to override sanitization callback
* Ability to override fields display callback  

### How to use?

Complete Details can be found in the [Wiki](https://github.com/boospot/boo-settings-helper/wiki), in the nutshell, follow the steps:
                   
1. copy the class in plugin assets folder and require the class in your plugin files (add dependency)
2. hook into `admin_menu` and provide a callback function
3. in the callback function, pass the config array to this helper class object to build your sections and fields.
 
Its that easy. Here is a [simple example](https://github.com/boospot/boo-settings-helper/wiki/Simple-Example) code that will create a plugin menu, 2 sections and some fields under these sections.

### Example

Here are two example plugins to demonstrate this class if you can figure out thing at your own:
1. [Functional / Procedural plugin example](https://github.com/boospot/demo-simple-plugin-to-demonstrate-settings-helper-class)
2. Object Oriented Plugin Example


### What this helper class can create?
This helper class can create the following:
- Plugin admin menu (top level / sub menu)
- Settings Sections (tabbed and tab-less)
- Settings fields under these sections

### Available Field Types

Following Field Types can be added using this Helper Class:

* text
* url
* number
* color
* textarea
* radio
* select
* checkbox
* multicheck
* media
* file
* posts (WP posts and Custom Post Types)
* pages (WP pages)
* password 
* html

![demo plugin settings page](http://g.recordit.co/7aRSdmprGf.gif)
