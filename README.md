# EANGUS Leadership Directory Plugin

A custom WordPress plugin to **manage and display the EANGUS leadership directory** in a secure, professional, and easy-to-update way. Designed for non-technical administrators, this plugin keeps directory data cleanly separated from the site's theme, ensuring long-term flexibility and stability.

---

## Features

- **Admin-Only Access**: Only logged-in users with admin permissions can add or modify leadership entries.
- **No Coding Required**: All data entry is handled via intuitive admin forms in the WordPress dashboard.
- **Custom Shortcodes**: Use `[state_directory]` to render the complete directory anywhere on the front-end.
- **Custom CSS Styling**: Clean and responsive design using custom CSS for modern, professional appearance.
- **Modular Design**: Built as a self-contained plugin, fully decoupled from any specific WordPress theme.
- **Expandable**: Sections include Executive Officers, Area Chairs, State Leadership, Past Presidents, and Annual Conferences.
- **Interactive Interface**: Collapsible sections with smooth toggle functionality for better user experience.

---

## 🛠️ Technical Overview

This plugin uses:

- **PHP (WordPress API)**: To register shortcodes, forms, and admin pages.
- **Custom CSS**: Professional styling with responsive design and interactive elements.
- **Vanilla JavaScript**: Enables interactive toggle sections and filtering functionality.
- **Unified Database Table**: Single `wp_eangus_directory` table for all directory data with type-based organization.
- **Shortcode**: `[state_directory]` – displays the complete interactive directory

### Directory Sections Included:
- **Executive Officers** - Current national leadership
- **Area Leadership** - Area chairs and directors organized by geographical area
- **State Leadership** - Council members organized alphabetically by state
- **Annual Conferences** - Historical conference data grouped by decade
- **Past Presidents** - Historical presidential records grouped by decade

---

## How It Works

### Admin Dashboard

1. After installing the plugin, admins will see **"Leadership Directory"** under the **Settings** menu in the WordPress admin.
2. The settings page contains tabs for each directory section:
   - Executive Officers
   - Area Chairs
   - State Leadership
   - Past Presidents
   - Annual Conferences
3. Admins can add new entries via secure, sanitized input forms with helpful placeholders and instructions.
4. Data is stored in a unified `wp_eangus_directory` table with automatic duplicate handling.

### Front-End Display

1. Add the `[state_directory]` shortcode to any page or post.
2. The plugin renders a fully responsive, interactive directory with:
   - **Hero section** with navigation links to different sections
   - **Collapsible sections** that can be expanded/collapsed by users
   - **Alphabetical grouping** for states and decades for historical data
   - **Area-based organization** for geographical leadership structure
   - **Professional styling** with cards, buttons, and smooth transitions

---

## Installation

1. Upload the plugin folder to your `/wp-content/plugins/` directory.
2. Activate it from the WordPress Plugin Dashboard.
3. Visit **Settings → Leadership Directory** in the admin menu to begin adding entries.
4. Add `[state_directory]` to any page or post to display the complete directory.

---

## Database Structure

The plugin uses a single unified table `wp_eangus_directory` with the following key fields:

- `type` - Identifies the entry type (exec_officer, area_chair, state_council, past_president, conference)
- `area` - Geographical area designation
- `state` - State name for state-level entries
- `position` - Leadership position/title
- `rank`, `first_name`, `last_name` - Personal information
- `email`, `phone_mobile`, `phone_office` - Contact information
- `term_start`, `term_end` - Service period
- `location`, `date_range` - Conference-specific fields

---

## Maintenance & Updates

### Adding/Updating Information
- Go to **Settings → Leadership Directory** in WordPress admin
- Select the appropriate tab for your entry type
- Fill out the form with the required information
- Click "Add Entry" (existing entries with matching criteria will be updated automatically)

### Styling Updates
- Modify `/assets/css/styles.css` for visual changes
- The CSS includes custom classes for hero sections, cards, toggles, and responsive design
- No external framework dependencies

### Adding New Fields
- Update the form arrays in `/includes/class-sdp-admin.php`
- Modify the database schema if needed
- Update the renderer methods in `/includes/class-sdp-renderer.php`

---

## File Structure

```
state-directory-plugin/
├── state-directory-plugin.php          # Main plugin file
├── README.md                           # This file
├── assets/
│   ├── css/styles.css                  # Custom CSS styling
│   └── js/script.js                    # Interactive JavaScript
└── includes/
    ├── class-sdp-admin.php             # Admin interface and forms
    ├── class-sdp-renderer.php          # Front-end rendering logic
    └── class-sdp-shortcode.php         # Shortcode handler
```

---

## Shortcode Usage

Simply add `[state_directory]` to any page or post where you want the directory to appear. The shortcode will render the complete directory with all sections and interactive features.

---

## Support & Customization

This plugin is designed to be easily customizable. Key areas for customization include:

- **Styling**: Modify `assets/css/styles.css` for visual changes
- **Fields**: Add new form fields in the admin class
- **Sections**: Create new directory sections by adding new types and renderers
- **Layout**: Adjust the HTML structure in the renderer classes

---

## Author

**Saleha Iftikhar**  
Intern | Enlisted Association of the National Guard of the United States (EANGUS)

---

## Version History

- **v1.2**: Enhanced Filters and UI enhancements.
- **v1.1**: Unified database structure, improved admin interface, enhanced front-end interactions
- **v1.0**: Initial release with basic directory functionality
