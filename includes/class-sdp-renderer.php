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

    // Fetch all conference entries ordered by year (ascending)
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sdp_annual_conferences ORDER BY year ASC");

    // Group entries by decade
    $grouped_by_decade = [];
    foreach ($results as $entry) {
        if (!is_numeric($entry->year)) continue; // Skip entries with non-numeric years

        $decade_start = floor($entry->year / 10) * 10;
        $range_label = "{$decade_start}–" . ($decade_start + 9); // e.g., "1990–1999"
        $grouped_by_decade[$range_label][] = $entry;
    }

    ob_start();

    // Section container
    echo '<section class="sdp-section py-12 bg-white px-4 sm:px-6 lg:px-8">';
    echo '<div class="max-w-6xl mx-auto">';
    echo '<h2 id="conference-section" class="text-3xl font-bold text-gray-900 mb-8">Annual Conferences</h2>';

    // Loop through each decade group and render its entries
    foreach ($grouped_by_decade as $range => $entries) {
        $target_id = 'decade-' . sanitize_title($range); // Unique ID for toggling

        // Toggle button for the decade
        echo "<button class='sdp-toggle-btn w-full text-left text-xl font-semibold text-blue-700 mb-2 hover:underline' data-target-id='{$target_id}'>{$range}</button>";

        // Toggle content wrapper
        echo "<div id='{$target_id}' class='sdp-toggle-content hidden'>";
        echo "<div class='flex flex-col space-y-4 mt-4 mb-8'>";

        // Loop through each conference in this decade
        foreach ($entries as $entry) {
            echo "<div class='bg-gray-50 border border-gray-200 rounded-xl p-6 shadow-sm'>";
            echo "<p class='text-sm text-gray-700 font-bold'>" . esc_html($entry->year) . "</p>";

            // Optional fields: edition, location, and dates
            if (!empty($entry->edition)) {
                echo "<p class='text-sm text-gray-700'><strong>Edition:</strong> " . esc_html($entry->edition) . "</p>";
            }
            if (!empty($entry->location)) {
                echo "<p class='text-sm text-gray-700'><strong>Location:</strong> " . esc_html($entry->location) . "</p>";
            }
            if (!empty($entry->dates)) {
                echo "<p class='text-sm text-gray-700'><strong>Dates:</strong> " . esc_html($entry->dates) . "</p>";
            }

            echo "</div>"; // Close individual entry card
        }

        echo "</div>"; // Close content list
        echo "</div>"; // Close toggle content
    }

        echo '</div>'; // Close container
        echo '</section>'; // Close section

    return ob_get_clean(); // Return buffered output
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

    // Fetch all executive officers, ordered by most recent term year and then by position
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sdp_executive_officers ORDER BY term_year DESC, position ASC");

    // Return empty string if no results are found
    if (empty($results)) return '';

    // Use the first result's term year to display at the top
    $term_year = esc_html($results[0]->term_year);

    ob_start();

    // Begin section container
    echo '<section class="sdp-section py-12 bg-white px-4 sm:px-6 lg:px-8">';
    echo '<div class="max-w-6xl mx-auto">';

    // Toggle button for executive officers section
    echo "<button class='sdp-toggle-btn w-full text-left text-2xl font-bold text-blue-800 mb-4' data-target-id='executive-officers'>Executive Officers</button>";

    // Collapsible content wrapper
    echo "<div id='executive-officers' class='sdp-toggle-content hidden'>";
    echo "<h3 class='text-xl font-bold text-blue-700 mb-6'>Term Year: {$term_year}</h3>";

    // Loop through each officer entry
    foreach ($results as $entry) {
        echo "<div class='bg-gray-50 border border-gray-200 rounded-xl p-5 shadow-sm mb-4 space-y-2'>";

        // Display position
        if (!empty($entry->position)) {
            echo "<p class='text-sm text-gray-800 font-bold'>" . esc_html($entry->position) . "</p>";
        }

        // Display full name with rank
        if (!empty($entry->rank) || !empty($entry->first_name) || !empty($entry->last_name)) {
            $full_name = trim("{$entry->rank} {$entry->first_name} {$entry->last_name}");
            echo "<p class='text-sm text-gray-700'>Name: " . esc_html($full_name) . "</p>";
        }

        // Display email with mailto link
        if (!empty($entry->email)) {
            echo "<p class='text-sm text-gray-700'>Email: <a href='mailto:" . esc_attr($entry->email) . "' class='text-blue-500 hover:underline'>" . esc_html($entry->email) . "</a></p>";
        }

        // Display mobile phone number
        if (!empty($entry->mobile_phone)) {
            echo "<p class='text-sm text-gray-700'>Mobile Phone: " . esc_html($entry->mobile_phone) . "</p>";
        }

        echo "</div>"; // End officer card
    }

    echo "</div>"; // End toggle content
    echo "</div>"; // End container
    echo "</section>"; // End section

    return ob_get_clean(); // Return buffered HTML
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

    // Retrieve all past presidents from the database
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sdp_past_presidents");

    // Group entries by decade based on their term year
    $grouped_by_decade = [];

    foreach ($results as $entry) {
        // Try to extract the 4-digit year from the beginning of the 'term' string
        if (preg_match('/^(\d{4})/', $entry->term, $matches)) {
            $year = (int)$matches[1];
            $decade_start = floor($year / 10) * 10;
            $label = "{$decade_start} - " . ($decade_start + 9);
            $grouped_by_decade[$label][] = $entry;
        } else {
            // Group entries with missing or malformed year under "Unknown"
            $grouped_by_decade['Unknown'][] = $entry;
        }
    }

    ob_start();

    // Start section wrapper
    echo '<section class="sdp-section py-12 bg-white px-4 sm:px-6 lg:px-8">';
    echo '<div class="max-w-6xl mx-auto">';

    // Main toggle for the Past Presidents section
    echo "<button class='sdp-toggle-btn w-full text-left text-2xl font-bold text-blue-800 mb-4' data-target-id='past-presidents'>Past Presidents</button>";

    // Past presidents container (collapsible)
    echo "<div id='past-presidents' class='sdp-toggle-content hidden space-y-6'>";

    // Loop through each decade group
    foreach ($grouped_by_decade as $decade => $entries) {
        $toggle_id = 'past-decade-' . sanitize_title($decade);

        // Toggle button for each decade
        echo "<button class='sdp-toggle-btn w-full text-left text-xl font-semibold text-gray-800 border border-gray-300 bg-gray-100 px-4 py-2 rounded-md' data-target-id='{$toggle_id}'>{$decade}</button>";

        // Collapsible container for entries in that decade
        echo "<div id='{$toggle_id}' class='sdp-toggle-content hidden'>";
        echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-6 mt-4'>";

        // Loop through individual past president entries
        foreach ($entries as $entry) {
            echo "<div class='bg-gray-50 border border-gray-200 rounded-xl p-6 shadow-sm'>";

            // Term
            if (!empty($entry->term)) {
                echo "<p class='text-sm text-gray-700'><strong>Term:</strong> " . esc_html($entry->term) . "</p>";
            }

            // Name with optional rank
            if (!empty($entry->rank) || !empty($entry->first_name) || !empty($entry->last_name)) {
                $name = trim("{$entry->rank} {$entry->first_name} {$entry->last_name}");
                echo "<p class='text-sm text-gray-700'><strong>Name:</strong> " . esc_html($name) . "</p>";
            }

            // Email (mailto link)
            if (!empty($entry->email)) {
                echo "<p class='text-sm text-gray-700'><strong>Email:</strong> <a href='mailto:" . esc_attr($entry->email) . "' class='text-blue-500 hover:underline'>" . esc_html($entry->email) . "</a></p>";
            }

            // Status
            if (!empty($entry->status)) {
                echo "<p class='text-sm text-gray-700'><strong>Status:</strong> " . esc_html($entry->status) . "</p>";
            }

            echo "</div>"; // End entry card
        }

        echo "</div>"; // End grid
        echo "</div>"; // End toggle content for decade
    }

    echo "</div>"; // End past presidents content
    echo "</div>"; // End container
    echo "</section>"; // End section

    return ob_get_clean(); // Return the buffered HTML
    }

    /**
     * Renders a generic card-style section from a given database table.
     *
     * This reusable method displays entries as cards in a grid layout, with optional filtering.
     * It supports dynamic field labels and can be used for any section with a consistent structure.
     *
     * @param string $table_name     The name of the table (without prefix) to query from.
     * @param string $section_title  The title of the section to display.
     * @param array  $fields         An associative array of field keys => display labels.
     * @param string|null $filter_field Optional. The field to filter cards by (dropdown-based).
     *
     * @return string The HTML output for the rendered card section.
     */
    private static function render_cards($table_name, $section_title, $fields, $filter_field = null) {
    global $wpdb;

    $full_table = $wpdb->prefix . $table_name;

    // Fetch all rows from the specified table
    $results = $wpdb->get_results("SELECT * FROM $full_table");

    ob_start();

    // Section wrapper with title
    echo "<div class='sdp-section' data-section='{$section_title}'>";
    echo "<h2 class='sdp-collapsible'>{$section_title}</h2>";
    echo "<div class='sdp-card-grid'>"; // Grid container

    if (!empty($results)) {
        $unique_values = [];

        // If a filter is provided, extract all unique values for that field
        if ($filter_field) {
            $unique_values = array_unique(array_filter(array_map(function ($r) use ($filter_field) {
                return $r->$filter_field ?? null;
            }, $results)));
            sort($unique_values);
        }

        // Render filter dropdown if applicable
        if ($filter_field && !empty($unique_values)) {
            echo "<label style='margin-bottom: 10px;'>Filter by {$fields[$filter_field]}: ";
            echo "<select class='sdp-subfilter'>";
            echo "<option value='all'>All</option>";
            foreach ($unique_values as $value) {
                echo "<option value='" . esc_attr($value) . "'>" . esc_html($value) . "</option>";
            }
            echo "</select></label>";
        }

        // Render individual cards
        foreach ($results as $entry) {
            $filter_value = $filter_field && isset($entry->$filter_field) ? esc_attr($entry->$filter_field) : '';
            $cardContent = '';

            // Build content for each field specified in $fields
            foreach ($fields as $key => $label) {
                if (!empty($entry->$key)) {
                    $cardContent .= "<p><strong>{$label}:</strong> " . esc_html($entry->$key) . "</p>";
                }
            }

            // Output the card if it has content
            if ($cardContent) {
                echo "<div class='sdp-card'" . ($filter_field ? " data-filter='{$filter_value}'" : "") . ">";
                echo $cardContent;
                echo "</div>";
            }
        }
    } else {
        echo "<p>No entries found.</p>";
    }

    echo "</div>"; // End .sdp-card-grid
    echo "</div>"; // End .sdp-section

    return ob_get_clean(); // Return buffered HTML
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

    // Fetch all states ordered alphabetically
    $states = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sdp_states ORDER BY state_name ASC");

    // Group states by the first letter of their name
    $grouped_states = [];
    foreach ($states as $state) {
        $letter = strtoupper(substr($state->state_name, 0, 1));
        if (!isset($grouped_states[$letter])) {
            $grouped_states[$letter] = [];
        }
        $grouped_states[$letter][] = $state;
    }

    ob_start();

    // Begin section layout
    echo '<div class="py-12 bg-white px-4 sm:px-6 lg:px-8">';
    echo '<div class="max-w-6xl mx-auto">';
    echo '<h2 id="state-section" class="text-3xl font-bold text-gray-900 mb-8">State Leadership</h2>';

    // Render each group by first letter
    foreach ($grouped_states as $letter => $states) {
        echo '<h3 class="text-2xl font-bold text-gray-800 mt-10 mb-4 border-b pb-2">' . esc_html($letter) . '</h3>';
        echo '<ul class="space-y-2 mb-8">';

        // Loop through each state under this letter
        foreach ($states as $state) {
            $targetId = 'state-' . sanitize_title($state->state_name);

            // Toggleable state name
            echo '<li>';
            echo '<button class="sdp-toggle-btn text-left text-blue-700 font-semibold hover:underline" data-target-id="' . esc_attr($targetId) . '">';
            echo esc_html($state->state_name);
            echo '</button>';

            // Hidden content that will expand on toggle
            echo '<div id="' . esc_attr($targetId) . '" class="sdp-toggle-content hidden mt-2 ml-4 text-sm text-gray-700">';

            // Fetch leaders for the current state
            $leaders = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}sdp_state_leadership WHERE state_id = %d",
                $state->id
            ));

            if ($leaders) {
                echo '<div class="flex flex-col space-y-6 mt-4">';

                // Render each leader
                foreach ($leaders as $leader) {
                    echo '<div class="bg-gray-50 border border-gray-200 rounded-xl p-6 shadow-sm">';
                    echo "<p class='text-sm text-gray-700 font-semibold'>" . esc_html($leader->position) . "</p>";
                    echo "<p class='text-sm text-gray-700'>" . esc_html("{$leader->rank} {$leader->first_name} {$leader->last_name}") . "</p>";

                    if (!empty($leader->email)) {
                        echo '<p class="text-sm text-gray-700">';
                        echo '<a href="mailto:' . esc_attr($leader->email) . '" class="text-blue-500 hover:underline">';
                        echo esc_html($leader->email);
                        echo '</a></p>';
                    }

                    if (!empty($leader->phone)) {
                        echo "<p class='text-sm text-gray-700'>" . esc_html($leader->phone) . "</p>";
                    }

                    echo '</div>'; // End individual leader card
                }

                echo '</div>'; // End leaders list
            } else {
                // If no leaders found for the state
                echo '<p class="text-gray-500">No leadership entries found.</p>';
            }

            echo '</div>'; // End toggle content
            echo '</li>';
        }

        echo '</ul>'; // End list of states under this letter
    }

    echo '</div>'; // Close container
    echo '</div>'; // Close section

    return ob_get_clean(); // Return the buffered HTML
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

    // Fetch all area chair entries
    $areas = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sdp_area_chairs");

    ob_start();

    // Begin section layout
    echo '<div class="py-12 bg-gray-50 px-4 sm:px-6 lg:px-8">';
    echo '<div class="max-w-6xl mx-auto">';
    echo '<h2 id="area-section" class="text-3xl font-bold text-gray-900 mb-8">State Leadership by Area</h2>';

    // Loop through each area
    foreach ($areas as $area) {
        echo '<div class="mb-6">';

        // Area name toggle button
        echo '<button class="sdp-toggle-btn w-full text-left text-xl font-semibold text-blue-800 py-2 border border-gray-300 rounded-md bg-white shadow-sm mb-2" data-target-id="area-' . esc_attr($area->id) . '">';
        echo esc_html($area->area_name) . '</button>';

        // Toggle content for the area
        echo '<div id="area-' . esc_attr($area->id) . '" class="sdp-toggle-content hidden">';
        echo '<div class="space-y-8">';

        // === Executive Council Members for this Area ===
        $exec_members = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}sdp_executive_council WHERE position REGEXP %s",
            '^' . $wpdb->esc_like($area->area_name) . '( Chair| Director)?$'
        ));

        if ($exec_members) {
            echo '<div class="flex flex-col sm:flex-row sm:flex-wrap gap-4">';
            foreach ($exec_members as $member) {
                echo '<div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm w-full sm:w-[calc(50%-0.5rem)]">';
                echo '<p class="text-sm font-semibold text-gray-800">' . esc_html($member->position) . '</p>';
                echo '<p class="text-sm text-gray-700">' . esc_html("{$member->rank} {$member->first_name} {$member->last_name}") . '</p>';

                if (!empty($member->email)) {
                    echo '<p class="text-sm text-gray-700"><a href="mailto:' . esc_attr($member->email) . '" class="text-blue-500 hover:underline">' . esc_html($member->email) . '</a></p>';
                }

                if (!empty($member->mobile_phone)) {
                    echo '<p class="text-sm text-gray-700">' . esc_html($member->mobile_phone) . '</p>';
                }

                echo '</div>'; // End executive member card
            }
            echo '</div>'; // End executive members flex wrap
        }

        // === State-Level Leadership under this Area ===
        $states = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}sdp_states WHERE area_id = %d",
            $area->id
        ));

        foreach ($states as $state) {
            echo '<div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-md hover:shadow-lg transition-all duration-200">';
            echo '<h4 class="text-lg font-bold text-blue-700 mb-2">' . esc_html($state->state_name) . '</h4>';

            // Fetch state leadership entries
            $leaders = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}sdp_state_leadership WHERE state_id = %d",
                $state->id
            ));

            if ($leaders) {
                echo '<ul class="space-y-1 text-sm text-gray-700">';
                foreach ($leaders as $leader) {
                    echo '<li class="text-sm text-gray-700 leading-snug">';
                    echo '<strong>' . esc_html($leader->position) . ':</strong> ';
                    echo esc_html("{$leader->rank} {$leader->first_name} {$leader->last_name}") . ' | ';
                    echo '<a href="mailto:' . esc_attr($leader->email) . '" class="text-blue-500 hover:underline">' . esc_html($leader->email) . '</a> | ';
                    echo esc_html($leader->phone) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="text-sm text-gray-500">No leadership found.</p>';
            }

            echo '</div>'; // End state card
        }

        echo '</div>'; // End area inner content
        echo '</div>'; // End area toggle content
        echo '</div>'; // End area block
    }

    echo '</div>'; // End container
    echo '</div>'; // End section

    return ob_get_clean(); // Return rendered HTML
    }
}