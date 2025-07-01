# EANGUS Leadership Directory Plugin

A custom WordPress plugin to **manage and display the EANGUS leadership directory** in a secure, professional, and easy-to-update way. Designed for non-technical administrators, this plugin keeps directory data cleanly separated from the site’s theme, ensuring long-term flexibility and stability.

---

## Features

- **Admin-Only Access**: Only logged-in users with admin permissions can add or modify leadership entries.
- **No Coding Required**: All data entry is handled via intuitive admin forms in the WordPress dashboard.
- **Custom Shortcodes**: Use `[state_directory]` or other shortcode tags to render specific sections anywhere on the front-end.
- **Tailwind-Styled Frontend**: Clean and responsive design using Tailwind CSS for modern, professional appearance.
- **Modular Design**: Built as a self-contained plugin, fully decoupled from any specific WordPress theme.
- **Expandable**: Sections include Executive Officers, Area Chairs, State Leadership, Past Presidents, and more.

---

## 🛠️ Technical Overview

This plugin uses:

- **PHP (WordPress API)**: To register shortcodes, forms, and admin pages.
- **Tailwind CSS**: Compiled into a custom `output.css` file for frontend styling.
- **Vanilla JavaScript**: Enables interactive toggle sections and optional modal functionality.
- **Shortcodes**:
  - `[state_directory]` – full directory view

### Directory Sections Included:
- Executive Officers
- Annual Conferences
- Past Presidents
- State Leadership by Area

Each section corresponds to a shortcode and is stored in its own custom table for easy backend data management.

---

## How It Works

### Admin Dashboard

1. After installing the plugin, admins will see a **“Leadership Directory”** tab in the WP admin menu.
2. Inside this tab are forms for each directory section.
3. Admins can add new entries via secure, sanitized input forms.
4. Data is inserted directly into custom database tables (`wp_sdp_*`).

### Front-End Display

1. Add any of the shortcodes to a page (e.g. `[state_directory]`).
2. The plugin will render a **Tailwind-styled**, fully responsive layout.
3. Filters by **Area** and **State** allow users to narrow down the directory.
4. Toggle buttons expand/collapse content as needed.

---

## Installation

1. Upload the plugin to your `/wp-content/plugins/` directory.
2. Activate it from the WordPress Plugin Dashboard.
3. Visit **Leadership Directory** in the admin menu to begin adding entries.
4. Add `[state_directory]` or other shortcode to any page or post to display.

---

## Maintenance & Updates

- To update information: Go to the admin panel → Leadership Directory → relevant section → fill out the form.
- To update styling: Modify the Tailwind source files and regenerate `output.css`.
- To add new fields or categories: Update the PHP form arrays and corresponding database schema.

---

##  Author

**Saleha Iftikhar**  
Intern | Enlisted Association of the National Guard of the United States (EANGUS)

---