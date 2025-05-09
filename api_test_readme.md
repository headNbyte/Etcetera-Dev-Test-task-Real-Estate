# Real Estate Objects API Test Script

This Python script tests the Real Estate Objects WordPress plugin REST API endpoints. The script verifies that all API endpoints are functioning correctly.

## Features

- Tests both read-only and write operations
- Verifies JSON and XML response formats
- Provides detailed success/failure information
- Automatically authenticates for write operations

## Requirements

- Python 3.6+
- `requests` library

To install the required Python package:

```bash
pip install requests
```

## Usage

Basic usage (read-only tests):

```bash
python api_test.py http://your-wordpress-site.com
```

For testing write operations (create, update, delete), provide Application Password credentials:

```bash
python api_test.py http://your-wordpress-site.com -u admin -p app_password
```

### Command-line Arguments

- `url` - The base URL of your WordPress site (required)
- `-u`, `--username` - WordPress admin username (for write operations)
- `-p`, `--password` - WordPress Application Password (for write operations)

## Tests Performed

1. **GET /properties** - Retrieves all real estate objects
2. **GET /properties/{id}** - Retrieves a specific real estate object
3. **GET /properties with filters** - Tests filtering by district, building type, and eco-rating
4. **XML Format** - Tests the XML response format
5. **POST /properties** - Creates a new real estate object (requires authentication)
6. **PUT /properties/{id}** - Updates an existing real estate object (requires authentication)
7. **DELETE /properties/{id}** - Deletes a real estate object (requires authentication)

## Authentication Note

For write operations (POST, PUT, DELETE), the script uses HTTP Basic Authentication with WordPress Application Passwords. This is a secure, built-in authentication method available in WordPress 5.6+.

To use this authentication:

1. Generate an Application Password in your WordPress admin under Users → Profile
2. Pass the username and generated application password to the script
3. See `app_password_guide.md` for detailed instructions on setting up Application Passwords

## Example Output

```
Starting Real Estate Objects API Tests
=====================================

=== Testing GET /properties ===
✓ GET /properties successful - Found 8 properties

Sample property:
{'building_name': 'Sunrise Towers',
 'building_type': 'brick',
 'content': 'A beautiful modern apartment building in the city center...',
 'coordinates': '50.4501, 30.5234',
 'eco_rating': 4,
 'floors': 12,
 'id': 123,
 'link': 'https://speedrun-rpg.hopto.org/real-estate/sunrise-towers',
 'title': 'Modern Apartment Building'}

=== Testing GET /properties/123 ===
✓ GET /properties/123 successful
{'building_name': 'Sunrise Towers',
 'building_type': 'brick',
 ...}

=== Testing GET /properties with filters ===
✓ GET /properties with filters successful - Found 3 properties

=== Testing XML format ===
✓ XML format response successful

Sample XML response (first 500 chars):
<?xml version="1.0"?>
<response>
  <item>
    <id>123</id>
    <title><![CDATA[Modern Apartment Building]]></title>
    <content><![CDATA[A beautiful modern apartment building in the city center...]]></content>
    <link>https://speedrun-rpg.hopto.org/real-estate/modern-apartment-building</link>
    <districts>
      <item>
        <id>45</id>
        <n>Center</n>
        <slug>center</slug>
      </item>
    </districts>...

API Tests Completed
=====================================
