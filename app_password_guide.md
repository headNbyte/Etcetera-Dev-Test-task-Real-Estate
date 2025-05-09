# Using Application Passwords with the Real Estate API Test Script

## Setting Up Application Passwords in WordPress

1. **Log into your WordPress admin panel**

2. **Navigate to Users → Profile**

3. **Scroll down to the "Application Passwords" section**

4. **Add a new application password:**
   - Enter a name for the application (e.g., "API Testing")
   - Click "Add New Application Password"
   - WordPress will generate a new password
   - **IMPORTANT:** Copy this password immediately as WordPress will only show it once

## Running the API Test Script

Now that you have an application password, you can run the updated test script:

```bash
python3 api_test.py http://speedrun-rpg.hopto.org -u admin -p your_application_password
```

Replace:
- `admin` with your WordPress username
- `your_application_password` with the application password you just generated

## How Application Passwords Work

Application passwords are a secure way to authorize API access without using your main WordPress password:

- They provide limited scope access (API only)
- They can be revoked individually without changing your main password
- They use HTTP Basic Authentication, which is widely supported

## Troubleshooting

If you encounter authentication issues:

1. Make sure you're using your application password, not your regular WordPress password
2. Verify the application password hasn't been revoked in the WordPress admin
3. Check that your username is correct
4. Verify that your REST API is properly configured (permalinks set to Post Name)

## Security Note

While application passwords are more secure than using your main WordPress password, you should still take precautions:

- Use HTTPS for all API requests in production
- Revoke application passwords when they're no longer needed
- Give each application or script its own unique application password
- Restrict application passwords to specific IP addresses if possible in your hosting environment
