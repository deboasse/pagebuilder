<?php
/**
 * Template Name: Generated Landing Page
 * 
 * Template for pages generated from the DeAngele Hardscape Editor.
 * This template uses the same structure as hardscape-template.php but loads
 * content from page meta instead of default content.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get the content data from page meta
$page_id = get_the_ID();
$content_data = get_post_meta($page_id, 'dhe_content_data', true);

// If no content data, fall back to default content
if (empty($content_data)) {
    $content_data = array();
}

// Create a local function to get content from our meta data
function dhe_get_content_from_meta($key, $default = '') {
    global $content_data;
    return isset($content_data[$key]) ? $content_data[$key] : $default;
}

// Temporarily override the global dhe_get_content function using a closure
$original_dhe_get_content = null;
if (function_exists('dhe_get_content')) {
    $original_dhe_get_content = 'dhe_get_content';
}

// Create a temporary override using a different approach
function dhe_get_content_override($key, $default = '') {
    global $content_data;
    return isset($content_data[$key]) ? $content_data[$key] : $default;
}

// Set editing mode to false for generated pages
$editing_mode = false;

// Store the original function and create our override
if (function_exists('dhe_get_content')) {
    // We can't redeclare the function, so we'll use a different approach
    // We'll use our local function instead
    $dhe_get_content = 'dhe_get_content_from_meta';
} else {
    // If the function doesn't exist, we can declare it
    function dhe_get_content($key, $default = '') {
        return dhe_get_content_from_meta($key, $default);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo esc_html(dhe_get_content_from_meta('page_title', get_the_title())); ?></title>
    <meta content="<?php echo esc_attr(dhe_get_content_from_meta('meta_description')); ?>" name="description"/>
    
    <!-- Schema Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "De'Angele Landscape & Construction",
        "url": "https://deangele.com",
        "logo": "<?php echo esc_url(dhe_get_content_from_meta('logo_image')); ?>",
        "areaServed": "Massachusetts",
        "description": "Expert outdoor living space transformations including kitchens, fire pits, patios, and retaining walls.",
        "telephone": "+1-781-347-1674"
    }
    </script>
    
    <?php wp_head(); ?>
</head>
<body>

<!-- Header -->
<header class="header-menu" id="header">
    <div class="header-content">
        <a href="https://deangele.com" class="logo-link" title="Back to DeAngele Main Website">
            <img 
                src="<?php echo esc_url(dhe_get_content_from_meta('logo_image')); ?>" 
                alt="DeAngele Landscape & Construction" 
                class="logo-img">
        </a>
        <a href="https://deangele.com" class="back-to-site">
            &lt;- Back to Main Site
        </a>
    </div>
</header>

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(0,0,0,0.4)), url('<?php echo esc_url(dhe_get_content_from_meta('hero_bg_image')); ?>'); background-size: cover; background-position: center;">
    <div class="hero-content">
        <h1 class="hero-title">
            <?php echo esc_html(dhe_get_content_from_meta('hero_title')); ?>
        </h1>
        
        <p class="hero-subtitle">
            <?php echo esc_html(dhe_get_content_from_meta('hero_subtitle')); ?>
        </p>
        
        <a href="#contact" class="cta-button">
            <span><?php echo esc_html(dhe_get_content_from_meta('hero_cta_text')); ?></span>
        </a>
    </div>
</section>

<!-- Sticky CTA -->
<a href="#contact" class="sticky-cta">
    <span><?php echo esc_html(dhe_get_content_from_meta('sticky_cta_text')); ?></span>
</a>

<!-- What We Build Section -->
<section class="what-we-build section">
    <div class="container">
        <h2 class="section-title">
            <?php echo esc_html(dhe_get_content_from_meta('what_we_build_title')); ?>
        </h2>
        
        <div class="features-grid">
            <!-- Feature Card 1 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content_from_meta('feature1_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content_from_meta('feature1_title')); ?>" 
                    class="feature-image">
                <div class="feature-content">
                    <h3 class="feature-title">
                        <?php echo esc_html(dhe_get_content_from_meta('feature1_title')); ?>
                    </h3>
                    <p class="feature-description">
                        <?php echo esc_html(dhe_get_content_from_meta('feature1_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Feature Card 2 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content_from_meta('feature2_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content_from_meta('feature2_title')); ?>" 
                    class="feature-image">
                <div class="feature-content">
                    <h3 class="feature-title">
                        <?php echo esc_html(dhe_get_content_from_meta('feature2_title')); ?>
                    </h3>
                    <p class="feature-description">
                        <?php echo esc_html(dhe_get_content_from_meta('feature2_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Feature Card 3 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content_from_meta('feature3_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content_from_meta('feature3_title')); ?>" 
                    class="feature-image">
                <div class="feature-content">
                    <h3 class="feature-title">
                        <?php echo esc_html(dhe_get_content_from_meta('feature3_title')); ?>
                    </h3>
                    <p class="feature-description">
                        <?php echo esc_html(dhe_get_content_from_meta('feature3_description')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bottom Section (Reviews/Video/Instagram) -->
<section class="bottom-section section" id="reviews">
    <div class="container">
        <?php
        $social_proof_type = dhe_get_content_from_meta('social_proof_type', 'instagram');
        
        switch ($social_proof_type) {
            case 'google_reviews':
                $shortcode = dhe_get_content_from_meta('google_reviews_shortcode');
                if ($shortcode) {
                    echo do_shortcode($shortcode);
                }
                break;
                
            case 'youtube':
                $video_url = dhe_get_content_from_meta('youtube_video_url');
                $video_title = dhe_get_content_from_meta('youtube_video_title', 'Customer Review Video');
                $video_description = dhe_get_content_from_meta('youtube_video_description');
                
                if ($video_url) {
                    // Extract video ID
                    $video_id = '';
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches)) {
                        $video_id = $matches[1];
                    }
                    
                    if ($video_id) {
                        ?>
                        <div class="video-section">
                            <h2 class="section-title"><?php echo esc_html($video_title); ?></h2>
                            <div class="video-wrapper">
                                <iframe 
                                    width="100%" 
                                    height="400" 
                                    src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>?rel=0" 
                                    frameborder="0" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <?php if ($video_description): ?>
                                <p class="video-description"><?php echo esc_html($video_description); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                break;
                
            case 'instagram':
            default:
                ?>
                <div class="instagram-section">
                    <h2 class="section-title">Customer Reviews</h2>
                    <div class="instagram-grid">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <?php 
                            $thumbnail = dhe_get_content_from_meta("instagram_reel{$i}_thumbnail");
                            $url = dhe_get_content_from_meta("instagram_reel{$i}_url");
                            $title = dhe_get_content_from_meta("instagram_reel{$i}_title");
                            $description = dhe_get_content_from_meta("instagram_reel{$i}_description");
                            ?>
                            
                            <?php if ($thumbnail && $url): ?>
                                <div class="instagram-item">
                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                        <img src="<?php echo esc_url($thumbnail); ?>" 
                                             alt="<?php echo esc_attr($title); ?>"
                                             class="instagram-thumbnail">
                                    </a>
                                    <div class="instagram-info">
                                        <h3><?php echo esc_html($title); ?></h3>
                                        <p><?php echo esc_html($description); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php
                break;
        }
        ?>
    </div>
</section>

<!-- Contact Section -->
<section class="contact section" id="contact">
    <div class="container">
        <h2 class="section-title">
            <?php echo esc_html(dhe_get_content_from_meta('contact_title')); ?>
        </h2>
        
        <div class="contact-content">
            <div class="contact-info">
                <h3><?php echo esc_html(dhe_get_content_from_meta('contact_subtitle')); ?></h3>
                <p><?php echo esc_html(dhe_get_content_from_meta('contact_description')); ?></p>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <strong>Phone:</strong> 
                        <a href="tel:+17813471674"><?php echo esc_html(dhe_get_content_from_meta('contact_phone')); ?></a>
                    </div>
                    <div class="contact-item">
                        <strong>Email:</strong> 
                        <a href="mailto:info@deangele.com"><?php echo esc_html(dhe_get_content_from_meta('contact_email')); ?></a>
                    </div>
                    <div class="contact-item">
                        <strong>Service Area:</strong> 
                        <?php echo esc_html(dhe_get_content_from_meta('contact_area')); ?>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <?php 
                $contact_form_shortcode = dhe_get_content_from_meta('contact_form_shortcode');
                if ($contact_form_shortcode) {
                    echo do_shortcode($contact_form_shortcode);
                } else {
                    // Fallback contact form
                    ?>
                    <form class="simple-contact-form" method="post" action="/form-handler.php">
                        <!-- SPAM PROTECTION: Honeypot fields (invisible to users) -->
                        <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true" tabindex="-1">
                            <label for="website">Website (leave blank)</label>
                            <input type="text" name="website" id="website" autocomplete="new-password" tabindex="-1">
                            <label for="url">URL (leave blank)</label>
                            <input type="url" name="url" id="url" autocomplete="new-password" tabindex="-1">
                            <label for="company">Company (leave blank)</label>
                            <input type="text" name="company" id="company" autocomplete="new-password" tabindex="-1">
                        </div>
                        <!-- SPAM PROTECTION: Time tracking -->
                        <input type="hidden" name="form_timer" id="form_timer" value="">
                        <input type="hidden" name="page_loaded" id="page_loaded" value="">
                        
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="email" name="email" placeholder="Your Email">
                        <input type="tel" name="phone" placeholder="Your Phone" required>
                        <input type="text" name="zip" placeholder="Zip Code" required pattern="[0-9]{5}" maxlength="5">
                        <select name="project" required>
                            <option value="">Select Project Type</option>
                            <option value="patio">Patio Installation</option>
                            <option value="walkway">Walkway/Pathway</option>
                            <option value="driveway">Driveway</option>
                            <option value="outdoor-kitchen">Outdoor Kitchen</option>
                            <option value="fire-pit">Fire Pit/Fireplace</option>
                            <option value="retaining-wall">Retaining Wall</option>
                            <option value="multiple">Multiple Projects</option>
                            <option value="other">Other</option>
                        </select>
                        <textarea name="details" placeholder="Tell us about your project" rows="4"></textarea>
                        <button type="submit" name="hardscape_submit">Send Message</button>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</section>

<script>
// Initialize form timer for spam protection
document.addEventListener('DOMContentLoaded', function() {
    const formTimer = document.getElementById('form_timer');
    const pageLoaded = document.getElementById('page_loaded');
    
    if (formTimer) {
        formTimer.value = Date.now();
    }
    if (pageLoaded) {
        pageLoaded.value = Date.now();
    }
});
</script>

<?php wp_footer(); ?>
</body>
</html>
