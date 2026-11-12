# UMP CFERI WordPress website

This repository contains the custom UMP CFERI WordPress plugin and theme source,
along with the existing shared assets and backups. It does not contain WordPress
core, uploads, local configuration, or plugin/theme ZIP archives.

## Repository layout

- `plugins/umpcferi-plugin/` - custom plugin PHP, frontend/admin assets, and tests.
- `themes/umpcferi-theme/` - complete custom theme, templates, styles, scripts,
  and required images.
- `assets/` - existing shared project assets.
- `backups/` - existing repository backups.

## Installation

Copy `plugins/umpcferi-plugin/` to a WordPress installation's `wp-content/plugins/`
directory and `themes/umpcferi-theme/` to `wp-content/themes/`. Activate the
plugin and theme in WordPress.

## Test

Run the plugin chatbot regression test with Node.js:

```sh
node plugins/umpcferi-plugin/tests/chatbot.test.js
```
