<?php

/**
 * Enhanced Class StateDirectoryShortcode
 * Handles front-end rendering with better theme isolation
 */
class StateDirectoryShortcode {

    public static function render_all() {
        global $wpdb;

        ob_start();

        // Wrap everything in our container class for CSS isolation
        echo '<div class="sdp-container">';

        // Hero section with title and tab links
        echo '<div class="sdp-hero">';
        echo '<div class="sdp-hero-inner">';
        echo '<h1 class="sdp-hero-title">EANGUS Directory</h1>';
        echo '<div class="sdp-hero-divider"></div>';
        echo '<p class="sdp-hero-subtitle">FILTER BY</p>';
        echo '<div class="sdp-hero-links">';
        echo '<a href="#area-section">Area</a> <span>|</span> ';
        echo '<a href="#state-section">State</a> <span>|</span> ';
        echo '<a href="#conference-section">Conference</a> <span>|</span> ';
        echo '<a href="#executive-section">Executive Officers</a> <span>|</span> ';
        echo '<a href="#past-presidents-section">Past Presidents</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Main content wrapper
        echo '<div class="sdp-main">';
        echo '<div class="sdp-main-inner">';

        // Area Leadership
        echo '<section class="sdp-section">';
        echo '<h2 id="area-section" class="section-title">Leadership by Area</h2>';
        echo '<p class="sdp-intro">Explore leadership organized by geographical area, including area chairs and directors.</p>';
        echo StateDirectoryRenderer::render_area_state_leadership();
        echo '</section>';

        // State Leadership
        echo '<section class="sdp-section">';
        echo '<h2 id="state-section" class="section-title">Leadership by State</h2>';
        echo '<p class="sdp-intro">View EANGUS council members for each individual chapter.</p>';
        echo StateDirectoryRenderer::render_state_leadership_by_state();
        echo '</section>';

        // Annual Conferences
        echo '<section class="sdp-section">';
        echo '<h2 id="conference-section" class="section-title">Annual Conferences</h2>';
        echo '<p class="sdp-intro">Historical listing of past EANGUS Annual Conferences.</p>';
        echo StateDirectoryRenderer::render_annual_conferences();
        echo '</section>';

        // Executive Officers
        echo '<section class="sdp-section">';
        echo '<h2 id="executive-section" class="section-title">Executive Officers</h2>';
        echo '<p class="sdp-intro">Current national executive officers serving EANGUS.</p>';
        echo StateDirectoryRenderer::render_executive_officers();
        echo '</section>';

        // Past Presidents
        echo '<section class="sdp-section">';
        echo '<h2 id="past-presidents-section" class="section-title">Past Presidents</h2>';
        echo '<p class="sdp-intro">A complete record of past presidents who have served in EANGUS leadership.</p>';
        echo StateDirectoryRenderer::render_past_presidents();
        echo '</section>';

        echo '</div>'; // .sdp-main-inner
        echo '</div>'; // .sdp-main
        echo '</div>'; // .sdp-container - Close the wrapper

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