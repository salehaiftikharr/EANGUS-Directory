<?php

/**
 * Admin class for handling the backend UI of the EANGUS Leadership Directory Plugin
 */
class StateDirectoryAdmin {

    /**
     * Registers the admin menu item in the WordPress dashboard.
     */
    public static function add_menu() {
        add_menu_page(
            'Leadership Directory',              // Page title
            'Leadership Directory',              // Menu title
            'manage_options',                    // Capability
            'leadership-directory',              // Menu slug
            [__CLASS__, 'render_page'],          // Callback function
            'dashicons-id',                      // Icon
            6                                    // Position
        );
    }

    /**
     * Renders the main admin page with navigation tabs.
     */
    public static function render_page() {
        // Define tabs and their labels
        $tabs = [
            'executive_officers' => 'Executive Officers',
            'annual_conferences' => 'Annual Conferences',
            'past_presidents'    => 'Past Presidents',
            'states'             => 'States',
            'state_leadership'   => 'State Leadership',
        ];

        // Determine the currently active tab
        $active_tab = $_GET['tab'] ?? 'executive_officers';

        echo '<div class="wrap">';
        echo '<h1 class="sdp-title">EANGUS Leadership Directory</h1>';

        // Render tab navigation
        echo '<h2 class="nav-tab-wrapper">';

        foreach ($tabs as $key => $label) {
            $class = ($active_tab === $key) ? 'nav-tab nav-tab-active' : 'nav-tab';
            echo "<a href='?page=leadership-directory&tab=$key' class='$class'>$label</a>";
        }

        echo '</h2>';

        echo '<div class="sdp-card-container">';

        // Load the corresponding form based on active tab
        switch ($active_tab) {
            case 'executive_officers':
                self::render_form('sdp_executive_officers', [
                    'term_year', 'position', 'rank', 'first_name', 'last_name',
                    'email', 'mobile_phone', 'address', 'office_phone', 'fax', 'home_phone'
                ]);
                break;

            case 'annual_conferences':
                self::render_form('sdp_annual_conferences', [
                    'year', 'edition', 'location', 'dates'
                ]);
                break;

            case 'past_presidents':
                self::render_form('sdp_past_presidents', [
                    'term', 'rank', 'first_name', 'last_name', 'email', 'home_phone', 'mobile_phone', 'status'
                ]);
                break;

            case 'states':
                self::render_form('sdp_states', ['state_name', 'area_id']);
                break;

            case 'state_leadership':
                self::render_form('sdp_state_leadership', [
                    'state_id', 'position', 'rank', 'first_name', 'last_name', 'email', 'phone', 'term_year'
                ]);
                break;
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * Renders the form for inserting entries into the given table.
     *
     * @param string $table_slug The suffix of the table name
     * @param array $fields      The fields to display in the form
     */
    private static function render_form($table_slug, $fields) {
        global $wpdb;
        $table = $wpdb->prefix . $table_slug;
        $submit_key = 'sdp_submit_' . $table_slug;

        // Handle form submission
        if (isset($_POST[$submit_key])) {
            $data = [];

            // Sanitize and collect field values
            foreach ($fields as $field) {
                $data[$field] = isset($_POST[$field]) ? sanitize_text_field($_POST[$field]) : '';
            }

            // Insert into the database
            $wpdb->insert($table, $data);
            echo '<div class="updated notice is-dismissible"><p>Data added successfully to ' . esc_html($table_slug) . '.</p></div>';
        }

        // Begin form
        echo '<div class="directory-section">';
        echo '<form method="post"><table class="form-table">';

        // Render input fields
        foreach ($fields as $field) {
            echo '<tr>';
            echo '<th><label for="' . esc_attr($field) . '">' . ucwords(str_replace('_', ' ', $field)) . '</label></th>';
            echo '<td>';

            // Special handling for dropdown/select fields
            if ($field === 'area_id' && $table_slug === 'sdp_states') {
                echo '<input type="text" name="area_id" class="regular-text">';
            } 
            elseif ($field === 'state_id' && $table_slug === 'sdp_state_leadership') {
                // Load states for dropdown
                $states = $wpdb->get_results("SELECT id, state_name FROM {$wpdb->prefix}sdp_states");
                echo '<select name="state_id">';
                
                foreach ($states as $state) {
                    echo '<option value="' . esc_attr($state->id) . '">' . esc_html($state->state_name) . '</option>';
                }
                echo '</select>';
            } 
            elseif (str_contains($field, 'address') || str_contains($field, 'notes')) {
                echo '<textarea name="' . esc_attr($field) . '" class="large-text"></textarea>';
            } 
            else {
                echo '<input type="text" name="' . esc_attr($field) . '" class="regular-text">';
            }

            echo '</td>';
            echo '</tr>';
        }

        // Submit button
        echo '</table>';
        echo '<p><input type="submit" name="' . esc_attr($submit_key) . '" class="button button-primary" value="Add Entry"></p>';
        echo '</form>';
        echo '</div>';
    }
}