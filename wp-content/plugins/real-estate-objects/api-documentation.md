# Real Estate Objects API Documentation

This document provides information about the Real Estate Objects REST API endpoints and how to use them.

## Base URL

All API endpoints are prefixed with:

```
/wp-json/real-estate/v1
```

## Authentication

For read-only operations (GET), no authentication is required. For write operations (POST, PUT, DELETE), you must be authenticated as an administrator.

The recommended authentication method is WordPress Application Passwords:

1. **Application Passwords** - Available in WordPress 5.6+
   - Set up in the WordPress admin panel under Users → Profile
   - Generate a unique password for each application or script
   - Use HTTP Basic Authentication with these credentials
   - See `app_password_guide.md` for detailed setup instructions

For browser-based applications, you can also use:

2. **Cookie Authentication**
   - Use the standard WordPress login form
   - Include the `X-WP-Nonce` header in your requests

For more information on WordPress REST API authentication, see: https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/

## API Testing Tools

Two Python scripts are provided for testing the API:

1. `api_test.py` - Comprehensive test suite with authentication support
   - Usage: `python api_test.py http://your-site.com -u username -p app_password`
   - Tests all endpoints (GET, POST, PUT, DELETE)
   - Uses HTTP Basic Authentication with Application Passwords
   - Supports XML response testing

2. `api_test_simple.py` - Simple read-only test script
   - Usage: `python api_test_simple.py`
   - Tests basic endpoints without authentication
   - Helpful for initial setup verification

See `api_test_readme.md` for more details on using these test scripts.

## Endpoints

### Get Properties

Retrieve a list of real estate properties with optional filtering.

**Endpoint:** `/properties`

**Method:** `GET`

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| district | string | No | Filter by district slug |
| building_type | string | No | Filter by building type (panel, brick, foam_block) |
| min_floors | integer | No | Minimum number of floors (1-20) |
| max_floors | integer | No | Maximum number of floors (1-20) |
| min_eco_rating | integer | No | Minimum eco-rating (1-5) |
| page | integer | No | Page number (default: 1) |
| per_page | integer | No | Items per page (default: 10, max: 100) |

**Example Request:**

```
GET /wp-json/real-estate/v1/properties?district=center&building_type=brick&min_eco_rating=3
```

**Example Response:**

```json
[
  {
    "id": 123,
    "title": "Modern Apartment Building",
    "content": "A beautiful modern apartment building in the city center...",
    "link": "https://speedrun-rpg.hopto.org/real-estate/modern-apartment-building",
    "districts": [
      {
        "id": 45,
        "name": "Center",
        "slug": "center"
      }
    ],
    "building_name": "Sunrise Towers",
    "coordinates": "50.4501, 30.5234",
    "floors": 12,
    "building_type": "brick",
    "eco_rating": 4,
    "premises": [
      {
        "area": "85",
        "rooms": 3,
        "balcony": "yes",
        "bathroom": "yes"
      },
      {
        "area": "65",
        "rooms": 2,
        "balcony": "yes",
        "bathroom": "yes"
      }
    ],
    "note": "Images should be added manually through WordPress admin"
  }
]
```

### Get Single Property

Retrieve a single real estate property by ID.

**Endpoint:** `/properties/{id}`

**Method:** `GET`

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Property ID |

**Example Request:**

```
GET /wp-json/real-estate/v1/properties/123
```

**Example Response:**

```json
{
  "id": 123,
  "title": "Modern Apartment Building",
  "content": "A beautiful modern apartment building in the city center...",
  "link": "https://speedrun-rpg.hopto.org/real-estate/modern-apartment-building",
  "districts": [
    {
      "id": 45,
      "name": "Center",
      "slug": "center"
    }
  ],
  "building_name": "Sunrise Towers",
  "coordinates": "50.4501, 30.5234",
  "floors": 12,
  "building_type": "brick",
  "eco_rating": 4,
  "premises": [
    {
      "area": "85",
      "rooms": 3,
      "balcony": "yes",
      "bathroom": "yes"
    },
    {
      "area": "65",
      "rooms": 2,
      "balcony": "yes",
      "bathroom": "yes"
    }
  ],
  "note": "Images should be added manually through WordPress admin"
}
```

### Create Property

Create a new real estate property.

**Endpoint:** `/properties`

**Method:** `POST`

**Authentication:** Required (Administrator)

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| title | string | Yes | Property title |
| content | string | No | Property description |
| district | string/array | No | District slug(s) |
| building_name | string | No | Building name |
| coordinates | string | No | Coordinates (e.g., "50.4501, 30.5234") |
| floors | integer | No | Number of floors (1-20) |
| building_type | string | No | Building type (panel, brick, foam_block) |
| eco_rating | integer | No | Eco-rating (1-5) |
| premises | array | No | Array of premises objects |

**Example Request:**

```json
POST /wp-json/real-estate/v1/properties

{
  "title": "New Apartment Building",
  "content": "A brand new apartment building in the suburbs...",
  "district": "suburbs",
  "building_name": "Green Valley",
  "coordinates": "50.4601, 30.5334",
  "floors": 8,
  "building_type": "panel",
  "eco_rating": 3,
  "premises": [
    {
      "area": "75",
      "rooms": 2,
      "balcony": "yes",
      "bathroom": "yes"
    },
    {
      "area": "55",
      "rooms": 1,
      "balcony": "no",
      "bathroom": "yes"
    }
  ]
}
```

**Example Response:**

```json
{
  "id": 124,
  "title": "New Apartment Building",
  "content": "A brand new apartment building in the suburbs...",
  "link": "https://speedrun-rpg.hopto.org/real-estate/new-apartment-building",
  "districts": [
    {
      "id": 46,
      "name": "Suburbs",
      "slug": "suburbs"
    }
  ],
  "building_name": "Green Valley",
  "coordinates": "50.4601, 30.5334",
  "floors": 8,
  "building_type": "panel",
  "eco_rating": 3,
  "premises": [
    {
      "area": "75",
      "rooms": 2,
      "balcony": "yes",
      "bathroom": "yes"
    },
    {
      "area": "55",
      "rooms": 1,
      "balcony": "no",
      "bathroom": "yes"
    }
  ],
  "note": "Images should be added manually through WordPress admin"
}
```

### Update Property

Update an existing real estate property.

**Endpoint:** `/properties/{id}`

**Method:** `PUT`

**Authentication:** Required (Administrator)

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Property ID (part of the URL) |
| title | string | No | Property title |
| content | string | No | Property description |
| district | string/array | No | District slug(s) |
| building_name | string | No | Building name |
| coordinates | string | No | Coordinates (e.g., "50.4501, 30.5234") |
| floors | integer | No | Number of floors (1-20) |
| building_type | string | No | Building type (panel, brick, foam_block) |
| eco_rating | integer | No | Eco-rating (1-5) |
| premises | array | No | Array of premises objects |

**Example Request:**

```json
PUT /wp-json/real-estate/v1/properties/124

{
  "title": "Updated Apartment Building",
  "eco_rating": 4,
  "premises": [
    {
      "area": "75",
      "rooms": 2,
      "balcony": "yes",
      "bathroom": "yes"
    },
    {
      "area": "55",
      "rooms": 1,
      "balcony": "no",
      "bathroom": "yes"
    },
    {
      "area": "95",
      "rooms": 3,
      "balcony": "yes",
      "bathroom": "yes"
    }
  ]
}
```

**Example Response:**

```json
{
  "id": 124,
  "title": "Updated Apartment Building",
  "content": "A brand new apartment building in the suburbs...",
  "link": "https://speedrun-rpg.hopto.org/real-estate/updated-apartment-building",
  "districts": [
    {
      "id": 46,
      "name": "Suburbs",
      "slug": "suburbs"
    }
  ],
  "building_name": "Green Valley",
  "coordinates": "50.4601, 30.5334",
  "floors": 8,
  "building_type": "panel",
  "eco_rating": 4,
  "premises": [
    {
      "area": "75",
      "rooms": 2,
      "balcony": "yes",
      "bathroom": "yes"
    },
    {
      "area": "55",
      "rooms": 1,
      "balcony": "no",
      "bathroom": "yes"
    },
    {
      "area": "95",
      "rooms": 3,
      "balcony": "yes",
      "bathroom": "yes"
    }
  ],
  "note": "Images should be added manually through WordPress admin"
}
```

### Delete Property

Delete a real estate property.

**Endpoint:** `/properties/{id}`

**Method:** `DELETE`

**Authentication:** Required (Administrator)

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Property ID (part of the URL) |

**Example Request:**

```
DELETE /wp-json/real-estate/v1/properties/124
```

**Example Response:**

```json
{
  "deleted": true,
  "previous": {
    "id": 124,
    "title": "Updated Apartment Building",
    "content": "A brand new apartment building in the suburbs...",
    "link": "https://speedrun-rpg.hopto.org/real-estate/updated-apartment-building",
    "districts": [
      {
        "id": 46,
        "name": "Suburbs",
        "slug": "suburbs"
      }
    ],
    "building_name": "Green Valley",
    "coordinates": "50.4601, 30.5334",
    "floors": 8,
    "building_type": "panel",
    "eco_rating": 4,
    "premises": [
      {
        "area": "75",
        "rooms": 2,
        "balcony": "yes",
        "bathroom": "yes"
      },
      {
        "area": "55",
        "rooms": 1,
        "balcony": "no",
        "bathroom": "yes"
      },
      {
        "area": "95",
        "rooms": 3,
        "balcony": "yes",
        "bathroom": "yes"
      }
    ],
    "note": "Images should be added manually through WordPress admin"
  }
}
```

## Error Handling

The API returns standard HTTP status codes to indicate the success or failure of a request:

- `200 OK`: The request was successful
- `201 Created`: The resource was successfully created
- `400 Bad Request`: The request was invalid or cannot be served
- `401 Unauthorized`: Authentication is required or failed
- `403 Forbidden`: The authenticated user does not have permission
- `404 Not Found`: The requested resource does not exist
- `500 Internal Server Error`: An error occurred on the server

Error responses include a JSON object with an error message:

```json
{
  "code": "not_found",
  "message": "Property not found",
  "data": {
    "status": 404
  }
}
```

## Testing the API

You can test the API using various tools:

1. **Provided Python scripts**
   - `api_test.py` for comprehensive testing
   - `api_test_simple.py` for simple read-only testing

2. **Third-party API clients**
   - [Postman](https://www.postman.com/)
   - [Insomnia](https://insomnia.rest/)
   - `curl` command-line tool

### Example curl Commands

Get all properties:
```bash
curl -X GET "https://speedrun-rpg.hopto.org/wp-json/real-estate/v1/properties"
```

Get a single property:
```bash
curl -X GET "https://speedrun-rpg.hopto.org/wp-json/real-estate/v1/properties/123"
```

Create a new property (with Application Password authentication):
```bash
curl -X POST "https://speedrun-rpg.hopto.org/wp-json/real-estate/v1/properties" \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic YOUR_BASE64_ENCODED_CREDENTIALS" \
  -d '{
    "title": "New Property",
    "building_name": "Sunset Heights",
    "floors": 5,
    "building_type": "brick",
    "eco_rating": 4
  }'
```

Update a property (with Application Password authentication):
```bash
curl -X PUT "https://speedrun-rpg.hopto.org/wp-json/real-estate/v1/properties/123" \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic YOUR_BASE64_ENCODED_CREDENTIALS" \
  -d '{
    "title": "Updated Property",
    "eco_rating": 5
  }'
```

Delete a property (with Application Password authentication):
```bash
curl -X DELETE "https://speedrun-rpg.hopto.org/wp-json/real-estate/v1/properties/123" \
  -H "Authorization: Basic YOUR_BASE64_ENCODED_CREDENTIALS"
```

To generate the base64 encoded credentials for Basic Authentication:
```bash
echo -n "username:application_password" | base64
```

## XML Support

The API includes built-in XML support. To receive responses in XML format instead of JSON, set the `Accept` header to `application/xml` in your request.

### Example curl Command for XML Response

```bash
curl -X GET "https://speedrun-rpg.hopto.org/wp-json/real-estate/v1/properties" \
  -H "Accept: application/xml"
```

### Example XML Response

```xml
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
        <name>Center</name>
        <slug>center</slug>
      </item>
    </districts>
    <building_name>Sunrise Towers</building_name>
    <coordinates>50.4501, 30.5234</coordinates>
    <floors>12</floors>
    <building_type>brick</building_type>
    <eco_rating>4</eco_rating>
    <premises>
      <item>
        <area>85</area>
        <rooms>3</rooms>
        <balcony>yes</balcony>
        <bathroom>yes</bathroom>
      </item>
      <item>
        <area>65</area>
        <rooms>2</rooms>
        <balcony>yes</balcony>
        <bathroom>yes</bathroom>
      </item>
    </premises>
    <note>Images should be added manually through WordPress admin</note>
  </item>
</response>
```

## Technical Implementation Details

The REST API is implemented in `rest-api.php` with the following key components:

1. Custom endpoints registered via `register_rest_route()`
2. Authentication checks using WordPress capabilities
3. XML format support through the `rest_pre_serve_request` filter
4. ACF field integration for custom fields

The XML conversion is handled by the `convert_to_xml()` method using PHP's SimpleXMLElement class.
