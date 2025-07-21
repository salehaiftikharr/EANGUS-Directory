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
            'Leadership Directory',
            'Leadership Directory',
            'manage_options',
            'leadership-directory',
            [__CLASS__, 'render_page'],
            'dashicons-id',
            6
        );
    }

    /**
     * Renders the main admin page with navigation tabs.
     */
    public static function render_page() {
        // Define tabs mapped to 'type' values
        $tabs = [
            'exec_officer'     => 'Executive Officers',
            'conference'       => 'Annual Conferences',
            'past_president'   => 'Past Presidents',
            'state_council'    => 'State Leadership',
            'area_chair'       => 'Area Chairs',
        ];

        $active_tab = $_GET['tab'] ?? 'exec_officer';

        echo '<div class="wrap">';
        echo '<h1 class="sdp-title">EANGUS Leadership Directory</h1>';

        // Tab navigation
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $key => $label) {
            $class = ($active_tab === $key) ? 'nav-tab nav-tab-active' : 'nav-tab';
            echo "<a href='?page=leadership-directory&tab={$key}' class='{$class}'>{$label}</a>";
        }
        echo '</h2>';

        echo '<div class="sdp-card-container">';

        // Show the correct form
        switch ($active_tab) {
            case 'exec_officer':
                self::render_form('exec_officer', [
                    'term_start', 'term_end', 'position', 'rank', 'first_name', 'last_name',
                    'email', 'phone_mobile', 'address', 'phone_office', 'phone_fax', 'phone_home'
                ]);
                break;

            case 'conference':
                self::render_form('conference', [
                    'term_start', 'edition', 'location', 'date_range'
                ]);
                break;

            case 'past_president':
                self::render_form('past_president', [
                    'term_start', 'term_end', 'rank', 'first_name', 'last_name',
                    'email', 'phone_home', 'phone_mobile', 'position' // position = status
                ]);
                break;

            case 'state_council':
                self::render_form('state_council', [
                    'state', 'area', 'position', 'rank', 'first_name', 'last_name',
                    'email', 'phone_mobile', 'term_start'
                ]);
                break;
            case 'area_chair':
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

                // Field-type-specific sanitization
                if (str_contains($field, 'email')) {
                    $data[$field] = sanitize_email($value);
                } elseif (in_array($field, ['term_start', 'term_end'])) {
                    $data[$field] = intval($value);
                } else {
                    $data[$field] = sanitize_text_field($value);
                }
            }

            $wpdb->insert($table, $data);

            if ($wpdb->last_error) {
                echo '<div class="notice notice-error"><p><strong>DB Error:</strong> ' . esc_html($wpdb->last_error) . '</p></div>';
                echo '<pre>' . print_r($data, true) . '</pre>';
            } else {
                echo '<div class="notice notice-success is-dismissible"><p>Entry added successfully.</p></div>';
                echo '<pre>' . print_r($data, true) . '</pre>';
            }
        }

        echo '<div class="directory-section">';
        echo '<form method="post"><table class="form-table">';
        echo '<input type="hidden" name="type" value="' . esc_attr($type) . '">';

        foreach ($fields as $field) {
            echo '<tr>';
            echo '<th><label for="' . esc_attr($field) . '">' . ucwords(str_replace('_', ' ', $field)) . '</label></th>';
            echo '<td>';

            if ($field === 'area' || $field === 'state') {
                echo '<input type="text" name="' . esc_attr($field) . '" class="regular-text">';
            } elseif (str_contains($field, 'address') || str_contains($field, 'notes')) {
                echo '<textarea name="' . esc_attr($field) . '" class="large-text"></textarea>';
            } else {
                echo '<input type="text" name="' . esc_attr($field) . '" class="regular-text">';
            }

            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '<p><input type="submit" name="' . esc_attr($submit_key) . '" class="button button-primary" value="Add Entry"></p>';
        echo '</form>';
        echo '</div>';
    }
}
