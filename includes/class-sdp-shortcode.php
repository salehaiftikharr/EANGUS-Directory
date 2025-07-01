<?php

/**
 * Class StateDirectoryShortcode
 * Handles front-end rendering of various sections using shortcodes
 */
class StateDirectoryShortcode {

    /**
     * Main shortcode to render the entire directory page
     */
    public static function render_all() {
        global $wpdb;

        ob_start();

        // Hero section with title and tab links
        echo '<div class="bg-gradient-to-r from-slate-900 to-blue-900 text-white py-16 px-4">';
        echo '<div class="max-w-4xl mx-auto text-center">';
        echo '<h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-4">State Association Directory</h1>';
        echo '<div class="border-t-2 border-b-2 border-yellow-400 w-24 mx-auto my-4"></div>';
        echo '<p class="text-lg font-light">FILTER BY</p>';
        echo '<div class="mt-4 text-lg font-medium space-x-4">';
        echo '<a href="#area-section" class="hover:underline">Area</a> <span class="text-white">|</span> ';
        echo '<a href="#state-section" class="hover:underline">State</a> <span class="text-white">|</span> ';
        echo '<a href="#conference-section" class="font-bold hover:underline">Conference</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Main content wrapper
        echo '<div class="bg-gray-50 min-h-screen py-10 px-4 sm:px-8 font-sans text-gray-800">';
        echo '<div class="max-w-6xl mx-auto space-y-12">';

        // Optional grid for state cards (currently not rendered)
        echo '<div id="state-card-grid" class="sdp-card-grid">';
        // echo self::render_state_cards(); // Uncomment if needed
        echo '</div>';

        // Optional modal markup for card display (not active unless JS uses it)
        echo '<div id="sdp-modal" class="sdp-modal">';
        echo '<div class="sdp-modal-content">';
        // echo '<span class="sdp-close">&times;</span>'; // Optional close button
        echo '<div id="sdp-modal-body"></div>';
        echo '</div></div>';

        // Render each directory section
        echo StateDirectoryRenderer::render_area_state_leadership();
        echo StateDirectoryRenderer::render_state_leadership_by_state();
        echo StateDirectoryRenderer::render_annual_conferences();
        echo StateDirectoryRenderer::render_executive_officers();
        echo StateDirectoryRenderer::render_past_presidents();

        echo '</div></div>'; // close content and container wrappers

        return ob_get_clean();
    }

    /**
     * Helper: Get distinct values from a column in a table
     */
    private static function get_unique_column_values($table, $column) {
        global $wpdb;
        $full_table = $wpdb->prefix . $table;
        return $wpdb->get_col("SELECT DISTINCT $column FROM $full_table WHERE $column IS NOT NULL AND $column != '' ORDER BY $column ASC");
    }

}