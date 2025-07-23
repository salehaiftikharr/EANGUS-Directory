<?php

/**
 * Admin class for handling the backend UI of the EANGUS Leadership Directory Plugin
 */
class StateDirectoryAdmin {

    /**
     * Registers the admin menu item as a submenu under Settings.
     */
    public static function add_menu() {
        add_options_page(
            'Leadership Directory',           // Page title
            'Leadership Directory',           // Menu title
            'manage_options',                 // Capability
            'leadership-directory',           // Menu slug
            [__CLASS__, 'render_page']        // Callback function
        );
    }

    /**
     * Renders the main admin page with navigation tabs.
     */
    public static function render_page() {
        // Define tabs mapped to 'type' values
        $tabs = [
            'exec_officer'     => 'Executive Officers',
            'area_chair'       => 'Area Chairs',
            'state_council'    => 'State Leadership',
            'past_president'   => 'Past Presidents',
            'conference'       => 'Annual Conferences',
        ];

        $active_tab = $_GET['tab'] ?? 'exec_officer';

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">EANGUS Leadership Directory Settings</h1>';
        echo '<p>Manage your EANGUS Leadership Directory entries from this settings page.</p>';

        // Tab navigation
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $key => $label) {
            $class = ($active_tab === $key) ? 'nav-tab nav-tab-active' : 'nav-tab';
            echo "<a href='?page=leadership-directory&tab={$key}' class='{$class}'>{$label}</a>";
        }
        echo '</h2>';

        echo '<div class="sdp-card-container">';

        // Show the correct form with instructions
        switch ($active_tab) {
            case 'exec_officer':
                self::render_section_header(
                    'Manage Executive Officers',
                    'Add or update executive officer information. Duplicate positions will be updated automatically.'
                );
                self::render_form('exec_officer', [
                    'term_start', 'term_end', 'position', 'rank', 'first_name', 'last_name',
                    'email', 'phone_mobile', 'address', 'phone_office', 'phone_fax', 'phone_home'
                ]);
                break;

            case 'conference':
                self::render_section_header(
                    'Manage Annual Conferences',
                    'Add conference information including location and date ranges. Use format "Month DD-DD, YYYY" for date ranges.'
                );
                self::render_form('conference', [
                    'term_start', 'edition', 'location', 'date_range'
                ]);
                break;

            case 'past_president':
                self::render_section_header(
                    'Manage Past Presidents',
                    'Add past president information. Use the "Position" field for status (e.g., "Deceased", "Active").'
                );
                self::render_form('past_president', [
                    'term_start', 'term_end', 'rank', 'first_name', 'last_name',
                    'email', 'phone_home', 'phone_mobile', 'position'
                ]);
                break;

            case 'state_council':
                self::render_section_header(
                    'Manage State Leadership',
                    'Add state council members. Each state/position combination should be unique. Use 2-letter state codes (e.g., "CA", "TX").'
                );
                self::render_form('state_council', [
                    'state', 'area', 'position', 'rank', 'first_name', 'last_name',
                    'email', 'phone_mobile', 'term_start'
                ]);
                break;
                
            case 'area_chair':
                self::render_section_header(
                    'Manage Area Chairs',
                    'Add area chair information. Each area/position combination should be unique. Use area numbers (e.g., "1", "2", "3").'
                );
                self::render_form('area_chair', [
                     'area', 'position', 'rank', 'first_name', 'last_name',
                     'email', 'phone_mobile'
                 ]);
                break;
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * Renders a section header with title and instructions
     */
    private static function render_section_header($title, $instructions) {
        echo '<div style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #0073aa;">';
        echo '<h3 style="margin-top: 0;">' . esc_html($title) . '</h3>';
        echo '<p style="margin-bottom: 0; color: #666;">' . esc_html($instructions) . '</p>';
        echo '</div>';
    }

    /**
     * Renders the form for inserting entries into the unified eangus_directory table.
     *
     * @param string $type    The type of entry (e.g., 'exec_officer', 'conference')
     * @param array  $fields  The fields to render in the form
     */
    private static function render_form($type, $fields) {
        global $wpdb;
        $table = $wpdb->prefix . 'eangus_directory';
        $submit_key = 'sdp_submit_' . $type;

        // Handle submission
        if (isset($_POST[$submit_key])) {
            $data = ['type' => $type];

            foreach ($fields as $field) {
                if (!isset($_POST[$field])) {
                    $data[$field] = '';
                    continue;
                }

                $value = $_POST[$field];

                if (str_contains($field, 'email')) {
                    $data[$field] = sanitize_email($value);
                } elseif (in_array($field, ['term_start', 'term_end'])) {
                    $data[$field] = intval($value);
                } else {
                    $data[$field] = sanitize_text_field($value);
                }
            }

            // Define keys for uniqueness checks depending on section type
            $where = null;
            if ($type === 'exec_officer') {
                $where = [
                    'type'     => $type,
                    'position' => $data['position'] ?? '',
                ];
            } elseif ($type === 'state_council') {
                $where = [
                    'type'     => $type,
                    'state'    => $data['state'] ?? '',
                    'position' => $data['position'] ?? '',
                ];
            } elseif ($type === 'area_chair') {
                $where = [
                    'type'     => $type,
                    'area'     => $data['area'] ?? '',
                    'position' => $data['position'] ?? '',
                ];
            }

            // Try update if matching record exists
            $updated = false;
            if ($where) {
                $existing_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM $table WHERE " . implode(' AND ', array_map(fn($k) => "$k = %s", array_keys($where))),
                    ...array_values($where)
                ));

                if ($existing_id) {
                    $wpdb->update($table, $data, ['id' => $existing_id]);
                    $updated = true;
                }
            }

            // If not updated, insert new
            if (!$updated) {
                $wpdb->insert($table, $data);
            }

            // Show success or error message (without debug data)
            if ($wpdb->last_error) {
                echo '<div class="notice notice-error"><p><strong>Error:</strong> There was a problem saving the entry. Please check your data and try again.</p></div>';
            } else {
                $msg = $updated ? 'Entry updated successfully!' : 'Entry added successfully!';
                echo '<div class="notice notice-success is-dismissible"><p>' . $msg . '</p></div>';
            }
        }

        echo '<div class="directory-section">';
        echo '<form method="post"><table class="form-table">';
        echo '<input type="hidden" name="type" value="' . esc_attr($type) . '">';

        foreach ($fields as $field) {
            echo '<tr>';
            echo '<th><label for="' . esc_attr($field) . '">' . self::get_field_label($field) . '</label></th>';
            echo '<td>';

            if ($field === 'area' || $field === 'state') {
                echo '<input type="text" name="' . esc_attr($field) . '" class="regular-text" placeholder="' . self::get_field_placeholder($field) . '">';
            } elseif (str_contains($field, 'address') || str_contains($field, 'notes')) {
                echo '<textarea name="' . esc_attr($field) . '" class="large-text" placeholder="' . self::get_field_placeholder($field) . '"></textarea>';
            } else {
                echo '<input type="text" name="' . esc_attr($field) . '" class="regular-text" placeholder="' . self::get_field_placeholder($field) . '">';
            }

            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '<p><input type="submit" name="' . esc_attr($submit_key) . '" class="button button-primary" value="Add Entry"></p>';
        echo '</form>';
        echo '</div>';
    }

    /**
     * Get user-friendly field labels
     */
    private static function get_field_label($field) {
        $labels = [
            'term_start' => 'Term Start Year',
            'term_end' => 'Term End Year',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone_office' => 'Office Phone',
            'phone_mobile' => 'Mobile Phone',
            'phone_home' => 'Home Phone',
            'phone_fax' => 'Fax Number',
            'date_range' => 'Conference Dates',
        ];

        return $labels[$field] ?? ucwords(str_replace('_', ' ', $field));
    }

    /**
     * Get helpful placeholders for form fields
     */
    private static function get_field_placeholder($field) {
        $placeholders = [
            'state' => 'e.g., California, Texas, New York',
            'area' => 'e.g., I, II, III',
            'term_start' => 'e.g., 2024',
            'term_end' => 'e.g., 2026',
            'email' => 'user@example.com',
            'phone_office' => '(555) 123-4567',
            'phone_mobile' => '(555) 123-4567',
            'phone_home' => '(555) 123-4567',
            'phone_fax' => '(555) 123-4567',
            'date_range' => 'e.g., August 15-18, 2024',
            'position' => 'e.g., President, Vice President',
            'rank' => 'e.g., CMSgt, MSgt, SMSgt',
            'edition' => 'e.g., 53rd Annual Conference',
            'location' => 'e.g., Holiday Inn - Sioux Falls, SD',
            'address' => 'Full mailing address',
        ];

        return $placeholders[$field] ?? '';
    }
}