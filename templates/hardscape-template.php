<?php
/**
 * Template Name: DeAngele Hardscape Landing Page
 * 
 * A professional landing page template for DeAngele Landscape & Construction.
 * This template creates a modern, responsive landing page with dynamic content
 * that can be customized through WordPress custom fields.
 * 
 * @package DeAngele_Page_Builder
 * @version 1.0.0
 * @author DeAngele Development Team
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
    
    <?php wp_head(); ?>
    <!-- DHE Debug: Template v2.1.0 loaded with fixed Instagram layout -->
</head>
<body <?php if ($editing_mode) echo 'data-dhe-editing="true"'; ?>>

<!-- Editing Toolbar (only visible in editing mode) -->
<?php if ($editing_mode): ?>
<div id="dhe-editing-toolbar">
    <div class="dhe-toolbar-content">
        <span class="dhe-toolbar-title">✏️ Page: <?php echo esc_html(get_query_var('dhe_variant') ?: 'default'); ?></span>
        <div class="dhe-toolbar-actions">
            <button id="dhe-save-as-btn" class="dhe-btn dhe-btn-warning" title="Create a new WordPress Page with the current content">
                📄 Save as WordPress Page
            </button>
            <button id="dhe-duplicate-btn" class="dhe-btn dhe-btn-warning">
                📋 Duplicate Page  
            </button>
            <button id="dhe-preview-toggle" class="dhe-btn dhe-btn-toggle">
                👁️ Preview Mode
            </button>
            <button id="dhe-save-all" class="dhe-btn dhe-btn-primary" disabled>
                💾 Save Changes
            </button>
            <button id="dhe-generate-page" class="dhe-btn dhe-btn-secondary">
                📄 Generate New Page
            </button>
            <button id="dhe-force-update" class="dhe-btn dhe-btn-warning" style="font-size: 12px;">
                🔄 Fix Materials
            </button>
            <button id="dhe-test-instagram" class="dhe-btn dhe-btn-info" style="font-size: 12px;">
                📱 Test Instagram API
            </button>
            <!-- Status notifications will be injected here -->
        </div>
    </div>
</div>

<!-- Status Notification Container -->
<div id="dhe-notifications-container"></div>

<!-- Page Management Modal -->
<div id="dhe-page-modal" class="dhe-modal" style="display: none;">
    <div class="dhe-modal-content">
        <div class="dhe-modal-header">
            <h3 id="dhe-modal-title">Create New Page</h3>
            <span class="dhe-modal-close">&times;</span>
        </div>
        <div class="dhe-modal-body">
            <label for="dhe-page-name">Page Title:</label>
            <input type="text" id="dhe-page-name" placeholder="e.g., Hingham Outdoor Kitchen">
            
            <label for="dhe-page-status" style="margin-top: 15px;">Page Status:</label>
            <select id="dhe-page-status" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
                <option value="draft" selected>Draft (Review before publishing)</option>
                <option value="publish">Publish (Make live immediately)</option>
                <option value="private">Private (Only visible to admins)</option>
            </select>
            
            <p class="dhe-help-text" style="color: #28a745; font-weight: bold; margin-top: 15px;">✅ This will create a new WordPress Page with all your edited content</p>
            <p class="dhe-help-text">The page will appear in your WordPress Pages section</p>
        </div>
        <div class="dhe-modal-footer">
            <button id="dhe-modal-cancel" class="dhe-btn">Cancel</button>
            <button id="dhe-modal-confirm" class="dhe-btn dhe-btn-primary" style="font-weight: 600; padding: 12px 30px;">
                ✅ Create WordPress Page
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Header -->
<header class="header-menu" id="header">
    <div class="header-content">
        <a href="https://deangele.com" class="logo-link" title="Back to DeAngele Main Website">
            <img 
                src="<?php echo esc_url(dhe_get_content('logo_image') ?: 'https://deangele.com/wp-content/uploads/2024/03/DeAngele-Landscape-logo-2.webp'); ?>" 
                alt="DeAngele Landscape & Construction" 
                class="logo-img<?php if ($editing_mode) echo ' dhe-editable-image'; ?>"
                <?php if ($editing_mode) echo 'data-dhe-content-key="logo_image" data-dhe-alt="Company Logo"'; ?>
                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
            >
            <span class="logo-fallback" style="display: none; color: #333; font-weight: 700; font-size: 18px; letter-spacing: 1px;">DEANGELE</span>
        </a>
        <a href="https://deangele.com" class="back-to-site">
            &lt;- Back to Main Site
        </a>
    </div>
</header>

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(0,0,0,0.4)), url('<?php echo esc_url(dhe_get_content('hero_bg_image')); ?>'); background-size: cover; background-position: center;">
    <?php if ($editing_mode): ?>
    <div class="dhe-bg-image-overlay" data-dhe-content-key="hero_bg_image">
        <span>📷 Click to change background image</span>
    </div>
    <?php endif; ?>
    
    <div class="hero-content">
        <h1 class="<?php if ($editing_mode) echo 'dhe-editable-text'; ?>" 
            <?php if ($editing_mode) echo 'data-dhe-content-key="hero_title"'; ?>>
            <?php echo esc_html(dhe_get_content('hero_title')); ?>
        </h1>
        
        <p class="hero-subtitle<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
           <?php if ($editing_mode) echo 'data-dhe-content-key="hero_subtitle"'; ?>>
            <?php echo esc_html(dhe_get_content('hero_subtitle')); ?>
        </p>
        
        <a href="#contact" class="cta-button">
            <span class="<?php if ($editing_mode) echo 'dhe-editable-text'; ?>" 
                  <?php if ($editing_mode) echo 'data-dhe-content-key="hero_cta_text"'; ?>>
                <?php echo esc_html(dhe_get_content('hero_cta_text')); ?>
            </span>
        </a>
    </div>
</section>

<!-- Sticky CTA -->
<a href="#contact" class="sticky-cta">
    <span class="<?php if ($editing_mode) echo 'dhe-editable-text'; ?>" 
          <?php if ($editing_mode) echo 'data-dhe-content-key="sticky_cta_text"'; ?>>
        <?php echo esc_html(dhe_get_content('sticky_cta_text')); ?>
    </span>
</a>

<!-- What We Build Section -->
<section class="what-we-build section">
    <div class="container">
        <h2 class="section-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
            <?php if ($editing_mode) echo 'data-dhe-content-key="what_we_build_title"'; ?>>
            <?php echo esc_html(dhe_get_content('what_we_build_title')); ?>
        </h2>
        
        <div class="features-grid">
            <!-- Feature Card 1 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('feature1_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('feature1_title')); ?>" 
                    class="feature-image<?php if ($editing_mode) echo ' dhe-editable-image'; ?>"
                    <?php if ($editing_mode) echo 'data-dhe-content-key="feature1_image" data-dhe-alt="Feature 1 Image"'; ?>
                >
                <div class="feature-content">
                    <h3 class="feature-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                        <?php if ($editing_mode) echo 'data-dhe-content-key="feature1_title"'; ?>>
                        <?php echo esc_html(dhe_get_content('feature1_title')); ?>
                    </h3>
                    <p class="feature-description<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                       <?php if ($editing_mode) echo 'data-dhe-content-key="feature1_description"'; ?>>
                        <?php echo esc_html(dhe_get_content('feature1_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Feature Card 2 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('feature2_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('feature2_title')); ?>" 
                    class="feature-image<?php if ($editing_mode) echo ' dhe-editable-image'; ?>"
                    <?php if ($editing_mode) echo 'data-dhe-content-key="feature2_image" data-dhe-alt="Feature 2 Image"'; ?>
                >
                <div class="feature-content">
                    <h3 class="feature-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                        <?php if ($editing_mode) echo 'data-dhe-content-key="feature2_title"'; ?>>
                        <?php echo esc_html(dhe_get_content('feature2_title')); ?>
                    </h3>
                    <p class="feature-description<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                       <?php if ($editing_mode) echo 'data-dhe-content-key="feature2_description"'; ?>>
                        <?php echo esc_html(dhe_get_content('feature2_description')); ?>
                    </p>
                </div>
            </div>
            
            <!-- Feature Card 3 -->
            <div class="feature-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content('feature3_image')); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content('feature3_title')); ?>" 
                    class="feature-image<?php if ($editing_mode) echo ' dhe-editable-image'; ?>"
                    <?php if ($editing_mode) echo 'data-dhe-content-key="feature3_image" data-dhe-alt="Feature 3 Image"'; ?>
                >
                <div class="feature-content">
                    <h3 class="feature-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                        <?php if ($editing_mode) echo 'data-dhe-content-key="feature3_title"'; ?>>
                        <?php echo esc_html(dhe_get_content('feature3_title')); ?>
                    </h3>
                    <p class="feature-description<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                       <?php if ($editing_mode) echo 'data-dhe-content-key="feature3_description"'; ?>>
                        <?php echo esc_html(dhe_get_content('feature3_description')); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="cta-section">
            <a href="#contact" class="cta-button">
                <span class="<?php if ($editing_mode) echo 'dhe-editable-text'; ?>" 
                      <?php if ($editing_mode) echo 'data-dhe-content-key="what_we_build_cta"'; ?>>
                    <?php echo esc_html(dhe_get_content('what_we_build_cta')); ?>
                </span>
            </a>
        </div>
    </div>
</section>

<!-- Materials Section -->
<section class="materials section">
    <div class="container">
        <h2 class="section-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
            <?php if ($editing_mode) echo 'data-dhe-content-key="materials_title"'; ?>>
            <?php echo esc_html(dhe_get_content('materials_title')); ?>
        </h2>
        
        <p class="section-subtitle<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
           <?php if ($editing_mode) echo 'data-dhe-content-key="materials_subtitle"'; ?>>
            <?php echo esc_html(dhe_get_content('materials_subtitle')); ?>
        </p>
        
        <div class="materials-grid">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <div class="material-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content("material{$i}_image")); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content("material{$i}_title")); ?>" 
                    class="material-image<?php if ($editing_mode) echo ' dhe-editable-image'; ?>"
                    <?php if ($editing_mode) echo "data-dhe-content-key=\"material{$i}_image\" data-dhe-alt=\"Material {$i} Image\""; ?>
                >
                <h3 class="material-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                    <?php if ($editing_mode) echo "data-dhe-content-key=\"material{$i}_title\""; ?>>
                    <?php echo esc_html(dhe_get_content("material{$i}_title")); ?>
                </h3>
                <p class="material-description<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                   <?php if ($editing_mode) echo "data-dhe-content-key=\"material{$i}_description\""; ?>>
                    <?php echo esc_html(dhe_get_content("material{$i}_description")); ?>
                </p>
            </div>
            <?php endfor; ?>
            
            <!-- Third row - new hardscape materials -->
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="material-card">
                <img 
                    src="<?php echo esc_url(dhe_get_content("hardscape_material{$i}_image")); ?>" 
                    alt="<?php echo esc_attr(dhe_get_content("hardscape_material{$i}_title")); ?>" 
                    class="material-image<?php if ($editing_mode) echo ' dhe-editable-image'; ?>"
                    <?php if ($editing_mode) echo "data-dhe-content-key=\"hardscape_material{$i}_image\" data-dhe-alt=\"Hardscape Material {$i} Image\""; ?>
                >
                <h3 class="material-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                    <?php if ($editing_mode) echo "data-dhe-content-key=\"hardscape_material{$i}_title\""; ?>>
                    <?php echo esc_html(dhe_get_content("hardscape_material{$i}_title")); ?>
                </h3>
                <p class="material-description<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                   <?php if ($editing_mode) echo "data-dhe-content-key=\"hardscape_material{$i}_description\""; ?>>
                    <?php echo esc_html(dhe_get_content("hardscape_material{$i}_description")); ?>
                </p>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- Social Proof Section -->
<section class="social-proof section">
    <div class="container">
        <h2 class="section-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
            <?php if ($editing_mode) echo 'data-dhe-content-key="reviews_title"'; ?>>
            <?php echo esc_html(dhe_get_content('reviews_title')); ?>
        </h2>
        
        <p class="section-subtitle<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
           <?php if ($editing_mode) echo 'data-dhe-content-key="reviews_subtitle"'; ?>>
            <?php echo esc_html(dhe_get_content('reviews_subtitle')); ?>
        </p>
        
        <!-- NEW FEATURE: Social Proof Type Selection -->
        <?php if ($editing_mode): ?>
        <div class="dhe-social-proof-selector">
            <h3 class="dhe-selector-title">Choose Social Proof Type:</h3>
            <div class="dhe-radio-group">
                <label class="dhe-radio-option">
                    <input type="radio" name="social_proof_type" value="google" 
                           <?php echo dhe_get_content('social_proof_type') === 'google' ? 'checked' : ''; ?>>
                    <span>Google Reviews</span>
                </label>
                <label class="dhe-radio-option">
                    <input type="radio" name="social_proof_type" value="youtube" 
                           <?php echo dhe_get_content('social_proof_type') === 'youtube' ? 'checked' : ''; ?>>
                    <span>YouTube Video</span>
                </label>
                <label class="dhe-radio-option">
                    <input type="radio" name="social_proof_type" value="instagram" 
                           <?php echo dhe_get_content('social_proof_type') === 'instagram' ? 'checked' : ''; ?>>
                    <span>Instagram Reviews</span>
                </label>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Social Proof Content - All Sections (show/hide with JavaScript) -->
        <div class="dhe-social-proof-content">
            <!-- Google Reviews Section -->
            <div class="google-reviews-section" style="display: <?php echo dhe_get_content('social_proof_type', 'instagram') === 'google' ? 'block' : 'none'; ?>">
                <?php if ($editing_mode): ?>
                    <div class="dhe-shortcode-editor">
                        <label>WordPress Reviews Shortcode:</label>
                        <input type="text" 
                               class="dhe-shortcode-input" 
                               data-dhe-content-key="google_reviews_shortcode" 
                               value="<?php echo esc_attr(dhe_get_content('google_reviews_shortcode')); ?>"
                               placeholder="[your_reviews_shortcode]">
                        <small>Paste the shortcode from your WordPress reviews plugin (e.g., Google Reviews, WP Google Reviews, etc.)</small>
                        <div class="shortcode-examples">
                            <strong>Common shortcode examples:</strong><br>
                            • <code>[google-reviews-pro]</code><br>
                            • <code>[trustindex no-registration=google]</code><br>
                            • <code>[site_reviews]</code><br>
                            • <code>[wprev_usebtemplate tid="1"]</code>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="reviews-display-container">
                    <?php 
                    $reviews_shortcode = dhe_get_content('google_reviews_shortcode');
                    if ($reviews_shortcode && $reviews_shortcode !== '[your_reviews_shortcode]' && !empty(trim($reviews_shortcode))) {
                        echo do_shortcode($reviews_shortcode);
                    } else {
                        echo '<p class="reviews-placeholder">Google Reviews will appear here when shortcode is added.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- YouTube Video Section -->
            <div class="youtube-video-section" style="display: <?php echo dhe_get_content('social_proof_type', 'instagram') === 'youtube' ? 'block' : 'none'; ?>">
                <!-- Video Title (above video) -->
                <h3 class="youtube-video-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                    <?php if ($editing_mode) echo 'data-dhe-content-key="youtube_video_title"'; ?>>
                    <?php echo esc_html(dhe_get_content('youtube_video_title')); ?>
                </h3>
                
                <?php if ($editing_mode): ?>
                    <div class="dhe-youtube-url-editor">
                        <label>YouTube Video URL:</label>
                        <input type="url" 
                               class="dhe-editable-url" 
                               data-dhe-content-key="youtube_video_url" 
                               value="<?php echo esc_url(dhe_get_content('youtube_video_url')); ?>" 
                               placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                <?php endif; ?>
                
                <?php 
                $youtube_url = dhe_get_content('youtube_video_url');
                if ($youtube_url && $youtube_url !== 'https://www.youtube.com/watch?v=EXAMPLE'): 
                    // Convert YouTube URL to embed format
                    $video_id = '';
                    if (preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $youtube_url, $matches)) {
                        $video_id = $matches[1];
                    }
                    if ($video_id): ?>
                        <div class="youtube-embed">
                            <iframe width="560" height="315" 
                                    src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>?mute=1&controls=1" 
                                    title="YouTube video player" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    <?php else: ?>
                        <p class="youtube-placeholder">Please enter a valid YouTube URL above.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="youtube-placeholder">YouTube video will appear here when URL is set.</p>
                <?php endif; ?>
                
                <!-- Video Description (below video) -->
                <p class="youtube-video-description<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                   <?php if ($editing_mode) echo 'data-dhe-content-key="youtube_video_description"'; ?>>
                    <?php echo esc_html(dhe_get_content('youtube_video_description')); ?>
                </p>
            </div>
            
            <!-- Instagram Reviews Section -->
            <div class="instagram-reviews-section" style="display: <?php echo dhe_get_content('social_proof_type', 'instagram') === 'instagram' ? 'block' : 'none'; ?>">
                <?php if ($editing_mode): ?>
                    <!-- Instagram Reels Editor - Complete Setup -->
                    <div class="instagram-reels-editor">
                        <h4>📱 Instagram Reels - Complete Setup</h4>
                        <div class="reels-editor-grid">
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <div class="reel-editor-card">
                                    <h5>Reel <?php echo $i; ?> Setup</h5>
                                    
                                    <!-- URL Input -->
                                    <label>Instagram URL:</label>
                                    <input type="url" 
                                           class="dhe-instagram-embed-input" 
                                           data-reel-index="<?php echo $i; ?>"
                                           data-dhe-content-key="instagram_reel<?php echo $i; ?>_url" 
                                           value="<?php echo esc_url(dhe_get_content("instagram_reel{$i}_url")); ?>" 
                                           placeholder="https://www.instagram.com/reel/...">
                                    
                                    <!-- Thumbnail Upload -->
                                    <label>Custom Thumbnail:</label>
                                    <div class="thumbnail-upload-area">
                                        <?php $thumbnail = dhe_get_content("instagram_reel{$i}_thumbnail"); ?>
                                        <?php if ($thumbnail): ?>
                                            <img src="<?php echo esc_url($thumbnail); ?>" class="current-thumbnail" alt="Current thumbnail">
                                        <?php endif; ?>
                                        <button type="button" 
                                                class="dhe-upload-thumbnail dhe-btn dhe-btn-secondary" 
                                                data-reel-index="<?php echo $i; ?>"
                                                data-dhe-content-key="instagram_reel<?php echo $i; ?>_thumbnail">
                                            📷 <?php echo $thumbnail ? 'Change' : 'Upload'; ?> Thumbnail
                                        </button>
                                    </div>
                                    
                                    <!-- Title Input -->
                                    <label>Title:</label>
                                    <input type="text" 
                                           class="dhe-instagram-title-input" 
                                           data-dhe-content-key="instagram_reel<?php echo $i; ?>_title" 
                                           value="<?php echo esc_attr(dhe_get_content("instagram_reel{$i}_title")); ?>" 
                                           placeholder="Video title (e.g., Bluestone Patio & Veneer Walls)">
                                    
                                    <!-- Description Input -->
                                    <label>Description:</label>
                                    <textarea class="dhe-instagram-description-input" 
                                              data-dhe-content-key="instagram_reel<?php echo $i; ?>_description" 
                                              placeholder="Video description"><?php echo esc_textarea(dhe_get_content("instagram_reel{$i}_description")); ?></textarea>
                                    
                                    <small>Add Instagram URL, upload custom thumbnail, and set title/description</small>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Instagram Grid Display -->
                <div class="instagram-grid">
                    <?php 
                    $badges = array('RECOMMENDED', 'QUALITY', 'BEST');
                    $fallback_gradients = array(
                        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 
                        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                    );
                    
                    for ($i = 1; $i <= 3; $i++): 
                        $reel_url = dhe_get_content("instagram_reel{$i}_url");
                        $reel_title = dhe_get_content("instagram_reel{$i}_title", "Instagram Reel {$i}");
                        $reel_description = dhe_get_content("instagram_reel{$i}_description", "Customer showcase video");
                        $reel_thumbnail = dhe_get_content("instagram_reel{$i}_thumbnail");
                        $badge = $badges[$i-1];
                        $fallback_bg = $fallback_gradients[$i-1];
                        $has_content = $reel_url && trim($reel_url) !== '';
                    ?>
                        <div class="video-card reel-card-<?php echo $i; ?> <?php echo $has_content ? 'has-content' : 'placeholder-card'; ?>" 
                             <?php if ($has_content): ?>onclick="openInstagramModal('instagram-modal<?php echo $i; ?>')"<?php endif; ?>>
                            <div class="badge"><?php echo esc_html($badge); ?></div>
                            
                            <!-- Video Thumbnail with proper 9:16 aspect ratio -->
                            <div class="video-thumbnail" 
                                 style="<?php echo $reel_thumbnail ? 'background-image: url(' . esc_url($reel_thumbnail) . ');' : 'background: ' . $fallback_bg . ';'; ?>">
                                <?php if ($has_content): ?>
                                    <div class="play-button">
                                        <svg viewBox="0 0 24 24" width="60" height="60">
                                            <path fill="white" d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <div class="placeholder-content">
                                        <div class="placeholder-icon">📱</div>
                                        <span>Add Reel Content</span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Thumbnail overlay for editing -->
                                <?php if ($editing_mode): ?>
                                    <div class="thumbnail-overlay" 
                                         onclick="event.stopPropagation(); openThumbnailUpload(<?php echo $i; ?>);">
                                        <span>📷 Change Thumbnail</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="video-info">
                                <div class="stars">⭐⭐⭐⭐⭐</div>
                                <h3 class="video-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                                    <?php if ($editing_mode) echo "data-dhe-content-key=\"instagram_reel{$i}_title\""; ?>>
                                    <?php echo esc_html($reel_title); ?>
                                </h3>
                                <p class="video-description<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                                   <?php if ($editing_mode) echo "data-dhe-content-key=\"instagram_reel{$i}_description\""; ?>>
                                    <?php echo esc_html($reel_description); ?>
                                </p>
                            </div>
                            <div class="instagram-handle">@deangelelandscape</div>
                        </div>
                    <?php endfor; ?>
                </div>
                
                <div class="instagram-footer">
                    <p class="social-link<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
                       <?php if ($editing_mode) echo 'data-dhe-content-key="instagram_reviews_content"'; ?>>
                        <?php echo wp_kses_post(dhe_get_content('instagram_reviews_content')); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Instagram Embed Modals -->
        <?php for ($i = 1; $i <= 3; $i++): 
            $reel_url = dhe_get_content("instagram_reel{$i}_url");
        ?>
        <div id="instagram-modal<?php echo $i; ?>" class="modal instagram-modal">
            <div class="modal-content">
                <span class="close" onclick="closeInstagramModal('instagram-modal<?php echo $i; ?>')">&times;</span>
                <div class="instagram-embed-container">
                    <?php if ($reel_url && trim($reel_url) !== ''): ?>
                        <blockquote class="instagram-media instagram-embed"
                                    data-instgrm-permalink="<?php echo esc_url($reel_url); ?>?utm_source=ig_embed&utm_campaign=loading"
                                    data-instgrm-version="14">
                            <a href="<?php echo esc_url($reel_url); ?>" target="_blank">View on Instagram</a>
                        </blockquote>
                    <?php else: ?>
                        <p>No Instagram URL set for this reel.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</section>

<!-- Contact Form Section -->
<section class="lead-form-section section" id="contact">
    <div class="form-container">
        <h2 class="section-title<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
            <?php if ($editing_mode) echo 'data-dhe-content-key="contact_title"'; ?>>
            <?php echo esc_html(dhe_get_content('contact_title')); ?>
        </h2>
        
        <p class="section-subtitle<?php if ($editing_mode) echo ' dhe-editable-text'; ?>" 
           <?php if ($editing_mode) echo 'data-dhe-content-key="contact_subtitle"'; ?>>
            <?php echo esc_html(dhe_get_content('contact_subtitle')); ?>
        </p>
        
        <?php if (isset($_GET['contact_sent']) && $_GET['contact_sent'] === '1'): ?>
        <div class="contact-success-message">
            <p>✅ Thank you! Your message has been sent successfully. We'll get back to you soon.</p>
        </div>
        <?php endif; ?>
        
        <form class="lead-form" method="post" action="/form-handler.php">
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
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" id="fullName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="zipCode">Zip Code *</label>
                    <input type="text" id="zipCode" name="zip" required pattern="[0-9]{5}" maxlength="5">
                </div>
            </div>
            
            <div class="form-group">
                <label for="projectType">What are you looking to build? *</label>
                <select id="projectType" name="project" required>
                    <option value="">Select a project type</option>
                    <option value="patio">Patio Installation</option>
                    <option value="walkway">Walkway/Pathway</option>
                    <option value="driveway">Driveway</option>
                    <option value="outdoor-kitchen">Outdoor Kitchen</option>
                    <option value="fire-pit">Fire Pit/Fireplace</option>
                    <option value="retaining-wall">Retaining Wall</option>
                    <option value="multiple">Multiple Projects</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="projectDetails">Project Details (Optional)</label>
                <textarea id="projectDetails" name="details" rows="4" placeholder="Tell us more about your project..."></textarea>
            </div>
            
            <button type="submit" name="hardscape_submit" class="submit-btn">
                <span class="<?php if ($editing_mode) echo 'dhe-editable-text'; ?>" 
                      <?php if ($editing_mode) echo 'data-dhe-content-key="form_submit_text"'; ?>>
                    <?php echo esc_html(dhe_get_content('form_submit_text')); ?>
                </span>
            </button>
        </form>
    </div>
</section>

<!-- Instagram Embed Script -->
<script async src="//www.instagram.com/embed.js"></script>

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

<script>
    // Instagram Modal functionality
    function openInstagramModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Process Instagram embeds after modal opens
        setTimeout(() => {
            if (window.instgrm) {
                window.instgrm.Embeds.process();
            }
        }, 100);
    }

    function closeInstagramModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                closeInstagramModal(modal.id);
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal[style*="block"]');
            if (openModal) {
                closeInstagramModal(openModal.id);
            }
        }
    });

    // Add hover effects for video cards
    document.querySelectorAll('.video-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Initialize Instagram embeds when page loads
    window.addEventListener('load', function() {
        if (window.instgrm) {
            window.instgrm.Embeds.process();
        }
    });
</script>

<?php wp_footer(); ?>
</body>
</html>