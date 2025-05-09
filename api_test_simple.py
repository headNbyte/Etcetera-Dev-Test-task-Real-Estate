#!/usr/bin/env python3
"""
Simple Real Estate Objects API Test Script

This script tests the Real Estate Objects WordPress plugin API endpoints using basic methods.
"""

import requests
import json
import sys
from pprint import pprint

# Configuration
WP_URL = "http://speedrun-rpg.hopto.org"
API_BASE = "/wp-json/real-estate/v1"

class SimpleApiTester:
    def __init__(self, wp_url=WP_URL, api_base=API_BASE):
        """Initialize the tester with WordPress URL and API base path"""
        self.wp_url = wp_url
        self.api_base = api_base
        self.test_results = {
            'passed': 0,
            'failed': 0,
            'details': []
        }

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

    def print_response_info(self, response):
        """Print detailed information about the API response for debugging"""
        print(f"Status Code: {response.status_code}")
        print(f"Response Headers: {response.headers}")
        print("Response Content:")
        try:
            print(json.dumps(response.json(), indent=2))
        except ValueError:
            print(f"Raw content (not JSON):\n{response.text[:500]}...")
        print("=" * 50)

    def test_core_wp_api(self):
        """Test if the WordPress core REST API is working"""
        print("\n🔍 Testing WordPress core REST API...")
        try:
            response = requests.get(f"{self.wp_url}/wp-json/")
            self.print_response_info(response)
            success = response.status_code == 200
            self.record_test_result("WordPress core REST API", success,
                                   None if success else f"Status code: {response.status_code}")
            return success
        except Exception as e:
            print(f"Error: {e}")
            self.record_test_result("WordPress core REST API", False, str(e))
            return False

    def check_plugin_status(self):
        """Check if the plugin is properly registered with WP REST API"""
        print("\n🔍 Checking plugin routes in WP REST API...")
        try:
            response = requests.get(f"{self.wp_url}/wp-json/")
            data = response.json()

            # Look for our namespace in the routes
            namespaces = data.get('namespaces', [])
            routes = data.get('routes', {})

            print(f"Available namespaces: {namespaces}")
            print(f"Looking for 'real-estate/v1' namespace...")

            # Check specific routes
            real_estate_routes = [route for route in routes.keys() if 'real-estate/v1' in route]
            print(f"Found real estate routes: {real_estate_routes}")

            success = 'real-estate/v1' in namespaces
            self.record_test_result("Plugin REST API registration", success,
                                   None if success else "Namespace not found")
            return success
        except Exception as e:
            print(f"Error checking plugin status: {e}")
            self.record_test_result("Plugin REST API registration", False, str(e))
            return False

    def test_get_properties(self):
        """Test the GET /properties endpoint"""
        print("\n🔍 Testing GET /properties endpoint...")
        try:
            response = requests.get(f"{self.wp_url}{self.api_base}/properties")
            self.print_response_info(response)
            success = response.status_code == 200
            self.record_test_result("GET /properties", success,
                                  None if success else f"Status code: {response.status_code}")
            return success
        except Exception as e:
            print(f"Error: {e}")
            self.record_test_result("GET /properties", False, str(e))
            return False

    def test_get_property(self, property_id=1):
        """Test the GET /properties/{id} endpoint"""
        print(f"\n🔍 Testing GET /properties/{property_id} endpoint...")
        try:
            response = requests.get(f"{self.wp_url}{self.api_base}/properties/{property_id}")
            self.print_response_info(response)
            success = response.status_code == 200
            self.record_test_result(f"GET /properties/{property_id}", success,
                                  None if success else f"Status code: {response.status_code}")
            return success
        except Exception as e:
            print(f"Error: {e}")
            self.record_test_result(f"GET /properties/{property_id}", False, str(e))
            return False

    def test_filter_properties(self):
        """Test filtering properties by various criteria"""
        print("\n🔍 Testing property filtering...")
        try:
            params = {
                'building_type': 'brick',
                'district': 'central',
                'min_eco_rating': 3
            }
            response = requests.get(f"{self.wp_url}{self.api_base}/properties", params=params)
            self.print_response_info(response)
            success = response.status_code == 200
            self.record_test_result("Property filtering", success,
                                  None if success else f"Status code: {response.status_code}")
            return success
        except Exception as e:
            print(f"Error: {e}")
            self.record_test_result("Property filtering", False, str(e))
            return False

    def test_xml_format(self):
        """Test API response in XML format"""
        print("\n🔍 Testing XML format response...")
        try:
            headers = {'Accept': 'application/xml'}
            response = requests.get(f"{self.wp_url}{self.api_base}/properties", headers=headers)
            self.print_response_info(response)
            success = response.status_code == 200 and 'application/xml' in response.headers.get('Content-Type', '')
            self.record_test_result("XML format", success,
                                  None if success else "Response not in XML format")
            return success
        except Exception as e:
            print(f"Error: {e}")
            self.record_test_result("XML format", False, str(e))
            return False

    def print_test_summary(self):
        """Print a summary of test results"""
        print("\n====== TEST SUMMARY ======")

        total_tests = self.test_results['passed'] + self.test_results['failed']
        success_rate = (self.test_results['passed'] / total_tests) * 100 if total_tests > 0 else 0

        print(f"Tests completed: {total_tests}")
        print(f"Tests passed:    {self.test_results['passed']} ({success_rate:.1f}%)")
        print(f"Tests failed:    {self.test_results['failed']}")

        if self.test_results['failed'] > 0:
            print("\nFailed tests:")
            for test in self.test_results['details']:
                if test['status'] == "FAIL":
                    message = test['message'] if test['message'] else "Unknown error"
                    print(f"  ❌ {test['name']}: {message}")

        if self.test_results['passed'] == total_tests:
            print("\n🎉 All tests passed successfully!")

        print("==========================")

    def run_all_tests(self):
        """Run all API tests"""
        print("\n====== REAL ESTATE OBJECTS API TEST ======\n")

        # First check core WP API
        wp_api_works = self.test_core_wp_api()

        # Check plugin registration
        plugin_registered = self.check_plugin_status()

        # Only continue with other tests if the core API works
        if wp_api_works:
            # Try to get a property ID for individual property test
            property_id = None
            try:
                response = requests.get(f"{self.wp_url}{self.api_base}/properties")
                if response.status_code == 200:
                    data = response.json()
                    if data and len(data) > 0:
                        property_id = data[0].get('id')
            except:
                pass

            # Test get properties
            self.test_get_properties()

            # Test get single property if we found an ID
            if property_id:
                self.test_get_property(property_id)
            else:
                # Try with ID 1 if no properties found
                self.test_get_property()

            # Test filtering
            self.test_filter_properties()

            # Test XML format
            self.test_xml_format()

        # Print test summary
        self.print_test_summary()

def main():
    """Main function to run tests"""
    tester = SimpleApiTester()
    tester.run_all_tests()

if __name__ == "__main__":
    main()
