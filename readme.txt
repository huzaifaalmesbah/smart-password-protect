=== Smart Password Protect ===
Contributors: huzaifaalmesbah
Tags: password protection, IP protection, Restrict Content
Requires at least: 5.6
Requires PHP: 7.0
Tested up to: 6.6.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Protect your WordPress site with a password or by allowing access only to specified IP addresses.

== Description ==

Smart Password Protect allows you to secure your WordPress site by requiring users to enter a password or by restricting access to certain IP addresses. Whether you're running a private blog or a membership site, this plugin provides an additional layer of security.

Features:
- Enable password protection for your site.
- Specify allowed IP addresses that can access your site without a password.
- Easy-to-use admin settings page to configure the plugin.

== Installation ==

1. Upload the `smart-password-protect` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to 'Settings' -> 'Smart Password Protect' to configure your settings.

== Frequently Asked Questions ==

= How do I enable password protection? =
To enable password protection, navigate to the Smart Password Protect settings page and check the "Enable Protection" option. Enter the desired password.

= How do I allow specific IP addresses? =
You can add allowed IP addresses in the IP Settings section of the Smart Password Protect settings page. Enter the IP address and click "Add IP" to save it.

= What happens if I forget the password? =
You will need to access your WordPress admin dashboard to change or reset the password if you've forgotten it.

= Is this plugin compatible with my theme? =
The Smart Password Protect plugin is compatible with most WordPress themes. If you encounter any issues, please contact support.

== Screenshots ==

1. General Settings Page - A screenshot of the plugin's General Settings page, highlighting the options to enable protection and set the password.

2. IP Settings Page - A screenshot of the plugin's IP Settings page, showcasing the options to manage Allowed IP Addresses.

3. Frontend Protection - A screenshot displaying the password prompt that users see when trying to access the protected site.

== Privacy and Policy ==

Please note that this plugin retrieves the public IP address of users using reliable external services for enhanced security. The following services may be used to obtain the public IP address:

- [AWS Check IP](http://checkip.amazonaws.com) - Amazon Web Services
- [IPEcho](https://ipecho.net/plain)

Your privacy is important to us, and we do not store or share your IP address with any third parties.

### Links to Services

1. **AWS Check IP**
   - [Service Endpoint](http://checkip.amazonaws.com)
   - [Privacy Policy](https://aws.amazon.com/privacy/)
   - [Terms of Service](https://aws.amazon.com/service-terms/)

2. **IPEcho**
   - [Service Endpoint](https://ipecho.net/plain)
   - [Privacy Policy](https://ipecho.net/developers.html)
   - [Terms of Service](https://ipecho.net/developers.html)

Remember, this is for your own legal protection. Use of services must be upfront and well documented.
== Changelog ==

= 1.0.0 =
* Initial release of the Smart Password Protect plugin.

== Upgrade Notice ==

= 1.0.0 =
Initial release. Upgrade to this version for password and IP protection features.
