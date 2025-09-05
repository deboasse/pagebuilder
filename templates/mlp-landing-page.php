<?php
/**
 * Template Name: MLP Landing Page
 * 
 * Custom template for MLP-generated pages that handles all review types:
 * - Google Reviews (Trustindex shortcode)
 * - YouTube video reviews
 * - Instagram reel reviews
 * 
 * This template ensures proper CSS loading and theme compatibility.
 */

get_header(); ?>

<style type="text/css">
/* Load template CSS */
@import url('<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/css/template.css'); ?>');

/* WordPress compatibility styles */
.entry-content { 
    margin: 0 !important; 
    padding: 0 !important; 
}

.entry-content h1,
.entry-content h2,
.entry-content h3,
.entry-content h4,
.entry-content h5,
.entry-content h6 {
    margin: 0 !important;
    padding: 0 !important;
}

.entry-content p {
    margin: 0 !important;
    padding: 0 !important;
}

/* Ensure MLP content takes full width */
.mlp-landing-page {
    width: 100%;
    max-width: none;
}

/* Override theme container if needed */
.mlp-landing-page .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
</style>

<?php
// Get MLP content data
$page_id = get_the_ID();
$content_data = get_post_meta($page_id, 'mlp_content_data', true);

// Fallback to default content if no MLP data
if (empty($content_data)) {
    $content_data = array();
}

// Helper function to get content with fallback
function mlp_get_content($key, $default = '') {
    global $content_data;
    return isset($content_data[$key]) ? $content_data[$key] : $default;
}
?>

<div class="mlp-landing-page">
    <!-- Header -->
    <header class="header-menu" id="header">
        <div class="header-content">
            <a href="https://deangele.com" class="logo-link" title="Back to DeAngele Main Website">
                <img 
                    src="<?php echo esc_url(mlp_get_content('logo_image', 'https://deangele.com/wp-content/uploads/2024/05/deangele-logo.png')); ?>" 
                    alt="DeAngele Landscape & Construction" 
                    class="logo-img">
            </a>
            <a href="https://deangele.com" class="back-to-site">
                &lt;- Back to Main Site
            </a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" 
             style="background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(0,0,0,0.4)), url('<?php echo esc_url(mlp_get_content('hero_bg_image', 'https://deangele.com/wp-content/uploads/2024/05/outdoor-kitchen-hero.jpg')); ?>'); background-size: cover; background-position: center;">
        
        <div class="hero-content">
            <h1 class="hero-title">
                <?php echo esc_html(mlp_get_content('hero_title', 'Transform Your Outdoor Space')); ?>
            </h1>
            
            <p class="hero-subtitle">
                <?php echo wp_kses_post(mlp_get_content('hero_subtitle', 'Expert outdoor living space transformations including kitchens, fire pits, patios, and retaining walls.')); ?>
            </p>
            
            <a href="#contact" class="cta-button">
                <span><?php echo esc_html(mlp_get_content('hero_cta_text', 'Get Your Free Quote')); ?></span>
            </a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <h2 class="section-title">
                <?php echo esc_html(mlp_get_content('services_title', 'Our Services')); ?>
            </h2>
            
            <div class="services-grid">
                <!-- Service 1 -->
                <div class="service-card">
                    <img src="<?php echo esc_url(mlp_get_content('service1_image', 'https://deangele.com/wp-content/uploads/2024/05/outdoor-kitchen-service.jpg')); ?>" 
                         alt="<?php echo esc_attr(mlp_get_content('service1_title', 'Outdoor Kitchens')); ?>"
                         class="service-image">
                    <h3><?php echo esc_html(mlp_get_content('service1_title', 'Outdoor Kitchens')); ?></h3>
                    <p><?php echo esc_html(mlp_get_content('service1_description', 'Custom outdoor kitchen designs with premium materials and professional installation.')); ?></p>
                </div>
                
                <!-- Service 2 -->
                <div class="service-card">
                    <img src="<?php echo esc_url(mlp_get_content('service2_image', 'https://deangele.com/wp-content/uploads/2024/05/fire-pit-service.jpg')); ?>" 
                         alt="<?php echo esc_attr(mlp_get_content('service2_title', 'Fire Pits')); ?>"
                         class="service-image">
                    <h3><?php echo esc_html(mlp_get_content('service2_title', 'Fire Pits')); ?></h3>
                    <p><?php echo esc_html(mlp_get_content('service2_description', 'Beautiful fire pit designs to create the perfect gathering spot in your backyard.')); ?></p>
                </div>
                
                <!-- Service 3 -->
                <div class="service-card">
                    <img src="<?php echo esc_url(mlp_get_content('service3_image', 'https://deangele.com/wp-content/uploads/2024/05/patio-service.jpg')); ?>" 
                         alt="<?php echo esc_attr(mlp_get_content('service3_title', 'Patios & Walkways')); ?>"
                         class="service-image">
                    <h3><?php echo esc_html(mlp_get_content('service3_title', 'Patios & Walkways')); ?></h3>
                    <p><?php echo esc_html(mlp_get_content('service3_description', 'Durable and stylish patios and walkways using premium hardscape materials.')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Reviews Section -->
    <?php echo mlp_render_reviews_section($content_data); ?>

    <!-- Hardscape Materials Section -->
    <section class="materials" id="materials">
        <div class="container">
            <h2 class="section-title">
                <?php echo esc_html(mlp_get_content('materials_title', 'Premium Hardscape Materials')); ?>
            </h2>
            
            <div class="materials-grid">
                <!-- Material 1 -->
                <div class="material-card">
                    <img src="<?php echo esc_url(mlp_get_content('hardscape_material1_image', 'https://deangele.com/wp-content/uploads/2024/05/natural-stone-veneer-fireplace-hingham-ma.jpg')); ?>" 
                         alt="<?php echo esc_attr(mlp_get_content('hardscape_material1_title', 'Natural Stone Veneer')); ?>"
                         class="material-image">
                    <h3><?php echo esc_html(mlp_get_content('hardscape_material1_title', 'Natural Stone Veneer')); ?></h3>
                    <p><?php echo esc_html(mlp_get_content('hardscape_material1_description', 'Desert Creek stone veneer creates stunning focal points for outdoor fireplaces and accent walls.')); ?></p>
                </div>
                
                <!-- Material 2 -->
                <div class="material-card">
                    <img src="<?php echo esc_url(mlp_get_content('hardscape_material2_image', 'https://deangele.com/wp-content/uploads/2024/05/granite-outdoor-kitchen-countertop-hingham.jpg')); ?>" 
                         alt="<?php echo esc_attr(mlp_get_content('hardscape_material2_title', 'Granite Countertop Material')); ?>"
                         class="material-image">
                    <h3><?php echo esc_html(mlp_get_content('hardscape_material2_title', 'Granite Countertop Material')); ?></h3>
                    <p><?php echo esc_html(mlp_get_content('hardscape_material2_description', 'Premium granite surfaces for outdoor kitchen islands and bar tops, engineered for weather resistance.')); ?></p>
                </div>
                
                <!-- Material 3 -->
                <div class="material-card">
                    <img src="<?php echo esc_url(mlp_get_content('hardscape_material3_image', 'https://deangele.com/wp-content/uploads/2024/05/stacked-stone-outdoor-kitchen-base-hingham.jpg')); ?>" 
                         alt="<?php echo esc_attr(mlp_get_content('hardscape_material3_title', 'Stacked Stone Base')); ?>"
                         class="material-image">
                    <h3><?php echo esc_html(mlp_get_content('hardscape_material3_title', 'Stacked Stone Base')); ?></h3>
                    <p><?php echo esc_html(mlp_get_content('hardscape_material3_description', 'Durable stacked stone construction for outdoor kitchen islands and structural elements.')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <h2 class="section-title">
                <?php echo esc_html(mlp_get_content('contact_title', 'Get Your Free Quote')); ?>
            </h2>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h3><?php echo esc_html(mlp_get_content('contact_subtitle', 'Ready to Transform Your Outdoor Space?')); ?></h3>
                    <p><?php echo esc_html(mlp_get_content('contact_description', 'Contact us today for a free consultation and quote on your outdoor living project.')); ?></p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <strong>Phone:</strong> 
                            <a href="tel:+17813471674"><?php echo esc_html(mlp_get_content('contact_phone', '+1-781-347-1674')); ?></a>
                        </div>
                        <div class="contact-item">
                            <strong>Email:</strong> 
                            <a href="mailto:info@deangele.com"><?php echo esc_html(mlp_get_content('contact_email', 'info@deangele.com')); ?></a>
                        </div>
                        <div class="contact-item">
                            <strong>Service Area:</strong> 
                            <?php echo esc_html(mlp_get_content('contact_area', 'Hingham, MA and surrounding areas')); ?>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <?php 
                    $contact_form_shortcode = mlp_get_content('contact_form_shortcode', '');
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
    </section>
</div>

<?php get_footer(); ?>


