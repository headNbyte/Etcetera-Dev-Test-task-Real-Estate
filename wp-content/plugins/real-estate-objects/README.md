# Real Estate Objects

A WordPress plugin that initializes a new post type "Об'єкт нерухомості" (Real Estate Object) and taxonomy "Район" (District), where a real estate object is a building (which can contain multiple apartments or premises).

## Features

- Custom post type "Об'єкт нерухомості" (Real Estate Object)
- Custom taxonomy "Район" (District)
- ACF fields for real estate objects:
  - Building name
  - Coordinates
  - Number of floors (1-20)
  - Building type (panel/brick/foam block)
  - Eco-rating (1-5)
  - Building image
  - Premises (repeater field) with:
    - Area
    - Number of rooms (1-10)
    - Balcony (yes/no)
    - Bathroom (yes/no)
    - Image
- REST API for CRUD operations on real estate objects with both JSON and XML support
- Shortcode `[real_estate_filter]` for displaying a filter form
- Widget for displaying the filter form
- AJAX-powered search and filtering
- Sorting real estate objects by eco-rating

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- Advanced Custom Fields (ACF) plugin

## Installation

1. Upload the `real-estate-objects` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure the Advanced Custom Fields (ACF) plugin is installed and activated

## Usage

### Adding Real Estate Objects

1. Go to "Об'єкти нерухомості" in the WordPress admin menu
2. Click "Додати новий" to add a new real estate object
3. Fill in the title, content, and ACF fields
4. Assign the object to a district using the "Райони" taxonomy
5. Publish the object

### Using the Shortcode

Add the `[real_estate_filter]` shortcode to any page or post to display the real estate filter form:

```
[real_estate_filter]
```

You can also specify a custom title:

```
[real_estate_filter title="Знайти нерухомість"]
```

### Using the Widget

1. Go to "Appearance" > "Widgets" in the WordPress admin menu
2. Drag the "Real Estate Filter" widget to a widget area
3. Set a title for the widget (optional)
4. Save the widget

### Using the REST API

The plugin includes a comprehensive REST API for working with real estate objects. See the [API Documentation](api-documentation.md) for detailed information on using the API.

Key API features:
- GET, POST, PUT, DELETE methods for properties
- Filter properties by various criteria
- XML response format support
- Authentication using WordPress Application Passwords

#### API Testing

Two Python scripts are included for testing the API:

1. `api_test.py` - Comprehensive test suite with authentication
   ```bash
   python api_test.py http://your-site.com -u username -p app_password
   ```

2. `api_test_simple.py` - Simple read-only test script
   ```bash
   python api_test_simple.py
   ```

See `api_test_readme.md` and `app_password_guide.md` for detailed usage instructions.

## Customization

### Styling

The plugin includes basic CSS for the filter form and results. You can customize the appearance by adding custom CSS to your theme or using a custom CSS plugin.

### Templates

To customize the display of real estate objects, you can create custom templates in your theme:

- `single-real_estate_object.php` - Template for single real estate object
- `archive-real_estate_object.php` - Template for real estate object archives
- `taxonomy-district.php` - Template for district taxonomy archives

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed as a Dev test for Etcetera.
