<?php get_header(); ?>

<div class="speakers-container">
    <h1 class="speakers-title">Our Speakers</h1>
    
    <!-- Filter Dropdown -->
    <div class="filter-container">
        <label for="org-filter">Filter by Organization:</label>
        <select id="org-filter">
            <option value="">All Organizations</option>
            <?php 
            $orgs = get_organizations_list();
            foreach ($orgs as $org) : 
            ?>
                <option value="<?php echo esc_attr($org); ?>">
                    <?php echo esc_html($org); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- ===== SPEAKER GRID - ONLY ONE! ===== -->
    <div class="speakers-grid">
        <?php
        $speakers = new WP_Query(array(
            'post_type' => 'speaker',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        if ($speakers->have_posts()) :
            while ($speakers->have_posts()) : $speakers->the_post();
                $job = get_post_meta(get_the_ID(), '_job_title', true);
                $org = get_post_meta(get_the_ID(), '_organization', true);
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
                    
                    <?php if (!empty($org)) : ?>
                        <p class="speaker-org"><?php echo esc_html($org); ?></p>
                    <?php endif; ?>
                    
                    <!-- BOOKING BUTTON -->
                    <button class="booking-btn" data-speaker-id="<?php echo get_the_ID(); ?>" data-speaker-name="<?php the_title(); ?>">
                        📅 Book a Meeting
                    </button>
                </div>
        <?php
            endwhile;
        else :
            echo '<p class="no-speakers">No speakers found. Add one in the admin!</p>';
        endif;
        wp_reset_postdata();
        ?>
    </div>
</div>

<!-- Booking Modal -->
<div id="booking-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; padding:40px; border-radius:12px; max-width:400px; width:90%; position:relative;">
        <button id="close-modal" style="position:absolute; top:10px; right:20px; font-size:24px; background:none; border:none; cursor:pointer;">&times;</button>
        
        <h2 style="margin-top:0;">Book a Meeting</h2>
        <p id="modal-speaker-name" style="color:#666; margin-bottom:20px;"></p>
        
        <form id="booking-form">
            <input type="hidden" id="booking-speaker-id" name="speaker_id">
            
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Your Name *</label>
                <input type="text" id="visitor-name" name="visitor_name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Your Email *</label>
                <input type="email" id="visitor-email" name="visitor_email" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
            </div>
            
            <button type="submit" style="width:100%; padding:12px; background:#0073aa; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer;">
                Submit Booking
            </button>
            
            <div id="booking-message" style="margin-top:15px; text-align:center;"></div>
        </form>
    </div>
</div>

<?php get_footer(); ?>