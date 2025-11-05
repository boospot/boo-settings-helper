# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.4.1] - 2025-11-05

### Fixed
- Fixed "Undefined array key 'id'" warnings in PHP 8+ environments
- Added defensive `isset()` checks in `get_default_field_args()` method (lines 476, 487, 491, 492)
- Added defensive `isset()` checks in `get_settings_fields_ids()` method (line 1343)
- Graceful handling of incomplete field definitions passed by plugins
- Eliminated PHP warnings in production environments

### Changed
- Enhanced error prevention with fallback to empty string for missing field IDs
- Improved defensive programming practices throughout field processing

### Compatibility
- 100% backward compatible with existing plugins
- No breaking changes to public API or method signatures
- Safe upgrade path for all existing implementations

## [5.4] - 2025-01-20

### Security
- Fixed multiple XSS vulnerabilities in form callbacks (text, textarea, select, checkbox, radio, number, color)
- Eliminated password hash leakage - password fields no longer display stored values
- Changed default `show_in_rest` from `true` to `false` to reduce attack surface
- Added proper escaping for navigation tabs, action links, and field descriptions
- Moved log files from plugin directory to secure WordPress uploads directory

### Added
- PHP 8.2 compatibility with proper property declarations and default values
- Enhanced array handling with `array_replace_recursive()`

### Fixed
- Added missing echo in `callback_media()` method description
- Improved array handling throughout the codebase

### Compatibility
- Zero breaking changes to public API or method signatures
- 100% compatible with existing plugins using this helper
- Safe upgrade path - existing implementations continue to work without modification
- No changes to text domains or translation compatibility

## [5.3] - 2024-12-01

### Added
- Base stable version with core functionality
- WordPress Settings API helper implementation
- Support for multiple field types (text, textarea, select, checkbox, radio, etc.)
- Tabbed and tab-less settings page support
- Plugin action links support
- Custom sanitization and display callbacks

### Features
- Automatic field input sanitization
- One config array to create admin menu, settings page, sections, and fields
- Support for WordPress 5.8+ and PHP 7.4+

---

## Version History Summary

- **5.4.1**: Bug fix release - PHP 8+ compatibility fixes
- **5.4**: Security release - XSS prevention and password security
- **5.3**: Stable base version with core functionality

## Upgrade Notes

### From 5.4 to 5.4.1
- **Safe upgrade**: No code changes required
- **Fixes**: Eliminates PHP 8+ warnings for incomplete field definitions
- **Recommended**: Especially for production environments using PHP 8+

### From 5.3 to 5.4+
- **Safe upgrade**: No code changes required  
- **Security**: Multiple security improvements
- **Highly recommended**: For all production environments

## Support

- **Issues**: [GitHub Issues](https://github.com/boospot/boo-settings-helper/issues)
- **Documentation**: [GitHub Wiki](https://github.com/boospot/boo-settings-helper/wiki)
- **Packagist**: [boospot/boo-settings-helper](https://packagist.org/packages/boospot/boo-settings-helper)
