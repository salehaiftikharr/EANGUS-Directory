<?php

class StateDirectoryShortcode {

    public static function render_all() {
        global $wpdb;
        $states = self::get_unique_column_values('sdp_state_directory', 'state');
        $areas = self::get_unique_column_values('sdp_state_directory', 'area');

        ob_start(); ?>

        <div class="sdp-hero">
            <h1>State Association Directory</h1>
            <div class="sdp-filter-bar">
                <label for="area-select">Area</label>
                <select id="area-select">
                    <option value="all">All</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?php echo esc_attr($area); ?>"><?php echo esc_html($area); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="state-select">State</label>
                <select id="state-select">
                    <option value="all">All</option>
                    <?php foreach ($states as $state): ?>
                        <option value="<?php echo esc_attr($state); ?>"><?php echo esc_html($state); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div id="state-card-grid" class="sdp-card-grid">
            <?php echo self::render_state_cards(); ?>
        </div>

        <div id="sdp-modal" class="sdp-modal">
            <div class="sdp-modal-content">
                <span class="sdp-close">&times;</span>
                <div id="sdp-modal-body"></div>
            </div>
        </div>

        <?php
        echo self::render_annual_conferences();
        echo self::render_executive_officers();
        echo self::render_area_chairs();
        echo self::render_executive_council();
        echo self::render_committees();
        echo self::render_auxiliary_exec_board();
        echo self::render_past_presidents();
        return ob_get_clean();
    }

    public static function render_state_cards() {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sdp_state_directory");

        ob_start();
        foreach ($results as $entry) {
            echo "<div class='sdp-card' data-state='" . esc_attr($entry->state) . "' data-area='" . esc_attr($entry->area) . "'>";
            echo "<p><strong>State:</strong> " . esc_html($entry->state) . "</p>";
            echo "<p><strong>Area:</strong> " . esc_html($entry->area) . "</p>";
            echo "<p><strong>President:</strong> " . esc_html($entry->president_name) . "</p>";
            echo "<p><strong>Email:</strong> " . esc_html($entry->email) . "</p>";
            echo "<p><strong>Phone:</strong> " . esc_html($entry->phone) . "</p>";
            echo "<p><strong>Address:</strong> " . esc_html($entry->address) . "</p>";
            echo "</div>";
        }
        return ob_get_clean();
    }

    private static function get_unique_column_values($table, $column) {
        global $wpdb;
        $full_table = $wpdb->prefix . $table;
        $results = $wpdb->get_col("SELECT DISTINCT $column FROM $full_table WHERE $column IS NOT NULL AND $column != '' ORDER BY $column ASC");
        return $results;
    }

    public static function render_annual_conferences() {
        return self::render_cards('sdp_annual_conferences', 'Annual Conferences', [
            'year' => 'Year',
            'edition' => 'Edition',
            'location' => 'Location',
            'dates' => 'Dates'
        ], 'year');
    }

    public static function render_executive_officers() {
        return self::render_cards('sdp_executive_officers', 'Executive Officers', [
            'term_year' => 'Term Year',
            'position' => 'Position',
            'rank' => 'Rank',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'mobile_phone' => 'Mobile Phone'
        ], 'term_year');
    }

    public static function render_area_chairs() {
        return self::render_cards('sdp_area_chairs', 'Area Chairs', [
            'position' => 'Position',
            'rank' => 'Rank',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email'
        ], 'position');
    }

    public static function render_executive_council() {
        return self::render_cards('sdp_executive_council', 'Executive Council', [
            'term' => 'Term',
            'position' => 'Position',
            'rank' => 'Rank',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email'
        ], 'position');
    }

    public static function render_committees() {
        return self::render_cards('sdp_committees', 'Committees', [
            'position' => 'Position',
            'rank' => 'Rank',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email'
        ], 'position');
    }

    public static function render_auxiliary_exec_board() {
        return self::render_cards('sdp_auxiliary_executive_board', 'Auxiliary Executive Board', [
            'title' => 'Title',
            'year' => 'Year',
            'name' => 'Name',
            'phone' => 'Phone',
            'email' => 'Email'
        ], 'year');
    }

    public static function render_past_presidents() {
        return self::render_cards('sdp_past_presidents', 'Past Presidents', [
            'term' => 'Term',
            'rank' => 'Rank',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'status' => 'Status'
        ], 'term');
    }

    private static function render_cards($table_name, $section_title, $fields, $filter_field = null) {
        global $wpdb;
        $full_table = $wpdb->prefix . $table_name;
        $results = $wpdb->get_results("SELECT * FROM $full_table");

        ob_start();
        echo "<div class='sdp-section' data-section='{$section_title}'>";
        echo "<h2 class='sdp-collapsible'>{$section_title}</h2>";

        if (!empty($results)) {
            $unique_values = [];
            if ($filter_field) {
                $unique_values = array_unique(array_filter(array_map(function ($r) use ($filter_field) {
                    return $r->$filter_field ?? null;
                }, $results)));
                sort($unique_values);
            }

            if ($filter_field && !empty($unique_values)) {
                echo "<label>Filter by {$fields[$filter_field]}: ";
                echo "<select class='sdp-subfilter'>";
                echo "<option value='all'>All</option>";
                foreach ($unique_values as $value) {
                    echo "<option value='" . esc_attr($value) . "'>" . esc_html($value) . "</option>";
                }
                echo "</select></label>";
            }

            echo "<div class='sdp-card-grid'>";
            foreach ($results as $entry) {
                $filter_value = $filter_field && isset($entry->$filter_field) ? esc_attr($entry->$filter_field) : '';
                $cardContent = '';
                foreach ($fields as $key => $label) {
                    if (!empty($entry->$key)) {
                        $cardContent .= "<p><strong>{$label}:</strong> " . esc_html($entry->$key) . "</p>";
                    }
                }
                if ($cardContent) {
                    echo "<div class='sdp-card'" . ($filter_field ? " data-filter='{$filter_value}'" : "") . ">";
                    echo $cardContent;
                    echo "</div>";
                }
            }
            echo "</div>";
        } else {
            echo "<p>No entries found.</p>";
        }

        echo "</div>";
        return ob_get_clean();
    }
}
