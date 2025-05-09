#!/usr/bin/env python3
"""
Real Estate Objects API Test Script

This script tests the Real Estate Objects WordPress plugin API endpoints.
"""

import requests
import json
import sys
import argparse
import base64
from pprint import pprint

class RealEstateApiTester:
    """Class to test the Real Estate Objects API"""

    def __init__(self, base_url, username=None, password=None):
        """Initialize with the WordPress site URL"""
        self.base_url = base_url.rstrip('/')
        self.api_base = f"{self.base_url}/wp-json/real-estate/v1"
        self.username = username
        self.password = password
        self.auth_header = None
        self.test_results = {
            'passed': 0,
            'failed': 0,
            'details': []
        }

        if username and password:
            # Set up basic auth for application passwords
            self.setup_auth()

    def record_test_result(self, test_name, success, message=None):
        """Record the result of a test"""
        status = "PASS" if success else "FAIL"
        self.test_results['details'].append({
            'name': test_name,
            'status': status,
            'message': message
        })

        if success:
            self.test_results['passed'] += 1
        else:
            self.test_results['failed'] += 1

        return success

    def setup_auth(self):
        """Set up HTTP Basic Authentication using Application Password"""
        if self.username and self.password:
            # Create the basic auth header
            credentials = f"{self.username}:{self.password}"
            encoded_credentials = base64.b64encode(credentials.encode('utf-8')).decode('utf-8')
            self.auth_header = f"Basic {encoded_credentials}"
            print("✓ Authentication configured")

            # Verify authentication works
            self.test_auth()
        else:
            print("✗ Username and password required for authentication")
            self.record_test_result("Authentication configuration", False, "Missing credentials")

    def test_auth(self):
        """Test if authentication works by attempting to access a protected endpoint"""
        print("\n=== Testing Authentication ===")

        headers = {'Authorization': self.auth_header}

        try:
            # Try to access a WordPress endpoint that requires authentication
            response = requests.get(f"{self.base_url}/wp-json/wp/v2/users/me", headers=headers)

            if response.status_code == 200:
                user_data = response.json()
                print(f"✓ Authentication successful - Logged in as: {user_data.get('name')}")
                self.record_test_result("Authentication", True)
                return True
            else:
                print(f"✗ Authentication failed: {response.status_code}")
                print(response.text)
                self.auth_header = None  # Reset auth if failed
                self.record_test_result("Authentication", False, f"Status code: {response.status_code}")
                return False
        except Exception as e:
            print(f"✗ Authentication error: {str(e)}")
            self.auth_header = None  # Reset auth if error
            self.record_test_result("Authentication", False, str(e))
            return False

    def test_get_properties(self):
        """Test GET /properties endpoint"""
        print("\n=== Testing GET /properties ===")

        try:
            response = requests.get(f"{self.api_base}/properties")

            if response.status_code == 200:
                data = response.json()
                print(f"✓ GET /properties successful - Found {len(data)} properties")

                # Print first property if available
                if data and len(data) > 0:
                    print("\nSample property:")
                    pprint(data[0])

                self.record_test_result("GET /properties", True)
                return True
            else:
                print(f"✗ GET /properties failed: {response.status_code}")
                print(response.text)
                self.record_test_result("GET /properties", False, f"Status code: {response.status_code}")
                return False
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            self.record_test_result("GET /properties", False, str(e))
            return False

    def test_get_property(self, property_id):
        """Test GET /properties/{id} endpoint"""
        print(f"\n=== Testing GET /properties/{property_id} ===")

        try:
            response = requests.get(f"{self.api_base}/properties/{property_id}")

            if response.status_code == 200:
                data = response.json()
                print(f"✓ GET /properties/{property_id} successful")
                pprint(data)
                self.record_test_result(f"GET /properties/{property_id}", True)
                return True
            else:
                print(f"✗ GET /properties/{property_id} failed: {response.status_code}")
                print(response.text)
                self.record_test_result(f"GET /properties/{property_id}", False, f"Status code: {response.status_code}")
                return False
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            self.record_test_result(f"GET /properties/{property_id}", False, str(e))
            return False

    def test_filter_properties(self):
        """Test filtering properties"""
        print("\n=== Testing GET /properties with filters ===")

        filters = {
            'district': 'central',
            'building_type': 'brick',
            'min_eco_rating': 3
        }

        try:
            response = requests.get(f"{self.api_base}/properties", params=filters)

            if response.status_code == 200:
                data = response.json()
                print(f"✓ GET /properties with filters successful - Found {len(data)} properties")

                # Print first property if available
                if data and len(data) > 0:
                    print("\nSample filtered property:")
                    pprint(data[0])

                self.record_test_result("GET /properties with filters", True)
                return True
            else:
                print(f"✗ GET /properties with filters failed: {response.status_code}")
                print(response.text)
                self.record_test_result("GET /properties with filters", False, f"Status code: {response.status_code}")
                return False
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            self.record_test_result("GET /properties with filters", False, str(e))
            return False

    def test_create_property(self):
        """Test POST /properties endpoint"""
        print("\n=== Testing POST /properties ===")

        if not self.auth_header:
            print("✗ Authentication required for this operation")
            self.record_test_result("POST /properties", False, "Authentication required")
            return False

        property_data = {
            'title': 'Test Property',
            'content': 'This is a test property created via API',
            'district': 'central',
            'building_name': 'Test Building',
            'coordinates': '50.4501, 30.5234',
            'floors': 5,
            'building_type': 'brick',
            'eco_rating': 4,
            'premises': [
                {
                    'area': '75',
                    'rooms': 2,
                    'balcony': 'yes',
                    'bathroom': 'yes'
                },
                {
                    'area': '45',
                    'rooms': 1,
                    'balcony': 'no',
                    'bathroom': 'yes'
                },
                {
                    'area': '120',
                    'rooms': 3,
                    'balcony': 'yes',
                    'bathroom': 'yes'
                }
            ]
        }

        headers = {
            'Authorization': self.auth_header,
            'Content-Type': 'application/json'
        }

        try:
            response = requests.post(
                f"{self.api_base}/properties",
                headers=headers,
                data=json.dumps(property_data)
            )

            if response.status_code in [200, 201]:
                data = response.json()
                print(f"✓ POST /properties successful - Created property with ID: {data.get('id')}")
                self.record_test_result("POST /properties", True)
                return data.get('id')
            else:
                print(f"✗ POST /properties failed: {response.status_code}")
                print(response.text)
                self.record_test_result("POST /properties", False, f"Status code: {response.status_code}")
                return None
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            self.record_test_result("POST /properties", False, str(e))
            return None

    def test_update_property(self, property_id):
        """Test PUT /properties/{id} endpoint"""
        print(f"\n=== Testing PUT /properties/{property_id} ===")

        if not self.auth_header:
            print("✗ Authentication required for this operation")
            self.record_test_result(f"PUT /properties/{property_id}", False, "Authentication required")
            return False

        update_data = {
            'title': 'Updated Test Property',
            'eco_rating': 5
        }

        headers = {
            'Authorization': self.auth_header,
            'Content-Type': 'application/json'
        }

        try:
            response = requests.put(
                f"{self.api_base}/properties/{property_id}",
                headers=headers,
                data=json.dumps(update_data)
            )

            if response.status_code == 200:
                data = response.json()
                print(f"✓ PUT /properties/{property_id} successful")
                print(f"  Title updated to: {data.get('title')}")
                print(f"  Eco-rating updated to: {data.get('eco_rating')}")
                self.record_test_result(f"PUT /properties/{property_id}", True)
                return True
            else:
                print(f"✗ PUT /properties/{property_id} failed: {response.status_code}")
                print(response.text)
                self.record_test_result(f"PUT /properties/{property_id}", False, f"Status code: {response.status_code}")
                return False
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            self.record_test_result(f"PUT /properties/{property_id}", False, str(e))
            return False

    def test_delete_property(self, property_id):
        """Test DELETE /properties/{id} endpoint"""
        print(f"\n=== Testing DELETE /properties/{property_id} ===")

        if not self.auth_header:
            print("✗ Authentication required for this operation")
            self.record_test_result(f"DELETE /properties/{property_id}", False, "Authentication required")
            return False

        headers = {
            'Authorization': self.auth_header
        }

        try:
            response = requests.delete(
                f"{self.api_base}/properties/{property_id}",
                headers=headers
            )

            if response.status_code == 200:
                data = response.json()
                print(f"✓ DELETE /properties/{property_id} successful")
                print(f"  Deleted: {data.get('deleted')}")
                self.record_test_result(f"DELETE /properties/{property_id}", True)
                return True
            else:
                print(f"✗ DELETE /properties/{property_id} failed: {response.status_code}")
                print(response.text)
                self.record_test_result(f"DELETE /properties/{property_id}", False, f"Status code: {response.status_code}")
                return False
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            self.record_test_result(f"DELETE /properties/{property_id}", False, str(e))
            return False

    def test_xml_format(self):
        """Test API response in XML format"""
        print("\n=== Testing XML format ===")

        headers = {
            'Accept': 'application/xml'
        }

        try:
            response = requests.get(f"{self.api_base}/properties", headers=headers)

            if response.status_code == 200:
                if 'application/xml' in response.headers.get('Content-Type', ''):
                    print("✓ XML format response successful")
                    print("\nSample XML response (first 500 chars):")
                    print(response.text[:500] + "...")
                    self.record_test_result("XML format", True)
                    return True
                else:
                    print("✗ Response is not in XML format")
                    print(f"Content-Type: {response.headers.get('Content-Type')}")
                    self.record_test_result("XML format", False, "Response not in XML format")
                    return False
            else:
                print(f"✗ XML format request failed: {response.status_code}")
                print(response.text)
                self.record_test_result("XML format", False, f"Status code: {response.status_code}")
                return False
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            self.record_test_result("XML format", False, str(e))
            return False

    def print_test_summary(self):
        """Print a summary of test results"""
        print("\n===== TEST SUMMARY =====")

        total_tests = self.test_results['passed'] + self.test_results['failed']
        success_rate = (self.test_results['passed'] / total_tests) * 100 if total_tests > 0 else 0

        print(f"Tests completed: {total_tests}")
        print(f"Tests passed:    {self.test_results['passed']} ({success_rate:.1f}%)")
        print(f"Tests failed:    {self.test_results['failed']}")

        if self.test_results['failed'] > 0:
            print("\nFailed tests:")
            for test in self.test_results['details']:
                if test['status'] == "FAIL":
                    print(f"  ✗ {test['name']}: {test['message']}")

        if self.test_results['passed'] == total_tests:
            print("\n🎉 All tests passed successfully!")

        print("=========================")

    def run_all_tests(self):
        """Run all API tests"""
        print("Starting Real Estate Objects API Tests")
        print("=====================================")

        # Test GET endpoints
        self.test_get_properties()

        # Get first property ID for individual tests
        try:
            response = requests.get(f"{self.api_base}/properties")
            if response.status_code == 200:
                data = response.json()
                if data and len(data) > 0:
                    first_property_id = data[0].get('id')
                    self.test_get_property(first_property_id)
        except:
            self.record_test_result("GET individual property", False, "Could not find property ID")
            pass

        # Test filters
        self.test_filter_properties()

        # Test XML format
        self.test_xml_format()

        # Test write operations if authenticated
        if self.auth_header:
            # Create a new property
            new_property_id = self.test_create_property()

            if new_property_id:
                # Update the property
                self.test_update_property(new_property_id)

                # Delete the property
                self.test_delete_property(new_property_id)
            else:
                self.record_test_result("Property lifecycle tests", False, "Could not create property")

        print("\nAPI Tests Completed")
        print("=====================================")

        # Print test summary
        self.print_test_summary()

def main():
    """Main function to parse arguments and run tests"""
    parser = argparse.ArgumentParser(description='Test Real Estate Objects WordPress API')
    parser.add_argument('url', help='WordPress site URL')
    parser.add_argument('-u', '--username', help='WordPress username for authenticated tests')
    parser.add_argument('-p', '--password', help='WordPress application password for authenticated tests')

    args = parser.parse_args()

    tester = RealEstateApiTester(args.url, args.username, args.password)
    tester.run_all_tests()

if __name__ == "__main__":
    main()
