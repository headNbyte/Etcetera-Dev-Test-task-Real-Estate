# Real Estate Objects - Etcetera Dev Test

This project implements a WordPress plugin that manages real estate properties with a comprehensive REST API.

## Project Overview

The Real Estate Objects plugin provides:
- Custom post type for real estate properties
- Custom taxonomy for districts
- Complete REST API for property management
- Both JSON and XML response formats
- Filtering capabilities for properties
- Authentication via WordPress Application Passwords

## Documentation

- [API Documentation](wp-content/plugins/real-estate-objects/api-documentation.md) - Detailed information about the REST API endpoints
- [Application Password Guide](app_password_guide.md) - Instructions for setting up secure API authentication
- [API Test Documentation](api_test_readme.md) - Guide for using the test scripts

## Tools

- [api_test.py](api_test.py) - Comprehensive API test script with authentication support
- [api_test_simple.py](api_test_simple.py) - Simple read-only API test script
- [create_mock_entries.py](create_mock_entries.py) - Script to generate mock property data

## Example Images

Sample property images are available in the [example_images](example_images/) directory.

## Installation

1. Clone this repository
2. Copy the `wp-content/plugins/real-estate-objects` directory to your WordPress plugins directory
3. Activate the plugin through the WordPress admin interface
4. Use the provided test scripts to verify the API functionality

## API Testing

```bash
# Run the simple test script (no authentication required)
python api_test_simple.py

# Run the comprehensive test script with authentication
python api_test.py http://your-site.com -u admin -p app_password
```

## Data Generation

```bash
# Generate mock property data (requires authentication)
python create_mock_entries.py http://your-site.com -u admin -p app_password -c 10
```

## Notes

This project was created as part of the Etcetera Dev Test. It demonstrates WordPress plugin development, custom post types, REST API implementation, and testing methodologies.

P.S. The ACF free version does not allow to use repeater field in admin panel, but when using the API the page properly displays the data.
