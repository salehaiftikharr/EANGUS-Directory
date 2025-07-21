<?php

    /**
     * Class StateDirectoryShortcode
     * Handles front-end rendering of various sections using shortcodes
     */
    class StateDirectoryShortcode {

    public static function render_all() {
    global $wpdb;

    ob_start();

    // Hero section with title and tab links
    echo '<div class="sdp-hero">';
    echo '<div class="sdp-hero-inner">';
    echo '<h1 class="sdp-hero-title">State Association Directory</h1>';
    echo '<div class="sdp-hero-divider"></div>';
    echo '<p class="sdp-hero-subtitle">FILTER BY</p>';
    echo '<div class="sdp-hero-links">';
    echo '<a href="#area-section">Area</a> <span>|</span> ';
    echo '<a href="#state-section">State</a> <span>|</span> ';
    echo '<a href="#conference-section" class="active">Conference</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Main content wrapper
    echo '<div class="sdp-main">';
    echo '<div class="sdp-main-inner">';

    echo '<div id="state-card-grid" class="sdp-card-grid"></div>';

    echo '<div id="sdp-modal" class="sdp-modal">';
    echo '<div class="sdp-modal-content">';
    echo '<div id="sdp-modal-body"></div>';
    echo '</div></div>';

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