# EANGUS-Directory
Custom Word Press plugin to manage and display the Company Directory in a secure, easy-to-update, and professional way.

EANGUS - Company Directory Plan of Action 

Overview 

I will build a custom WordPress plugin to manage and display the Company Directory in a secure, easy-to-update, and professional way. 
 
This plugin will allow administrators to add, update, and manage state leadership information without needing any coding - all through a simple form in the WordPress dashboard. 
 
The plugin will make the directory independent from the website design, so future updates to the website theme will not affect the directory’s functionality. 

How It Will Work 

- Secure Access: Only administrators will be able to add, edit, or delete directory entries. 
- User-Friendly Form: Admins can log in, click on the State Directory menu, and fill out a simple form to add or update information. 
- Professional Front-End: Visitors will see a clean, organized directory with each state’s logo, leadership information, website link, and conference dates. 
- Independent System: The directory will operate through a plugin, keeping it separate from the website’s theme and easy to maintain. 

Plugin Structure 

The plugin will follow a professional, organized folder structure: 
 
state-directory-plugin/ 
│-- state-directory-plugin.php          	← Main plugin file (entry point) 
│-- includes/ 
│   │-- class-sdp-database.php         	← Handles database setup 
│   │-- class-sdp-admin.php             	← Handles admin dashboard forms 
│   │-- class-sdp-shortcode.php        	← Handles the front-end display 
│-- assets/ 
│   │-- css/ 
│   │   │-- styles.css                  	       	← Front-end styles (for layout and design) 
│   │-- js/ 
│   │   │-- script.js                    	       	← JavaScript for interactivity 
│-- uninstall.php                      ← Removes the directory database table if the plugin is uninstalled 

 

 

How the Directory Will Be Displayed 

- After creating and activating the plugin, a new page will be added to the website where the directory will be visible. 
- The administrator will simply add a special shortcode like [state_directory] to a page, and the directory will automatically appear. 
- No coding or technical skills are needed to update or add information - only simple form submissions through the admin panel. 

 
