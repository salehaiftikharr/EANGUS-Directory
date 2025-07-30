<?php

class StateDirectoryRenderer {

     /**
     * Renders the "Annual Conferences" section of the directory.
     *
     * This method retrieves all annual conference entries from the database,
     * groups them by decade, and outputs them as a collapsible list using Tailwind CSS classes.
     * Each decade can be expanded to reveal individual conferences with year, edition, location, and dates.
     *
     * @return string The HTML content for the annual conferences section.
     */
    public static function render_annual_conferences() {
    global $wpdb;

    // Query unified table for all conference entries
    $table = $wpdb->prefix . 'eangus_directory';
    $results = $wpdb->get_results("
        SELECT term_start AS year, edition, location, date_range
        FROM {$table}
        WHERE type = 'conference'
        ORDER BY term_start DESC
    ");

    // Group entries by decade
    $grouped_by_decade = [];
    foreach ($results as $entry) {
        if (!is_numeric($entry->year)) continue;

        $decade_start = floor($entry->year / 10) * 10;
        $range_label = "{$decade_start}–" . ($decade_start + 9);
        $grouped_by_decade[$range_label][] = $entry;
    }

    // Sort decades by most recent first
    uksort($grouped_by_decade, function($a, $b) {
        $decade_a = (int)substr($a, 0, 4);
        $decade_b = (int)substr($b, 0, 4);
        
        return $decade_b - $decade_a; // DESC order (newest first)
    });

    ob_start();

    echo '<section class="sdp-section">';
    echo '<div class="sdp-main-inner">';

    foreach ($grouped_by_decade as $range => $entries) {
        $target_id = 'decade-' . sanitize_title($range);

        echo "<button class='sdp-toggle-btn' data-target-id='{$target_id}'>{$range}</button>";

        echo "<div id='{$target_id}' class='sdp-toggle-content hidden'>";
        echo "<div class='sdp-card-grid'>";

        foreach ($entries as $entry) {
            echo "<div class='sdp-card'>";
            echo "<p class='card-header'>" . esc_html($entry->year) . "</p>";

            if (!empty($entry->edition)) {
                echo "<p><strong>Edition:</strong> " . esc_html($entry->edition) . "</p>";
            }
            if (!empty($entry->location)) {
                echo "<p><strong>Location:</strong> " . esc_html($entry->location) . "</p>";
            }
            if (!empty($entry->date_range)) {
                echo "<p><strong>Dates:</strong> " . esc_html($entry->date_range) . "</p>";
            }

            echo "</div>";
        }

        echo "</div>"; // space-y-4
        echo "</div>"; // toggle content
    }

    echo '</div>'; // sdp-main-inner
    echo '</section>';

    return ob_get_clean();
    }


    /**
     * Renders the "Executive Officers" section of the directory.
     *
     * This method retrieves executive officer data from the database, organized by term year.
     * Officers are displayed in a collapsible section, showing their position, name, email, and phone if available.
     *
     * @return string The HTML content for the executive officers section.
     */
    public static function render_executive_officers() {
        global $wpdb;

        $table = $wpdb->prefix . 'eangus_directory';
        $results = $wpdb->get_results("
            SELECT *
            FROM {$table}
            WHERE type = 'exec_officer'
            ORDER BY term_start DESC, position ASC
        ");

        if (empty($results)) return '';

        $term_year = esc_html($results[0]->term_start);

        ob_start();

        echo '<section class="sdp-section">';
        echo '<div class="sdp-main-inner">';

        echo "<button class='sdp-toggle-btn' data-target-id='executive-officers'>Executive Officers</button>";

        // Add `hidden` to start collapsed
        echo "<div id='executive-officers' class='sdp-toggle-content hidden'>";
        echo "<h3 class='subsection-title'>Term Year: {$term_year}</h3>";
        
        // Add the card grid wrapper like State Leadership
        echo "<div class='sdp-card-grid'>";

        foreach ($results as $entry) {
            echo "<div class='sdp-card'>";

            // Position as bold header (like State Leadership)
            if (!empty($entry->position)) {
                echo "<p class='card-header'>" . esc_html($entry->position) . "</p>";
            }

            // Name without "Name:" label (like State Leadership)
            if (!empty($entry->rank) || !empty($entry->first_name) || !empty($entry->last_name)) {
                $full_name = trim("{$entry->rank} {$entry->first_name} {$entry->last_name}");
                echo "<p>" . esc_html($full_name) . "</p>";
            }

            // Email as clickable link
            if (!empty($entry->email)) {
                echo "<p><a href='mailto:" . esc_attr($entry->email) . "'>" . esc_html($entry->email) . "</a></p>";
            }

            // Phone without label
            if (!empty($entry->phone_mobile)) {
                echo "<p>" . esc_html($entry->phone_mobile) . "</p>";
            }

            echo "</div>";
        }

        echo "</div>"; // Close card grid
        echo "</div>"; // Toggle content
        echo "</div>"; // Container
        echo '</section>';

        return ob_get_clean();
    }


    /**
     * Renders the "Past Presidents" section of the directory.
     *
     * This method fetches all past presidents from the database, groups them by decade
     * based on the start year in their term (if available), and displays their info
     * in a collapsible interface grouped by decade.
     *
     * @return string The HTML content for the past presidents section.
     */
    public static function render_past_presidents() {
    global $wpdb;

    $table = $wpdb->prefix . 'eangus_directory';

    // Changed ORDER BY to DESC for newest first
    $results = $wpdb->get_results("
        SELECT *
        FROM {$table}
        WHERE type = 'past_president'
        ORDER BY term_start DESC
    ");

    $grouped_by_decade = [];

    foreach ($results as $entry) {
        if (!empty($entry->term_start) && is_numeric($entry->term_start)) {
            $decade_start = floor($entry->term_start / 10) * 10;
            $label = "{$decade_start} - " . ($decade_start + 9);
            $grouped_by_decade[$label][] = $entry;
        } else {
            $grouped_by_decade['Unknown'][] = $entry;
        }
    }

    // Sort decades by most recent first
    uksort($grouped_by_decade, function($a, $b) {
        if ($a === 'Unknown') return 1;
        if ($b === 'Unknown') return -1;
        
        $decade_a = (int)substr($a, 0, 4);
        $decade_b = (int)substr($b, 0, 4);
        
        return $decade_b - $decade_a; // DESC order (newest first)
    });

    ob_start();

    echo '<section class="sdp-section">';
    echo '<div class="sdp-main-inner">';
    echo "<button class='sdp-toggle-btn' data-target-id='past-presidents'>Past Presidents</button>";

    echo "<div id='past-presidents' class='sdp-toggle-content space-y-6 hidden'>";

    foreach ($grouped_by_decade as $decade => $entries) {
        $toggle_id = 'past-decade-' . sanitize_title($decade);

        echo "<button class='sdp-toggle-btn' data-target-id='{$toggle_id}'>{$decade}</button>";

        echo "<div id='{$toggle_id}' class='sdp-toggle-content hidden'>";
        echo "<div class='sdp-card-grid'>";

        foreach ($entries as $entry) {
            echo "<div class='sdp-card'>";

            if (!empty($entry->term_start)) {
                $term = $entry->term_start;
                if (!empty($entry->term_end)) {
                    $term .= " – {$entry->term_end}";
                }
                echo "<p><strong>Term:</strong> " . esc_html($term) . "</p>";
            }

            if (!empty($entry->rank) || !empty($entry->first_name) || !empty($entry->last_name)) {
                $name = trim("{$entry->rank} {$entry->first_name} {$entry->last_name}");
                echo "<p><strong>Name:</strong> " . esc_html($name) . "</p>";
            }

            // FIXED: Email as clickable link
            if (!empty($entry->email)) {
                echo "<p><strong>Email:</strong> <a href='mailto:" . esc_attr($entry->email) . "'>" . esc_html($entry->email) . "</a></p>";
            }

            if (!empty($entry->position)) {
                echo "<p><strong>Status:</strong> " . esc_html($entry->position) . "</p>";
            }

            echo "</div>";
        }

        echo "</div>";
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";
    echo "</section>";

    return ob_get_clean();
    }


   /**
     * Renders a card-style section from the unified eangus_directory table.
     *
     * This reusable method displays filtered directory entries as cards in a grid layout.
     * It supports dynamic field labels and dropdown-based subfilters.
     *
     * @param string $section_title   The title to display at the top of the section.
     * @param array  $fields          An associative array of field keys => display labels.
     * @param string $type_filter     The `type` field value in eangus_directory to filter by.
     * @param string|null $filter_field Optional. A field (e.g., 'area' or 'state') to use for dropdown filtering.
     *
     * @return string Rendered HTML content for the section.
     */
    private static function render_cards($section_title, $fields, $type_filter, $filter_field = null) {
        global $wpdb;

        $table = $wpdb->prefix . 'eangus_directory';

        // Fetch matching rows
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE type = %s",
            $type_filter
        ));

        ob_start();

        echo "<div class='sdp-section' data-section='{$section_title}'>";
        echo "<h2 class='sdp-collapsible'>{$section_title}</h2>";
        echo "<div class='sdp-card-grid'>";

        if (!empty($results)) {
            $unique_values = [];

            if ($filter_field) {
                $unique_values = array_unique(array_filter(array_map(function ($r) use ($filter_field) {
                    return $r->$filter_field ?? null;
                }, $results)));
                sort($unique_values);
            }

            if ($filter_field && !empty($unique_values)) {
                echo "<label style='margin-bottom: 10px;'>Filter by {$fields[$filter_field]}: ";
                echo "<select class='sdp-subfilter'>";
                echo "<option value='all'>All</option>";
                foreach ($unique_values as $value) {
                    echo "<option value='" . esc_attr($value) . "'>" . esc_html($value) . "</option>";
                }
                echo "</select></label>";
            }

            foreach ($results as $entry) {
                $filter_value = $filter_field && isset($entry->$filter_field) ? esc_attr($entry->$filter_field) : '';
                $cardContent = '';

                foreach ($fields as $key => $label) {
                    if (!empty($entry->$key)) {
                        // FIXED: Make email fields clickable links
                        if ($key === 'email') {
                            $cardContent .= "<p><strong>{$label}:</strong> <a href='mailto:" . esc_attr($entry->$key) . "'>" . esc_html($entry->$key) . "</a></p>";
                        } else {
                            $cardContent .= "<p><strong>{$label}:</strong> " . esc_html($entry->$key) . "</p>";
                        }
                    }
                }

                if ($cardContent) {
                    echo "<div class='sdp-card'" . ($filter_field ? " data-filter='{$filter_value}'" : "") . ">";
                    echo $cardContent;
                    echo "</div>";
                }
            }
        } else {
            echo "<p>No entries found.</p>";
        }

        echo "</div>"; // .sdp-card-grid
        echo "</div>"; // .sdp-section

        return ob_get_clean();
    }

    /**
     * Renders the "State Leadership" section grouped alphabetically by state name.
     *
     * This method pulls all states from the database, groups them by their first letter,
     * and displays their respective leadership in collapsible toggle sections.
     *
     * @return string The HTML output for the state leadership section.
     */
    public static function render_state_leadership_by_state() {
        global $wpdb;

        $table = $wpdb->prefix . 'eangus_directory';

        // Get distinct state names from unified table
        $states = $wpdb->get_col("
            SELECT DISTINCT state 
            FROM {$table} 
            WHERE type = 'state_council' AND state IS NOT NULL AND state != ''
            ORDER BY state ASC
        ");

        // Group states by first letter
        $grouped_states = [];
        foreach ($states as $state) {
            $letter = strtoupper(substr($state, 0, 1));
            $grouped_states[$letter][] = $state;
        }

        ob_start();

        echo '<section class="sdp-section">';
        echo '<div class="sdp-main-inner">';
        // echo '<h2 id="state-section" class="section-title">State Leadership</h2>';

        foreach ($grouped_states as $letter => $state_list) {
            echo '<h3 class="alpha-heading">' . esc_html($letter) . '</h3>';
            echo '<ul class="space-y-2 mb-6">';

            foreach ($state_list as $state_name) {
                $targetId = 'state-' . sanitize_title($state_name);

                echo '<li>';
                echo '<button class="sdp-toggle-btn" data-target-id="' . esc_attr($targetId) . '">';
                echo esc_html($state_name);
                echo '</button>';

                // Add hidden class here:
                echo '<div id="' . esc_attr($targetId) . '" class="sdp-toggle-content hidden">';

                // Fetch leadership entries for this state
                $leaders = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$table} WHERE type = 'state_council' AND state = %s ORDER BY position ASC",
                    $state_name
                ));

                if ($leaders) {
                    echo '<div class="sdp-card-grid mt-2">';
                    foreach ($leaders as $leader) {
                        echo '<div class="sdp-card">';

                        if (!empty($leader->position)) {
                            echo "<p class='card-header'>" . esc_html($leader->position) . "</p>";
                        }

                        $full_name = trim("{$leader->rank} {$leader->first_name} {$leader->last_name}");
                        echo "<p>" . esc_html($full_name) . "</p>";

                        // ALREADY CORRECT: Email as clickable link
                        if (!empty($leader->email)) {
                            echo '<p><a href="mailto:' . esc_attr($leader->email) . '">' . esc_html($leader->email) . '</a></p>';
                        }

                        if (!empty($leader->phone_mobile)) {
                            echo "<p>" . esc_html($leader->phone_mobile) . "</p>";
                        }

                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p class="text-gray-500">No leadership entries found.</p>';
                }

                echo '</div>'; // toggle-content
                echo '</li>';
            }

            echo '</ul>';
        }

        echo '</div>';
        echo '</section>';

        return ob_get_clean();
    }


    /**
     * Renders the "State Leadership by Area" section.
     *
     * This method displays all areas (from the Area Chairs table) and lists the executive council members
     * and state leadership within each area. Each area is collapsible and contains the relevant states and members.
     *
     * @return string The HTML output for the state leadership grouped by area.
     */
    public static function render_area_state_leadership() {
        global $wpdb;
        $table = $wpdb->prefix . 'eangus_directory';

        // Fetch distinct areas from area chairs or directors
        $areas = $wpdb->get_col("
            SELECT DISTINCT area
            FROM {$table}
            WHERE type IN ('area_chair', 'area_director')
            ORDER BY area ASC
        ");

        ob_start();

        echo '<section class="sdp-section">';
        echo '<div class="sdp-main-inner">';
       // echo '<h2 id="area-section" class="section-title">State Leadership by Area</h2>';

        foreach ($areas as $area) {
            $area_label = 'Area ' . strtoupper($area);
            $toggle_id = 'area-' . sanitize_title($area);

            echo '<div class="mb-6">';
            echo '<button class="sdp-toggle-btn" data-target-id="' . esc_attr($toggle_id) . '">';
            echo esc_html($area_label) . '</button>';

            // Add "hidden" class here
            echo '<div id="' . esc_attr($toggle_id) . '" class="sdp-toggle-content hidden">';
            echo '<div class="space-y-4">';

            // Area-level leadership cards
            $exec_members = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table} WHERE area = %s AND type IN ('area_chair', 'area_director') ORDER BY position ASC",
                $area
            ));

            if ($exec_members) {
                echo '<div class="sdp-card-grid">';
                foreach ($exec_members as $member) {
                    echo '<div class="sdp-card">';
                    echo '<p class="card-header">' . esc_html($member->position) . '</p>';
                    echo '<p>' . esc_html(trim("{$member->rank} {$member->first_name} {$member->last_name}")) . '</p>';

                    // FIXED: Email as clickable link
                    if (!empty($member->email)) {
                        echo '<p><a href="mailto:' . esc_attr($member->email) . '">' . esc_html($member->email) . '</a></p>';
                    }

                    if (!empty($member->phone_mobile)) {
                        echo '<p>' . esc_html($member->phone_mobile) . '</p>';
                    }

                    echo '</div>';
                }
                echo '</div>'; // .sdp-card-grid
            }

            // List of states in the area
            $states = $wpdb->get_col($wpdb->prepare(
                "SELECT DISTINCT state FROM {$table} WHERE area = %s AND type = 'state_council' ORDER BY state ASC",
                $area
            ));

            if (!empty($states)) {
                echo '<div class="mt-4">';
                echo '<h4 class="subsection-title">States in ' . esc_html($area_label) . ':</h4>';
                echo '<div class="flex-wrap gap-2">';
                foreach ($states as $state_name) {
                    echo '<span class="pill">' . esc_html($state_name) . '</span>';
                }
                echo '</div>';
                echo '</div>';
            }

            echo '</div>'; // .space-y-4
            echo '</div>'; // .sdp-toggle-content
            echo '</div>'; // .mb-6
        }

        echo '</div>';
        echo '</section>';

        return ob_get_clean();
    }
}