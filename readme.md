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

### Requirements

- PHP 7.4 or higher
- WordPress 5.8 or higher

### Installation

#### Via Composer (Recommended)

**Basic Installation:**
```bash
composer require boospot/boo-settings-helper
```

**For WordPress Plugins (composer.json example):**
```json
{
  "require": {
    "php": ">=7.4",
    "boospot/boo-settings-helper": "^5.4"
  },
  "autoload": {
    "files": [
      "vendor/boospot/boo-settings-helper/class-boo-settings-helper.php"
    ]
  }
}
```

**Version Constraints:**
- `"^5.4"` - Latest compatible version (recommended for security fixes)
- `"5.4.*"` - Specific 5.4.x versions only  
- `"~5.4.1"` - 5.4.1 and patch versions only (latest bug fixes)

Or visit the [Packagist page](https://packagist.org/packages/boospot/boo-settings-helper) for more installation options and version information.

#### Manual Installation
1. Download the `class-boo-settings-helper.php` file
2. Copy the class file to your plugin's assets or includes folder
3. Require the class in your plugin files: `require_once 'path/to/class-boo-settings-helper.php';`

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

### Version 5.4.1 - Bug Fix Release (November 2025)

This patch release fixes critical PHP 8+ compatibility issues while maintaining 100% backward compatibility.

#### Bug Fixes:
- **PHP 8+ Compatibility**: Fixed "Undefined array key 'id'" warnings in field processing
- **Defensive Programming**: Added `isset()` checks before accessing `$field['id']` in all methods
- **Error Prevention**: Graceful handling of incomplete field definitions passed by plugins
- **Production Ready**: Eliminates PHP warnings in production environments

#### Technical Changes:
- Fixed `get_default_field_args()` method to handle missing 'id' keys (lines 476, 487, 491, 492)
- Fixed `get_settings_fields_ids()` method to validate field structure (line 1343)
- Added fallback to empty string for missing field IDs
- Maintained all existing functionality and API compatibility

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

### v5.4.1 (November 2025)
- BUGFIX: Fixed "Undefined array key 'id'" warnings in PHP 8+
- BUGFIX: Added defensive `isset()` checks in `get_default_field_args()`
- BUGFIX: Added defensive `isset()` checks in `get_settings_fields_ids()`
- IMPROVEMENT: Graceful handling of incomplete field definitions
- IMPROVEMENT: Enhanced error prevention for production environments
- COMPATIBILITY: 100% backward compatible with existing plugins

### v5.4 (January 2025)
- SECURITY: Fixed multiple XSS vulnerabilities in form callbacks
- SECURITY: Eliminated password hash leakage in HTML output
- SECURITY: Changed REST API exposure default to false
- SECURITY: Moved log files to secure location
- IMPROVEMENT: Added PHP 8.2 compatibility
- BUGFIX: Added missing echo in callback_media() description
- IMPROVEMENT: Enhanced array handling with array_replace_recursive()

### v5.3 (Previous stable)
- Base version with existing functionality
