#!/usr/bin/env python3
"""
Mock Real Estate Entry Generator

This script creates mock real estate entries using the WordPress REST API
without handling any SVG images, as those will be managed manually in WP admin.
"""

import os
import sys
import requests
import json
import base64
import random
import argparse
from pathlib import Path

class MockDataGenerator:
    """Class to generate mock real estate entries"""

    def __init__(self, base_url, username, password):
        """Initialize with WordPress credentials"""
        self.base_url = base_url.rstrip('/')
        self.api_base = f"{self.base_url}/wp-json/real-estate/v1"
        self.wp_api_base = f"{self.base_url}/wp-json/wp/v2"
        self.username = username
        self.password = password
        self.auth_header = None
        self.districts = []

        # Set up authentication
        self.setup_auth()

        # Get or create districts
        self.ensure_districts()

    def setup_auth(self):
        """Set up HTTP Basic Authentication"""
        if self.username and self.password:
            credentials = f"{self.username}:{self.password}"
            encoded_credentials = base64.b64encode(credentials.encode('utf-8')).decode('utf-8')
            self.auth_header = f"Basic {encoded_credentials}"
            print("✅ Authentication configured")

            # Test authentication
            self.test_auth()
        else:
            print("❌ Username and password required")
            sys.exit(1)

    def test_auth(self):
        """Test if authentication works"""
        headers = {'Authorization': self.auth_header}

        try:
            response = requests.get(f"{self.base_url}/wp-json/wp/v2/users/me", headers=headers)

            if response.status_code == 200:
                user_data = response.json()
                print(f"✅ Authentication successful - Logged in as: {user_data.get('name')}")
            else:
                print(f"❌ Authentication failed: {response.status_code}")
                print(response.text)
                sys.exit(1)
        except Exception as e:
            print(f"❌ Authentication error: {str(e)}")
            sys.exit(1)

    def ensure_districts(self):
        """Get existing districts or create new ones if needed"""
        district_names = ['Центральний', 'Північний', 'Південний', 'Східний', 'Західний']
        district_slugs = ['central', 'northern', 'southern', 'eastern', 'western']

        # Check if districts already exist
        try:
            # Get all terms from the district taxonomy
            headers = {'Authorization': self.auth_header}
            response = requests.get(f"{self.wp_api_base}/district", headers=headers)

            if response.status_code == 200:
                existing_districts = response.json()
                if existing_districts:
                    self.districts = existing_districts
                    print(f"✅ Found {len(existing_districts)} existing districts")
                    return

            # If we get here, we need to create the districts
            for i, name in enumerate(district_names):
                data = {
                    'name': name,
                    'slug': district_slugs[i],
                    'description': f'Properties in the {name} district'
                }

                response = requests.post(
                    f"{self.wp_api_base}/district",
                    headers={
                        'Authorization': self.auth_header,
                        'Content-Type': 'application/json'
                    },
                    data=json.dumps(data)
                )

                if response.status_code == 201:
                    district = response.json()
                    self.districts.append(district)
                    print(f"✅ Created district: {name}")
                else:
                    print(f"❌ Failed to create district {name}: {response.status_code}")

            print(f"✅ Created {len(self.districts)} districts")

        except Exception as e:
            print(f"❌ Error ensuring districts: {str(e)}")
            sys.exit(1)

    def random_building_data(self):
        """Generate random building data"""
        building_types = ['panel', 'brick', 'foam_block']
        building_type_names = {
            'panel': 'Панель',
            'brick': 'Цегла',
            'foam_block': 'Піноблок'
        }
        building_names = [
            'Сонячний', 'Затишний', 'Престижний', 'Ексклюзивний', 'Елітний',
            'Парковий', 'Центральний', 'Новий', 'Сучасний', 'Комфортний'
        ]

        # Random district
        district = random.choice(self.districts)

        # Random building data
        building_type = random.choice(building_types)
        floors = random.randint(5, 25)
        eco_rating = random.randint(1, 5)
        building_name = f"{random.choice(building_names)} {random.randint(1, 100)}"

        # Random coordinates (Kyiv area)
        lat = round(random.uniform(50.38, 50.52), 6)
        lon = round(random.uniform(30.28, 30.71), 6)
        coordinates = f"{lat}, {lon}"

        # Generate 1-5 random premises
        premises = []
        num_premises = random.randint(1, 5)
        for _ in range(num_premises):
            area = round(random.uniform(50, 150), 1)
            rooms = random.randint(1, 4)
            balcony = random.choice(['yes', 'no'])
            bathroom = random.choice(['yes', 'no'])

            premises.append({
                'area': str(area),
                'rooms': rooms,
                'balcony': balcony,
                'bathroom': bathroom
            })

        return {
            'district': district['slug'],
            'building_name': building_name,
            'building_type': building_type,
            'floors': floors,
            'eco_rating': eco_rating,
            'coordinates': coordinates,
            'premises': premises
        }

    def create_property(self, index):
        """Create a single property without image handling"""
        building_data = self.random_building_data()

        # Create a title for the property
        title = f"Будинок {building_data['building_name']} ({index + 1})"

        building_type_names = {
            'panel': 'Панель',
            'brick': 'Цегла',
            'foam_block': 'Піноблок'
        }

        # Find district name from slug and use correct grammatical case for Ukrainian
        district_locative = {
            'central': 'Центральному',
            'northern': 'Північному',
            'southern': 'Південному',
            'eastern': 'Східному',
            'western': 'Західному'
        }

        district_name = district_locative.get(building_data['district'], "Центральному")  # Default fallback

        # Create property data
        property_data = {
            'title': title,
            'content': f"Сучасний будинок {building_data['building_name']} у {district_name} районі. "
                       f"Тип: {building_type_names[building_data['building_type']]}, "
                       f"поверхів: {building_data['floors']}, "
                       f"екологічний рейтинг: {building_data['eco_rating']}. "
                       f"Розташування: {building_data['coordinates']}.",
            'district': building_data['district'],
            'building_name': building_data['building_name'],
            'coordinates': building_data['coordinates'],
            'floors': building_data['floors'],
            'building_type': building_data['building_type'],
            'eco_rating': building_data['eco_rating'],
            'premises': building_data['premises']
        }

        headers = {
            'Authorization': self.auth_header,
            'Content-Type': 'application/json'
        }

        try:
            # Create the property
            response = requests.post(
                f"{self.api_base}/properties",
                headers=headers,
                data=json.dumps(property_data)
            )

            if response.status_code in [200, 201]:
                data = response.json()
                property_id = data.get('id')
                print(f"✅ Created property: {title} (ID: {property_id})")
                return property_id
            else:
                print(f"❌ Failed to create property {title}: {response.status_code}")
                print(response.text)
                return None

        except Exception as e:
            print(f"❌ Error creating property: {str(e)}")
            return None

    def generate_mock_data(self, count):
        """Generate a specified number of mock properties"""
        print(f"\n=== Generating {count} mock real estate properties ===\n")

        created_count = 0
        for i in range(count):
            if self.create_property(i):
                created_count += 1

        print(f"\n=== Mock data generation complete ===")
        print(f"Successfully created {created_count} out of {count} properties")
        print(f"\nNote: No images were uploaded. Please add images manually through the WordPress admin.")

        return created_count

def main():
    """Main function to parse arguments and run the generator"""
    parser = argparse.ArgumentParser(description='Generate mock real estate entries')
    parser.add_argument('url', help='WordPress site URL')
    parser.add_argument('-u', '--username', required=True, help='WordPress username')
    parser.add_argument('-p', '--password', required=True, help='WordPress application password')
    parser.add_argument('-c', '--count', type=int, default=8, help='Number of entries to generate (default: 8)')

    args = parser.parse_args()

    generator = MockDataGenerator(args.url, args.username, args.password)
    generator.generate_mock_data(args.count)

if __name__ == "__main__":
    main()
