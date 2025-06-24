<?php

class StateDirectoryAdmin {
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

    public static function render_page() {
        $tabs = [
            'executive_officers' => 'Executive Officers',
            'annual_conferences' => 'Annual Conferences',
            'area_chairs' => 'Area Chairs',
            'executive_council' => 'Executive Council',
            'committees' => 'Committees',
            'auxiliary_exec_board' => 'Auxiliary Executive Board',
            'past_presidents' => 'Past Presidents',
        ];

        $active_tab = $_GET['tab'] ?? 'executive_officers';

        echo '<div class="wrap">';
        echo '<h1 class="sdp-title">EANGUS Leadership Directory</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $key => $label) {
            $class = ($active_tab === $key) ? 'nav-tab nav-tab-active' : 'nav-tab';
            echo "<a href='?page=leadership-directory&tab=$key' class='$class'>$label</a>";
        }
        echo '</h2>';

        echo '<div class="sdp-card-container">';

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
            case 'area_chairs':
                self::render_form('sdp_area_chairs', [
                    'position', 'rank', 'first_name', 'last_name', 'email'
                ]);
                break;
            case 'executive_council':
                self::render_form('sdp_executive_council', [
                    'term', 'position', 'rank', 'first_name', 'last_name', 'email'
                ]);
                break;
            case 'committees':
                self::render_form('sdp_committees', [
                    'position', 'rank', 'first_name', 'last_name', 'email'
                ]);
                break;
            case 'auxiliary_exec_board':
                self::render_form('sdp_auxiliary_executive_board', [
                    'title', 'year', 'name', 'phone', 'email'
                ]);
                break;
            case 'past_presidents':
                self::render_form('sdp_past_presidents', [
                    'term', 'rank', 'first_name', 'last_name', 'email', 'home_phone', 'mobile_phone', 'status'
                ]);
                break;
        }

        echo '</div>';
        echo '</div>';
    }

    private static function render_form($table_slug, $fields) {
        global $wpdb;
        $table = $wpdb->prefix . $table_slug;

        $submit_key = 'sdp_submit_' . $table_slug;
        if (isset($_POST[$submit_key])) {
            $data = [];
            foreach ($fields as $field) {
                $data[$field] = isset($_POST[$field]) ? sanitize_text_field($_POST[$field]) : '';
            }
            $wpdb->insert($table, $data);
            echo '<div class="updated notice is-dismissible"><p>Data added successfully to ' . esc_html($table_slug) . '.</p></div>';
        }

        echo '<div class="directory-section">';
        echo '<form method="post"><table class="form-table">';
        foreach ($fields as $field) {
            echo '<tr>';
            echo '<th><label for="' . esc_attr($field) . '">' . ucwords(str_replace('_', ' ', $field)) . '</label></th>';
            echo '<td>';
            if (str_contains($field, 'address') || str_contains($field, 'notes')) {
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
