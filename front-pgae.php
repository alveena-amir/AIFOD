<?php get_header(); 
echo "<!-- front-page.php is LOADING -->";
?>

<div class="speakers-grid">
    <?php
    $speakers = new WP_Query(array(
        'post_type' => 'speaker',
        'posts_per_page' => -1
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
                <?php endif; ?>
                
                <h2 class="speaker-name"><?php the_title(); ?></h2>
                
                <?php if (!empty($job)) : ?>
                    <p class="speaker-job"><?php echo esc_html($job); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($org)) : ?>
                    <p class="speaker-org"><?php echo esc_html($org); ?></p>
                <?php endif; ?>
            </div>
    <?php
        endwhile;
    else :
        echo '<p>No speakers found. Add some in the admin!</p>';
    endif;
    wp_reset_postdata();
    ?>
</div>

<?php get_footer(); ?>