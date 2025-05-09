# Simple SVG Upload

A lightweight WordPress plugin that enables SVG file uploads.

## Features

- Allows uploading SVG files to the WordPress media library
- Fixes MIME type detection for SVG files
- Displays SVG thumbnails correctly in the media library
- Basic sanitization of SVG files to remove potentially harmful elements

## Installation

1. Upload the `simple-svg-upload` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Start uploading SVG files to your media library

## Security Note

This plugin includes basic sanitization for SVG files which:
- Removes `<script>` tags from SVG files
- Removes event handlers (like onclick attributes)

While this provides some protection, it is still recommended to only allow trusted users to upload files.

