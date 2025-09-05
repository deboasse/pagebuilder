<?php
/**
 * MLP Hardscape Template
 * Modern landing page template with MLP editor integration
 * 
 * Features:
 * - data-mlp-id attributes for stable node identification
 * - data-mlp-type attributes for editing affordances
 * - Support for all editor types (text, richtext, image, bg-image, link, shortcode, embed)
 * - Clean structure for template-based cloning
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if we're in editing mode
$editing_mode = is_user_logged_in() && current_user_can('edit_pages');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo esc_html(dhe_get_content('page_title')); ?></title>
    <meta content="<?php echo esc_attr(dhe_get_content('meta_description')); ?>" name="description"/>
    
    <!-- Schema Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "De'Angele Landscape & Construction",
        "url": "https://deangele.com",
        "logo": "<?php echo esc_url(dhe_get_content('logo_image')); ?>",
        "areaServed": "Massachusetts",
        "description": "Expert outdoor living space transformations including kitchens, fire pits, patios, and retaining walls.",
        "telephone": "+1-781-347-1674"
    }
    </script>
    
    <?php if ($editing_mode): ?>
    <!-- MLP Editor Styles -->
    <link rel="stylesheet" href="<?php echo esc_url(DHE_PLUGIN_URL . 'assets/css/mlp-editor.css'); ?>">
    <?php endif; ?>
    
    <?php wp_head(); ?>
</head>
<body <?php if ($editing_mode) echo 'data-mlp-editing="true"'; ?>>

<?php if ($editing_mode): ?>
<!-- MLP Save Bar -->
<div class="mlp-save-bar">
    <div class="mlp-save-bar-left">
        <span class="mlp-save-status" id="mlp-save-status">Ready to edit</span>
    </div>
    <div class="mlp-save-bar-right">
        <button class="mlp-btn mlp-btn-secondary" onclick="mlpEditor.undo()" title="Undo (Ctrl+Z)">↶ Undo</button>
        <button class="mlp-btn mlp-btn-secondary" onclick="mlpEditor.redo()" title="Redo (Ctrl+Shift+Z)">↷ Redo</button>
        <button class="mlp-btn mlp-btn-primary" onclick="mlpEditor.save()" title="Save (Ctrl+S)">💾 Save</button>
        <button class="mlp-btn mlp-btn-success" onclick="mlpEditor.saveAsNewPage()" title="Create new page">📄 Save as New Page</button>
    </div>
</div>
<?php endif; ?>

<!-- Header -->
<header class="header-menu" id="header">
    <div class="header-content">
        <a href="https://deangele.com" class="logo-link" title="Back to DeAngele Main Website">
            <img 
                src="<?php echo esc_url(dhe_get_content('logo_image')); ?>" 
                alt="DeAngele Landscape & Construction" 
                class="logo-img"
                <?php if ($editing_mode): ?>
                data-mlp-id="logo-image"
                data-mlp-type="image"
                <?php endif; ?>
            >
        </a>
        <a href="https://deangele.com" class="back-to-site">
            &lt;- Back to Main Site
        </a>
    </div>
</header>

<!-- Hero Section -->
<section class="hero" 
         style="background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(0,0,0,0.4)), url('<?php echo esc_url(dhe_get_content('hero_bg_image')); ?>'); background-size: cover; background-position: center;"
         <?php if ($editing_mode): ?>
         data-mlp-id="hero-section"
         data-mlp-type="bg-image"
         <?php endif; ?>>
    
    <div class="hero-content">
        <h1 class="hero-title"
            <?php if ($editing_mode): ?>
            data-mlp-id="hero-title"
            data-mlp-type="text"
            <?php endif; ?>>
            <?php echo esc_html(dhe_get_content('hero_title')); ?>
        </h1>
        
        <p class="hero-subtitle"
           <?php if ($editing_mode): ?>
           data-mlp-id="hero-subtitle"
           data-mlp-type="richtext"
           <?php endif; ?>>
            <?php echo wp_kses_post(dhe_get_content('hero_subtitle')); ?>
        </p>
        
        <a href="#contact" class="cta-button"
           <?php if ($editing_mode): ?>
           data-mlp-id="hero-cta"
           data-mlp-type="link"
           <?php endif; ?>>
            <span <?php if ($editing_mode): ?>
                  data-mlp-id="hero-cta-text"
                  data-mlp-type="text"
                  <?php endif; ?>>
                <?php echo esc_html(dhe_get_content('hero_cta_text')); ?>
            </span>
        </a>
    </div>
</section>

<!-- Sticky CTA -->
<a href="#contact" class="sticky-cta"
   <?php if ($editing_mode): ?>
   data-mlp-id="sticky-cta"
   data-mlp-type="link"
   <?php endif; ?>>
    <span <?php if ($editing_mode): ?>
          data-mlp-id="sticky-cta-text"
          data-mlp-type="text"
          <?php endif; ?>>
        <?php echo esc_html(dhe_get_content('sticky_cta_text')); ?>
    </span>
</a>

<!-- What We Build Section -->
<section class="what-we-build section">
    <div class="container">
        <h2 class="section-title"
            <?php if ($editing_mode): ?>
            data-mlp-id="what-we-build-title"
            data-mlp-type="text"
            <?php endif; ?>>
            <?php echo esc_html(dhe_get_content('what_we_build_title')); ?>
        </h2>
        
        <div class="features-grid">
            <!-- Feature Card 1 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('feature1_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('feature1_title')); ?>" 
                    class="feature-image"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="feature1-image"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
                <div class="feature-content">
                    <h3 class="feature-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="feature1-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('feature1_title')); ?>
                    </h3>
                    <p class="feature-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="feature1-description"
                       data-mlp-type="richtext"
                       <?php endif; ?>>
                        <?php echo wp_kses_post(dhe_get_content('feature1_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Feature Card 2 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('feature2_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('feature2_title')); ?>" 
                    class="feature-image"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="feature2-image"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
                <div class="feature-content">
                    <h3 class="feature-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="feature2-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('feature2_title')); ?>
                    </h3>
                    <p class="feature-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="feature2-description"
                       data-mlp-type="richtext"
                       <?php endif; ?>>
                        <?php echo wp_kses_post(dhe_get_content('feature2_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Feature Card 3 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('feature3_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('feature3_title')); ?>" 
                    class="feature-image"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="feature3-image"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
                <div class="feature-content">
                    <h3 class="feature-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="feature3-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('feature3_title')); ?>
                    </h3>
                    <p class="feature-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="feature3-description"
                       data-mlp-type="richtext"
                       <?php endif; ?>>
                        <?php echo wp_kses_post(dhe_get_content('feature3_description')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2 class="section-title"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="about-title"
                    data-mlp-type="text"
                    <?php endif; ?>>
                    <?php echo esc_html(dhe_get_content('about_title')); ?>
                </h2>
                
                <div class="about-description"
                     <?php if ($editing_mode): ?>
                     data-mlp-id="about-description"
                     data-mlp-type="richtext"
                     <?php endif; ?>>
                    <?php echo wp_kses_post(dhe_get_content('about_description')); ?>
                </div>
                
                <ul class="about-features"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="about-features"
                    data-mlp-type="richtext"
                    <?php endif; ?>>
                    <?php echo wp_kses_post(dhe_get_content('about_features')); ?>
                </ul>
            </div>
            
            <div class="about-image">
                <img 
                    src="<?php echo esc_url(dhe_get_content('about_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('about_title')); ?>" 
                    class="about-img"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="about-image"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services section">
    <div class="container">
        <h2 class="section-title"
            <?php if ($editing_mode): ?>
            data-mlp-id="services-title"
            data-mlp-type="text"
            <?php endif; ?>>
            <?php echo esc_html(dhe_get_content('services_title')); ?>
        </h2>
        
        <div class="services-grid">
            <!-- Service 1 -->
            <div class="service-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('service1_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('service1_title')); ?>" 
                    class="service-image"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="service1-image"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
                <div class="service-content">
                    <h3 class="service-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="service1-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('service1_title')); ?>
                    </h3>
                    <p class="service-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="service1-description"
                       data-mlp-type="richtext"
                       <?php endif; ?>>
                        <?php echo wp_kses_post(dhe_get_content('service1_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Service 2 -->
            <div class="service-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('service2_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('service2_title')); ?>" 
                    class="service-image"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="service2-image"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
                <div class="service-content">
                    <h3 class="service-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="service2-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('service2_title')); ?>
                    </h3>
                    <p class="service-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="service2-description"
                       data-mlp-type="richtext"
                       <?php endif; ?>>
                        <?php echo wp_kses_post(dhe_get_content('service2_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Service 3 -->
            <div class="service-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('service3_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('service3_title')); ?>" 
                    class="service-image"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="service3-image"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
                <div class="service-content">
                    <h3 class="service-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="service3-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('service3_title')); ?>
                    </h3>
                    <p class="service-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="service3-description"
                       data-mlp-type="richtext"
                       <?php endif; ?>>
                        <?php echo wp_kses_post(dhe_get_content('service3_description')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Proof Section -->
<section class="social-proof section">
    <div class="container">
        <h2 class="section-title"
            <?php if ($editing_mode): ?>
            data-mlp-id="social-proof-title"
            data-mlp-type="text"
            <?php endif; ?>>
            <?php echo esc_html(dhe_get_content('social_proof_title')); ?>
        </h2>
        
        <?php if ($editing_mode): ?>
        <!-- Social Proof Type Selector -->
        <div class="mlp-social-proof-selector">
            <label>
                <input type="radio" name="social_proof_type" value="google" 
                       <?php echo (dhe_get_content('social_proof_type') === 'google') ? 'checked' : ''; ?>>
                Google Reviews
            </label>
            <label>
                <input type="radio" name="social_proof_type" value="youtube" 
                       <?php echo (dhe_get_content('social_proof_type') === 'youtube') ? 'checked' : ''; ?>>
                YouTube Testimonials
            </label>
            <label>
                <input type="radio" name="social_proof_type" value="instagram" 
                       <?php echo (dhe_get_content('social_proof_type') === 'instagram' || !dhe_get_content('social_proof_type')) ? 'checked' : ''; ?>>
                Instagram Reviews
            </label>
        </div>
        <?php endif; ?>
        
        <!-- Google Reviews -->
        <div class="social-proof-content" id="google-reviews" 
             style="display: <?php echo (dhe_get_content('social_proof_type') === 'google') ? 'block' : 'none'; ?>;">
            <div class="google-reviews-container"
                 <?php if ($editing_mode): ?>
                 data-mlp-id="google-reviews-shortcode"
                 data-mlp-type="shortcode"
                 <?php endif; ?>>
                <?php echo do_shortcode(dhe_get_content('google_reviews_shortcode') ?: '[trustindex]'); ?>
            </div>
        </div>
        
        <!-- YouTube Testimonials -->
        <div class="social-proof-content" id="youtube-testimonials" 
             style="display: <?php echo (dhe_get_content('social_proof_type') === 'youtube') ? 'block' : 'none'; ?>;">
            <div class="youtube-video-container">
                <div class="youtube-video"
                     <?php if ($editing_mode): ?>
                     data-mlp-id="youtube-video-embed"
                     data-mlp-type="embed"
                     <?php endif; ?>>
                    <?php 
                    $youtube_url = dhe_get_content('youtube_video_url');
                    if ($youtube_url) {
                        $video_id = '';
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                            $video_id = $matches[1];
                        }
                        if ($video_id) {
                            echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr($video_id) . '" frameborder="0" allowfullscreen></iframe>';
                        }
                    }
                    ?>
                </div>
                <div class="youtube-content">
                    <h3 class="youtube-video-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="youtube-video-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('youtube_video_title')); ?>
                    </h3>
                    <p class="youtube-video-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="youtube-video-description"
                       data-mlp-type="richtext"
                       <?php endif; ?>>
                        <?php echo wp_kses_post(dhe_get_content('youtube_video_description')); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Instagram Reviews -->
        <div class="social-proof-content" id="instagram-reviews" 
             style="display: <?php echo (dhe_get_content('social_proof_type') === 'instagram' || !dhe_get_content('social_proof_type')) ? 'block' : 'none'; ?>;">
            <div class="instagram-grid">
                <!-- Instagram Reel 1 -->
                <div class="instagram-reel">
                    <div class="instagram-thumbnail"
                         <?php if ($editing_mode): ?>
                         data-mlp-id="instagram-reel1-thumbnail"
                         data-mlp-type="image"
                         <?php endif; ?>>
                        <img src="<?php echo esc_url(dhe_get_content('instagram_reel1_thumbnail')); ?>" 
                             alt="Instagram Reel 1">
                        <div class="play-overlay">▶</div>
                    </div>
                    <h3 class="instagram-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="instagram-reel1-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('instagram_reel1_title')); ?>
                    </h3>
                    <p class="instagram-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="instagram-reel1-description"
                       data-mlp-type="text"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('instagram_reel1_description')); ?>
                    </p>
                </div>
                
                <!-- Instagram Reel 2 -->
                <div class="instagram-reel">
                    <div class="instagram-thumbnail"
                         <?php if ($editing_mode): ?>
                         data-mlp-id="instagram-reel2-thumbnail"
                         data-mlp-type="image"
                         <?php endif; ?>>
                        <img src="<?php echo esc_url(dhe_get_content('instagram_reel2_thumbnail')); ?>" 
                             alt="Instagram Reel 2">
                        <div class="play-overlay">▶</div>
                    </div>
                    <h3 class="instagram-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="instagram-reel2-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('instagram_reel2_title')); ?>
                    </h3>
                    <p class="instagram-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="instagram-reel2-description"
                       data-mlp-type="text"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('instagram_reel2_description')); ?>
                    </p>
                </div>
                
                <!-- Instagram Reel 3 -->
                <div class="instagram-reel">
                    <div class="instagram-thumbnail"
                         <?php if ($editing_mode): ?>
                         data-mlp-id="instagram-reel3-thumbnail"
                         data-mlp-type="image"
                         <?php endif; ?>>
                        <img src="<?php echo esc_url(dhe_get_content('instagram_reel3_thumbnail')); ?>" 
                             alt="Instagram Reel 3">
                        <div class="play-overlay">▶</div>
                    </div>
                    <h3 class="instagram-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="instagram-reel3-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('instagram_reel3_title')); ?>
                    </h3>
                    <p class="instagram-description"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="instagram-reel3-description"
                       data-mlp-type="text"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('instagram_reel3_description')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact section" id="contact">
    <div class="container">
        <h2 class="section-title"
            <?php if ($editing_mode): ?>
            data-mlp-id="contact-title"
            data-mlp-type="text"
            <?php endif; ?>>
            <?php echo esc_html(dhe_get_content('contact_title')); ?>
        </h2>
        
        <div class="contact-content">
            <div class="contact-info">
                <div class="contact-item">
                    <h3 class="contact-item-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="contact-phone-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('contact_phone_title')); ?>
                    </h3>
                    <a href="tel:<?php echo esc_attr(dhe_get_content('contact_phone')); ?>" 
                       class="contact-link"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="contact-phone"
                       data-mlp-type="link"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('contact_phone')); ?>
                    </a>
                </div>
                
                <div class="contact-item">
                    <h3 class="contact-item-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="contact-email-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('contact_email_title')); ?>
                    </h3>
                    <a href="mailto:<?php echo esc_attr(dhe_get_content('contact_email')); ?>" 
                       class="contact-link"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="contact-email"
                       data-mlp-type="link"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('contact_email')); ?>
                    </a>
                </div>
                
                <div class="contact-item">
                    <h3 class="contact-item-title"
                        <?php if ($editing_mode): ?>
                        data-mlp-id="contact-address-title"
                        data-mlp-type="text"
                        <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('contact_address_title')); ?>
                    </h3>
                    <p class="contact-address"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="contact-address"
                       data-mlp-type="text"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('contact_address')); ?>
                    </p>
                </div>
            </div>
            
            <div class="contact-form">
                <h3 class="form-title"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="contact-form-title"
                    data-mlp-type="text"
                    <?php endif; ?>>
                    <?php echo esc_html(dhe_get_content('contact_form_title')); ?>
                </h3>
                
                <div class="form-description"
                     <?php if ($editing_mode): ?>
                     data-mlp-id="contact-form-description"
                     data-mlp-type="richtext"
                     <?php endif; ?>>
                    <?php echo wp_kses_post(dhe_get_content('contact_form_description')); ?>
                </div>
                
                <!-- Contact Form Shortcode -->
                <div class="contact-form-container"
                     <?php if ($editing_mode): ?>
                     data-mlp-id="contact-form-shortcode"
                     data-mlp-type="shortcode"
                     <?php endif; ?>>
                    <?php 
                    $contact_form_shortcode = dhe_get_content('contact_form_shortcode', '');
                    if ($contact_form_shortcode) {
                        echo do_shortcode($contact_form_shortcode);
                    } else {
                        // Fallback contact form
                        ?>
                        <form class="simple-contact-form" method="post" action="/form-handler.php">
                            <input type="text" name="name" placeholder="Your Name" required>
                            <input type="email" name="email" placeholder="Your Email" required>
                            <input type="tel" name="phone" placeholder="Your Phone">
                            <textarea name="message" placeholder="Tell us about your project" rows="4" required></textarea>
                            <button type="submit">Send Message</button>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img 
                    src="<?php echo esc_url(dhe_get_content('footer_logo')); ?>" 
                    alt="DeAngele Landscape & Construction" 
                    class="footer-logo-img"
                    <?php if ($editing_mode): ?>
                    data-mlp-id="footer-logo"
                    data-mlp-type="image"
                    <?php endif; ?>
                >
            </div>
            
            <div class="footer-info">
                <p class="footer-text"
                   <?php if ($editing_mode): ?>
                   data-mlp-id="footer-text"
                   data-mlp-type="richtext"
                   <?php endif; ?>>
                    <?php echo wp_kses_post(dhe_get_content('footer_text')); ?>
                </p>
                
                <div class="footer-links">
                    <a href="<?php echo esc_url(dhe_get_content('footer_link1_url')); ?>" 
                       class="footer-link"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="footer-link1"
                       data-mlp-type="link"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('footer_link1_text')); ?>
                    </a>
                    <a href="<?php echo esc_url(dhe_get_content('footer_link2_url')); ?>" 
                       class="footer-link"
                       <?php if ($editing_mode): ?>
                       data-mlp-id="footer-link2"
                       data-mlp-type="link"
                       <?php endif; ?>>
                        <?php echo esc_html(dhe_get_content('footer_link2_text')); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p class="copyright"
               <?php if ($editing_mode): ?>
               data-mlp-id="copyright-text"
               data-mlp-type="text"
               <?php endif; ?>>
                <?php echo esc_html(dhe_get_content('copyright_text')); ?>
            </p>
        </div>
    </div>
</footer>

<?php if ($editing_mode): ?>
<!-- MLP Editor Script -->
<script src="<?php echo esc_url(DHE_PLUGIN_URL . 'assets/js/mlp-editor.js'); ?>"></script>
<script>
// Initialize MLP Editor
document.addEventListener('DOMContentLoaded', function() {
    if (typeof MLP_CONFIG !== 'undefined') {
        window.mlpEditor = new MLPEditor(MLP_CONFIG);
    }
});
</script>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
