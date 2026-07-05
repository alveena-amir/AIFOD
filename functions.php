<?php
// 1. CREATE SPEAKER POST TYPE
add_action('init', 'create_speaker_post_type');

function create_speaker_post_type() {
    register_post_type('speaker', array(
        'labels' => array(
            'name' => 'Speakers',
            'singular_name' => 'Speaker',
            'add_new' => 'Add New Speaker',
        ),
        'public' => true,
        'supports' => array('title', 'thumbnail'),
        'menu_icon' => 'dashicons-groups',
        'show_in_rest' => true
    ));
}

// 2. ADD CUSTOM FIELDS
add_action('add_meta_boxes', 'add_speaker_fields');

function add_speaker_fields() {
    add_meta_box('speaker_details', 'Speaker Details', 'show_speaker_fields', 'speaker');
}

function show_speaker_fields($post) {
    wp_nonce_field('save_speaker', 'speaker_nonce');
    
    $job = get_post_meta($post->ID, '_job_title', true);
    $org = get_post_meta($post->ID, '_organization', true);
    ?>
    <p>
        <label>Job Title:</label><br>
        <input type="text" name="job_title" value="<?php echo esc_attr($job); ?>" style="width:100%">
    </p>
    <p>
        <label>Organization:</label><br>
        <input type="text" name="organization" value="<?php echo esc_attr($org); ?>" style="width:100%">
    </p>
    <?php
}

// 3. SAVE CUSTOM FIELDS
add_action('save_post_speaker', 'save_speaker_fields');

function save_speaker_fields($post_id) {
    if (!isset($_POST['speaker_nonce']) || !wp_verify_nonce($_POST['speaker_nonce'], 'save_speaker')) {
        return;
    }
    
    if (isset($_POST['job_title'])) {
        update_post_meta($post_id, '_job_title', sanitize_text_field($_POST['job_title']));
    }
    
    if (isset($_POST['organization'])) {
        update_post_meta($post_id, '_organization', sanitize_text_field($_POST['organization']));
    }
}

// 4. LOAD CSS
add_action('wp_enqueue_scripts', 'load_theme_css');

function load_theme_css() {
    wp_enqueue_style('theme-style', get_stylesheet_uri());
}
// TASK 2A: AJAX ORGANIZATION FILTER

// 1. Handle AJAX request
add_action('wp_ajax_filter_speakers', 'filter_speakers_callback');
add_action('wp_ajax_nopriv_filter_speakers', 'filter_speakers_callback');

function filter_speakers_callback() {
    // Verify nonce for security
    check_ajax_referer('speaker_filter_nonce', 'nonce');
    
    // Get the organization from the AJAX request
    $org = isset($_POST['organization']) ? sanitize_text_field($_POST['organization']) : '';
    
    // Build query
    $args = array(
        'post_type' => 'speaker',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    
    // If organization is not empty, filter by it
    if (!empty($org)) {
        $args['meta_query'] = array(
            array(
                'key' => '_organization',
                'value' => $org,
                'compare' => '='
            )
        );
    }
    
    $query = new WP_Query($args);
    
    // Start output buffer
    ob_start();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $job = get_post_meta(get_the_ID(), '_job_title', true);
            $organization = get_post_meta(get_the_ID(), '_organization', true);
            ?>
            <div class="speaker-card">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="speaker-portrait">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                <?php else : ?>
                    <div class="speaker-portrait placeholder">
                        <span>📷 No Photo</span>
                    </div>
                <?php endif; ?>
                
                <h2 class="speaker-name"><?php the_title(); ?></h2>
                
                <?php if (!empty($job)) : ?>
                    <p class="speaker-job"><?php echo esc_html($job); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($organization)) : ?>
                    <p class="speaker-org"><?php echo esc_html($organization); ?></p>
                <?php endif; ?>
            </div>
            <?php
        }
    } else {
        echo '<p class="no-speakers">No speakers found for this organization.</p>';
    }
    wp_reset_postdata();
    
    // Send the HTML back to JavaScript
    wp_send_json_success(ob_get_clean());
}

// 2. Get unique organizations for dropdown
function get_organizations_list() {
    global $wpdb;
    $results = $wpdb->get_col("
        SELECT DISTINCT meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_organization' 
        AND meta_value != ''
        ORDER BY meta_value ASC
    ");
    return $results;
}

// 3. Load JavaScript for AJAX
add_action('wp_enqueue_scripts', 'load_ajax_js');

function load_ajax_js() {
    if (is_front_page()) {
        wp_enqueue_script(
            'speaker-filter',
            get_template_directory_uri() . '/filter.js',
            array('jquery'),
            '1.0',
            true
        );
        
        // Pass data to JavaScript
        wp_localize_script('speaker-filter', 'ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('speaker_filter_nonce')
        ));
    }
}

// TASK 2B: BOOK A MEETING FORM

// 1. Create Booking Custom Post Type
add_action('init', 'create_booking_post_type');

function create_booking_post_type() {
    register_post_type('booking', array(
        'labels' => array(
            'name' => 'Bookings',
            'singular_name' => 'Booking',
            'add_new' => 'Add Booking',
            'all_items' => 'All Bookings',
        ),
        'public' => true,
        'show_in_menu' => true,
        'supports' => array('title'),
        'menu_icon' => 'dashicons-calendar-alt',
    ));
}

// 2. Handle booking form submission
add_action('wp_ajax_save_booking', 'save_booking_callback');
add_action('wp_ajax_nopriv_save_booking', 'save_booking_callback');

function save_booking_callback() {
    // Verify nonce for security - USING SAME NONCE AS FILTER
    check_ajax_referer('speaker_filter_nonce', 'nonce');  // ← FIXED!
    
    // Get and sanitize data
    $speaker_id = isset($_POST['speaker_id']) ? intval($_POST['speaker_id']) : 0;
    $visitor_name = isset($_POST['visitor_name']) ? sanitize_text_field($_POST['visitor_name']) : '';
    $visitor_email = isset($_POST['visitor_email']) ? sanitize_email($_POST['visitor_email']) : '';
    
    // Validate
    if (!$speaker_id || empty($visitor_name) || !is_email($visitor_email)) {
        wp_send_json_error('Invalid data. Please check all fields.');
        return;
    }
    
    // Save booking
    $post_id = wp_insert_post(array(
        'post_title' => 'Booking: ' . $visitor_name . ' - ' . get_the_title($speaker_id),
        'post_type' => 'booking',
        'post_status' => 'publish',
        'meta_input' => array(
            '_booking_speaker_id' => $speaker_id,
            '_booking_speaker_name' => get_the_title($speaker_id),
            '_booking_visitor_name' => $visitor_name,
            '_booking_visitor_email' => $visitor_email,
            '_booking_date' => current_time('mysql')
        )
    ));
    
    if ($post_id) {
        wp_send_json_success('✅ Booking saved successfully!');
    } else {
        wp_send_json_error('❌ Error saving booking.');
    }
}