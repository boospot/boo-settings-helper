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

### Installation

#### Via Composer (Recommended)
```bash
composer require boospot/boo-settings-helper
```

Or visit the [Packagist page](https://packagist.org/packages/boospot/boo-settings-helper) for more installation options and version information.

#### Manual Installation
1. copy the class in plugin assets folder and require the class in your plugin files (add dependency)
2. hook into `admin_menu` and provide a callback function
3. in the callback function, pass the config array to this helper class object to build your sections and fields.

### How to use?

Complete Details can be found in the [Wiki](https://github.com/boospot/boo-settings-helper/wiki), in the nutshell, follow the steps above for installation, then:
 
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

## Security & Updates

### Version 5.4 - Security Fixes (January 2025)

This version addresses multiple security vulnerabilities while maintaining 100% backward compatibility.

#### Security Improvements:
- **XSS Prevention**: Fixed output escaping in all callback methods (text, textarea, select, checkbox, radio, number, color)
- **Password Security**: Eliminated hash leakage - password fields no longer display stored values
- **REST API Security**: Changed default `show_in_rest` from `true` to `false` to reduce attack surface
- **HTML Security**: Added proper escaping for navigation tabs, action links, and field descriptions
- **Log Security**: Moved log files from plugin directory to secure WordPress uploads directory

#### Infrastructure Improvements:
- **PHP 8.2 Compatibility**: Added proper property declarations with default values
- **Bug Fixes**: Added missing echo in callback_media() method, improved array handling
- **Enhanced Security**: All user-facing output now properly escaped using WordPress functions

#### Backward Compatibility:
- Zero breaking changes to public API or method signatures
- 100% compatible with existing plugins using this helper
- Safe upgrade path - existing implementations continue to work without modification
- No changes to text domains or translation compatibility

## Changelog

### v5.4 (January 2025)
- SECURITY: Fixed multiple XSS vulnerabilities in form callbacks
- SECURITY: Eliminated password hash leakage in HTML output
- SECURITY: Changed REST API exposure default to false
- SECURITY: Moved log files to secure location
- IMPROVEMENT: Added PHP 8.2 compatibility
- BUGFIX: Added missing echo in callback_media() description
- IMPROVEMENT: Enhanced array handling with array_replace_recursive()

### v5.3 (Current stable)
- Base version with existing functionality
