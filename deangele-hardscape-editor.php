<?php
/**
 * Plugin Name: DeAngele Hardscape Front-End Editor
 * Plugin URI: https://deangele.com
 * Description: Simple front-end editing for the DeAngele hardscape template. Edit text and images directly on the page without touching the WordPress dashboard.
 * Version: 1.0.0
 * Author: DeAngele Team
 * License: GPL v2 or later
 * Text Domain: deangele-hardscape-editor
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DHE_VERSION', '2.6.0');
define('DHE_PLUGIN_FILE', __FILE__);
define('DHE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DHE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DHE_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main DeAngele Hardscape Editor Class
 * 
 * Phase 1: Core Plugin Structure
 * - Basic plugin initialization following WordPress best practices
 * - Template loading mechanism
 * - Content storage and retrieval system
 * - Authentication and permission checks
 */
class DeAngele_Hardscape_Editor {

    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Content data storage
     */
    private $content_data = array();
    
    /**
     * Current page variant
     */
    private $current_page_variant = 'default';
    
    /**
     * Get plugin instance (singleton pattern)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Initialize plugin
     */
    private function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(DHE_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(DHE_PLUGIN_FILE, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('deangele-hardscape-editor', false, dirname(DHE_PLUGIN_BASENAME) . '/languages');
        
        // Step 3: Check for version updates and force database refresh
        $this->check_version_update();
        
        // Load content data
        $this->load_content_data();
        
        // Initialize hooks
        $this->init_hooks();
        
        // Add rewrite rules for custom page
        $this->add_rewrite_rules();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Template loading
        add_action('template_redirect', array($this, 'handle_template_loading'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        
        // Admin hooks
        if (is_admin()) {
            add_action('admin_notices', array($this, 'admin_notices'));
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'admin_init'));
        }
        
        // Front-end hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        
        // AJAX hooks for content saving
        add_action('wp_ajax_dhe_save_content', array($this, 'ajax_save_content'));
        add_action('wp_ajax_dhe_get_content', array($this, 'ajax_get_content'));
        add_action('wp_ajax_dhe_upload_media', array($this, 'ajax_upload_media'));
        add_action('wp_ajax_dhe_generate_page', array($this, 'ajax_generate_page'));
        add_action('wp_ajax_dhe_force_update_content', array($this, 'ajax_force_update_content'));
        add_action('wp_ajax_dhe_render_shortcode', array($this, 'ajax_render_shortcode'));
        add_action('wp_ajax_dhe_save_as_new_page', array($this, 'ajax_save_as_new_page'));
        add_action('wp_ajax_dhe_duplicate_page', array($this, 'ajax_duplicate_page'));
    }
    
    /**
     * Step 3: Check for version updates and force database refresh
     */
    private function check_version_update() {
        $saved_version = get_option('dhe_plugin_version', '1.0.0');
        $current_version = DHE_VERSION;
        
        // If version changed to 2.6.0+, force update database
        if (version_compare($saved_version, '2.6.0', '<') && version_compare($current_version, '2.6.0', '>=')) {
            $this->force_database_update();
            update_option('dhe_plugin_version', $current_version);
        }
    }
    
    /**
     * Step 4: Force clear cached data and repopulate (preserve Instagram data)
     */
    private function force_database_update() {
        // Get current content to preserve Instagram data
        $current_content = get_option('dhe_content_data', array());
        
        // Preserve Instagram reel data
        $instagram_data = array();
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($current_content["instagram_reel{$i}_thumbnail"])) {
                $instagram_data["instagram_reel{$i}_thumbnail"] = $current_content["instagram_reel{$i}_thumbnail"];
            }
            if (!empty($current_content["instagram_reel{$i}_url"])) {
                $instagram_data["instagram_reel{$i}_url"] = $current_content["instagram_reel{$i}_url"];
            }
        }
        
        // Clear cached content but not completely
        delete_option('dhe_content_data');
        
        // Force reload content data which will repopulate hardscape materials
        $this->load_content_data();
        
        // Restore preserved Instagram data
        if (!empty($instagram_data)) {
            $this->content_data = array_merge($this->content_data, $instagram_data);
            update_option('dhe_content_data', $this->content_data);
        }
        
        // Log the update
        error_log('DHE: Database updated to version ' . DHE_VERSION . ' - hardscape materials populated, Instagram data preserved');
    }
    
    /**
     * Add custom query vars
     */
    public function add_query_vars($vars) {
        $vars[] = 'dhe_page';
        $vars[] = 'dhe_variant';
        return $vars;
    }
    
    /**
     * Add rewrite rules for custom page
     */
    private function add_rewrite_rules() {
        add_rewrite_rule('^deangele-hardscape/?$', 'index.php?dhe_page=hardscape&dhe_variant=default', 'top');
        add_rewrite_rule('^deangele-hardscape/([^/]+)/?$', 'index.php?dhe_page=hardscape&dhe_variant=$matches[1]', 'top');
    }
    
    /**
     * Handle template loading (Phase 1: Template loading mechanism)
     */
    public function handle_template_loading() {
        if (get_query_var('dhe_page') === 'hardscape') {
            // Set current page variant
            $this->current_page_variant = get_query_var('dhe_variant') ?: 'default';
            
            // Load content data for this variant
            $this->load_content_data();
            
            // Check permissions for editing mode
            $editing_mode = $this->is_editing_mode();
            
            // Load the hardscape template
            $this->load_hardscape_template($editing_mode);
            exit;
        }
    }
    
    /**
     * Check if user has editing permissions (Phase 1: Authentication and permission checks)
     */
    private function is_editing_mode() {
        // Only allow editing for users with edit_pages capability
        return is_user_logged_in() && current_user_can('edit_pages');
    }
    
    /**
     * Load content data (Phase 1: Content storage and retrieval system)
     */
    private function load_content_data() {
        // Default content structure based on hardscape-hingham.html analysis
        $default_content = array(
            'page_title' => 'Top Hardscape Materials for Patios, Fire Pits & Retaining Walls',
            'meta_description' => 'Backyard transformations in Massachusetts. Outdoor kitchens, fire pits, retaining walls, and more by DeAngele Landscape. Book your consultation now.',
            'hero_title' => 'Top Hardscape Materials for Patios, Fire Pits & Retaining Walls',
            'hero_subtitle' => 'From outdoor kitchens to fire pits, retaining walls, and more — Explore premium materials like Concord Wall™, granite pavers, and natural gas fire pits — all professionally installed in Hingham MA, by DeAngele Landscape & Construction',
            'hero_cta_text' => '📅 Get a Free Design Consultation',
            'hero_bg_image' => 'https://deangele.com/wp-content/uploads/2025/06/hero-outdoor-transformation-hingham-ma-deangele-landscape.jpg',
            'sticky_cta_text' => '📞 Free Quote',
            'logo_image' => 'https://deangele.com/wp-content/uploads/2024/03/DeAngele-Landscape-logo-2.webp',
            'what_we_build_title' => 'What We Build',
            'what_we_build_cta' => '🛠️ Have a Project? Request a Call Back!',
            'materials_title' => 'Materials Matter',
            'materials_subtitle' => '🧱 Premium Products Backed by Expert Installation',
            'reviews_title' => '📱 Real Customer Reviews',
            'reviews_subtitle' => 'See what our satisfied customers have to say about their outdoor living transformations',
            'contact_title' => 'Ready to Transform Your Outdoor Space?',
            'contact_subtitle' => 'Get your free, no-obligation estimate today!',
            'form_submit_text' => 'Get My Free Estimate',
            // NEW FEATURE: Social proof options
            'social_proof_type' => 'instagram', // Options: 'google', 'youtube', 'instagram'
            'google_reviews_shortcode' => '[your_reviews_shortcode]',
            'youtube_video_url' => 'https://www.youtube.com/watch?v=EXAMPLE',
            'youtube_video_title' => 'Customer Testimonial Video',
            'youtube_video_description' => 'Watch our satisfied customer share their experience with DeAngele Landscape & Construction.',
            'instagram_reviews_content' => 'Visit our Instagram <a href="https://instagram.com/deangelelandscape" target="_blank">@deangelelandscape</a> to see more customer reviews and project showcases!',
            // Instagram Reels data - using embed method with custom thumbnails
            'instagram_reel1_url' => '',
            'instagram_reel1_title' => 'Bluestone Patio & Veneer Walls',
            'instagram_reel1_description' => 'Customer showcase of completed bluestone patio installation',
            'instagram_reel1_thumbnail' => '',
            'instagram_reel2_url' => '',
            'instagram_reel2_title' => 'Outdoor Living Space',
            'instagram_reel2_description' => 'Complete outdoor kitchen and entertainment area transformation',
            'instagram_reel2_thumbnail' => '',
            'instagram_reel3_url' => '',
            'instagram_reel3_title' => 'Lawn & Fence Installation', 
            'instagram_reel3_description' => 'Professional lawn and fencing project completion review',
            'instagram_reel3_thumbnail' => '',
            // Feature cards
            'feature1_title' => 'Dream Backyard Patio',
            'feature1_description' => 'Backyard patio with white pergola, gazebo, and central fire pit creating the perfect entertaining space.',
            'feature1_image' => 'https://deangele.com/wp-content/uploads/2025/06/hero-amazing-outdoor-kitchen-hingham-ma-deangele-landscape-2.jpg',
            'feature2_title' => 'Full Outdoor Kitchen',
            'feature2_description' => 'Spacious covered outdoor kitchen with built-in gas grill, counter space, and storage solutions.',
            'feature2_image' => 'https://deangele.com/wp-content/uploads/2025/06/detail-outdoor-kitchen-design-hingham-ma-deangele-landscape.jpg',
            'feature3_title' => 'Round Stone Fire Pit',
            'feature3_description' => 'Round gray stone fire pit with white Adirondack chairs creating a cozy gathering space.',
            'feature3_image' => 'https://deangele.com/wp-content/uploads/2025/06/detail-outdoor-firepit-installation-hingham-ma-deangele-landscape.jpg',
            // Material cards (first 6 for now)
            'material1_title' => 'Concord Wall™ Segmental Retaining Wall',
            'material1_description' => 'Add structure and style with granite-toned segmental retaining walls.',
            'material1_image' => 'https://deangele.com/wp-content/uploads/2025/06/Concord-Wall™-Segmental-Retaining-Wall-installation-by-deangele-landscape-and-construction-hingham.jpg',
            'material2_title' => 'Granite Retaining Block Detail',
            'material2_description' => 'Engineered for support, slope control, and clean visual lines in backyard installations.',
            'material2_image' => 'https://deangele.com/wp-content/uploads/2025/06/Granite-Retaining-Block-Detail-installation-by-deangele-landscape-and-construction-in-hingham.jpg',
            'material3_title' => 'CMU Block Framing',
            'material3_description' => 'Durable, load-bearing construction for long-lasting outdoor kitchens and fireplaces.',
            'material3_image' => 'https://deangele.com/wp-content/uploads/2025/06/CMU-Block-Framing-by-deangele-landscape-installed-in-hingham-massachusetts.jpg',
            'material4_title' => 'Natural Gas Fire Pit',
            'material4_description' => 'Mess-free, stylish fire pit built from stone — perfect for year-round use.',
            'material4_image' => 'https://deangele.com/wp-content/uploads/2025/06/Natural-Gas-Fire-Pit-installation-in-hingham-ma-by-deangele-landscape-top-landscape-company.jpg',
            'material5_title' => 'Fireplace with Veneer Mosaic',
            'material5_description' => 'Focal point for comfort and style, built with Desert Creek veneer and premium bluestone coping.',
            'material5_image' => 'https://deangele.com/wp-content/uploads/2025/06/Stone-Fireplace-with-Bluestone-Cap-Installation-By-Hardscape-Pros-in-Hingham-DeAngele-Solutions-in-Outdoor-Design.jpg',
            'material6_title' => 'Bluestone Coping & Capstone',
            'material6_description' => 'Polished, smooth finishing touches for steps, walls, and fire features.',
            'material6_image' => 'https://deangele.com/wp-content/uploads/2025/06/Bluestone-Coping-Capstone-installation-outdoor-builders-in-Hingham-MA-Deangele-top-landscape-contractors.jpg',
            
            // NEW Third row hardscape materials - fresh database entries
            'hardscape_material1_title' => 'Natural Stone Veneer',
            'hardscape_material1_description' => 'Desert Creek stone veneer creates stunning focal points for outdoor fireplaces and accent walls.',
            'hardscape_material1_image' => 'https://deangele.com/wp-content/uploads/2024/05/natural-stone-veneer-fireplace-hingham-ma.jpg',
            'hardscape_material2_title' => 'Granite Countertop Material', 
            'hardscape_material2_description' => 'Premium granite surfaces for outdoor kitchen islands and bar tops, engineered for weather resistance.',
            'hardscape_material2_image' => 'https://deangele.com/wp-content/uploads/2024/05/granite-outdoor-kitchen-countertop-hingham.jpg',
            'hardscape_material3_title' => 'Stacked Stone Base',
            'hardscape_material3_description' => 'Durable stacked stone construction for outdoor kitchen islands and structural elements.',
            'hardscape_material3_image' => 'https://deangele.com/wp-content/uploads/2024/05/stacked-stone-outdoor-kitchen-base-hingham.jpg'
        );
        
        // Load saved content from WordPress options - support page variants
        $option_key = 'dhe_content_data_' . $this->current_page_variant;
        $saved_content = get_option($option_key, array());
        
        // If this is a new variant, use default content as fallback
        if (empty($saved_content) && $this->current_page_variant !== 'default') {
            $saved_content = get_option('dhe_content_data_default', array());
        }
        
        // If still empty, use the legacy option key for backward compatibility
        if (empty($saved_content)) {
            $saved_content = get_option('dhe_content_data', array());
        }
        
        // Merge with defaults to ensure all keys exist
        $this->content_data = array_merge($default_content, $saved_content);
        
        // Force ensure hardscape materials exist (Step 2: Fix Content Loading)
        $hardscape_materials = array(
            'hardscape_material1_title' => 'Natural Stone Veneer',
            'hardscape_material1_description' => 'Desert Creek stone veneer creates stunning focal points for outdoor fireplaces and accent walls.',
            'hardscape_material1_image' => 'https://deangele.com/wp-content/uploads/2024/05/natural-stone-veneer-fireplace-hingham-ma.jpg',
            'hardscape_material2_title' => 'Granite Countertop Material', 
            'hardscape_material2_description' => 'Premium granite surfaces for outdoor kitchen islands and bar tops, engineered for weather resistance.',
            'hardscape_material2_image' => 'https://deangele.com/wp-content/uploads/2024/05/granite-outdoor-kitchen-countertop-hingham.jpg',
            'hardscape_material3_title' => 'Stacked Stone Base',
            'hardscape_material3_description' => 'Durable stacked stone construction for outdoor kitchen islands and structural elements.',
            'hardscape_material3_image' => 'https://deangele.com/wp-content/uploads/2024/05/stacked-stone-outdoor-kitchen-base-hingham.jpg'
        );
        
        // Force populate hardscape materials on every load
        foreach ($hardscape_materials as $key => $value) {
            if (empty($this->content_data[$key])) {
                $this->content_data[$key] = $value;
            }
        }
        
        // Ensure Instagram reel structure exists with defaults
        $instagram_defaults = array(
            'instagram_reel1_title' => 'Bluestone Patio & Veneer Walls',
            'instagram_reel1_description' => 'Customer showcase of completed bluestone patio installation',
            'instagram_reel2_title' => 'Outdoor Living Space',
            'instagram_reel2_description' => 'Complete outdoor kitchen and entertainment area transformation',
            'instagram_reel3_title' => 'Lawn & Fence Installation',
            'instagram_reel3_description' => 'Professional lawn and fencing project completion review'
        );
        
        foreach ($instagram_defaults as $key => $value) {
            if (empty($this->content_data[$key])) {
                $this->content_data[$key] = $value;
            }
        }
        
        // Save back to ensure persistence (Step 3: Database Update)
        $option_key = 'dhe_content_data_' . $this->current_page_variant;
        update_option($option_key, $this->content_data);
        
        // Also save to legacy key for backward compatibility if this is default variant
        if ($this->current_page_variant === 'default') {
            update_option('dhe_content_data', $this->content_data);
        }
        
    }
    
    /**
     * Get content value by key
     */
    public function get_content($key, $default = '') {
        return isset($this->content_data[$key]) ? $this->content_data[$key] : $default;
    }
    
    /**
     * Load the hardscape template (Phase 1: Template loading mechanism)
     */
    private function load_hardscape_template($editing_mode = false) {
        // Include the template file
        include DHE_PLUGIN_DIR . 'templates/hardscape-template.php';
    }
    
    /**
     * Enqueue front-end scripts and styles
     */
    public function enqueue_frontend_scripts() {
        // Only load on our custom page
        if (get_query_var('dhe_page') === 'hardscape') {
            // Always load the base template styles
            wp_enqueue_style('dhe-template', DHE_PLUGIN_URL . 'assets/css/template.css', array(), DHE_VERSION);
            
            // Load editing scripts only for users with permissions
            if ($this->is_editing_mode()) {
                wp_enqueue_script('dhe-editor', DHE_PLUGIN_URL . 'assets/js/editor.js', array('jquery'), DHE_VERSION, true);
                wp_enqueue_style('dhe-editor', DHE_PLUGIN_URL . 'assets/css/editor.css', array(), DHE_VERSION);
                
                // Localize script with AJAX data
                wp_localize_script('dhe-editor', 'dhe_ajax', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('dhe_editor_nonce'),
                    'editing_mode' => true,
                    'plugin_url' => DHE_PLUGIN_URL,
                    'current_variant' => $this->current_page_variant,
                    'current_url' => home_url('/deangele-hardscape' . ($this->current_page_variant !== 'default' ? '/' . $this->current_page_variant : ''))
                ));
                
                // Enqueue WordPress media library
                wp_enqueue_media();
            }
        }
    }
    
    /**
     * AJAX: Save content
     */
    public function ajax_save_content() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('edit_pages')) {
            wp_send_json_error('Permission denied');
            return;
        }
        
        $content_key = sanitize_text_field($_POST['content_key']);
        
        // Handle shortcodes with minimal sanitization
        if ($content_key === 'google_reviews_shortcode') {
            $content_value = sanitize_text_field($_POST['content_value']);
        } else {
            $content_value = wp_kses_post($_POST['content_value']);
        }
        
        // Get variant from POST data or use default
        $variant = sanitize_text_field($_POST['variant'] ?? 'default');
        
        // Load content data for this variant
        $option_key = 'dhe_content_data_' . $variant;
        $content_data = get_option($option_key, array());
        
        // Update content data
        $content_data[$content_key] = $content_value;
        update_option($option_key, $content_data);
        
        // Also save to legacy key if this is default variant
        if ($variant === 'default') {
            update_option('dhe_content_data', $content_data);
        }
        
        wp_send_json_success(array(
            'message' => 'Content saved successfully',
            'content_key' => $content_key,
            'content_value' => $content_value
        ));
    }
    
    /**
     * AJAX: Get content
     */
    public function ajax_get_content() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('edit_pages')) {
            wp_send_json_error('Permission denied');
            return;
        }
        
        wp_send_json_success($this->content_data);
    }
    
    /**
     * AJAX: Handle media upload
     */
    public function ajax_upload_media() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('upload_files')) {
            wp_send_json_error('Permission denied');
            return;
        }
        
        $attachment_id = intval($_POST['attachment_id']);
        $content_key = sanitize_text_field($_POST['content_key']);
        
        if ($attachment_id) {
            $image_url = wp_get_attachment_url($attachment_id);
            
            // Update content data
            $this->content_data[$content_key] = $image_url;
            update_option('dhe_content_data', $this->content_data);
            
            wp_send_json_success(array(
                'message' => 'Image updated successfully',
                'content_key' => $content_key,
                'image_url' => $image_url
            ));
        } else {
            wp_send_json_error('Invalid attachment');
        }
    }
    
    /**
     * AJAX: Generate new WordPress page
     */
    public function ajax_generate_page() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('edit_pages')) {
            wp_send_json_error('Permission denied');
            return;
        }
        
        $page_title = sanitize_text_field($_POST['page_title']);
        if (empty($page_title)) {
            $page_title = 'Hardscape Page - ' . date('Y-m-d H:i:s');
        }
        
        // Generate static HTML content from current data
        $html_content = $this->generate_static_html();
        
        // Create new WordPress page
        $page_data = array(
            'post_title'    => $page_title,
            'post_content'  => $html_content,
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => get_current_user_id(),
            'meta_input'    => array(
                'dhe_generated_page' => true,
                'dhe_generated_date' => current_time('mysql'),
                'dhe_source_data' => $this->content_data
            )
        );
        
        $page_id = wp_insert_post($page_data);
        
        if (is_wp_error($page_id)) {
            wp_send_json_error('Failed to create page: ' . $page_id->get_error_message());
            return;
        }
        
        $page_url = get_permalink($page_id);
        
        wp_send_json_success(array(
            'message' => 'Page generated successfully!',
            'page_id' => $page_id,
            'page_url' => $page_url,
            'page_title' => $page_title
        ));
    }
    
    /**
     * Generate static HTML content from current template and data
     */
    private function generate_static_html() {
        // Start output buffering
        ob_start();
        
        // Set editing mode to false for static generation
        $editing_mode = false;
        
        // Include the template
        include DHE_PLUGIN_DIR . 'templates/hardscape-template.php';
        
        // Get the generated content
        $html_content = ob_get_clean();
        
        // Clean up the HTML - remove editing elements and scripts
        $html_content = $this->clean_html_for_static_page($html_content);
        
        return $html_content;
    }
    
    /**
     * Generate static HTML content with specific data
     */
    private function generate_static_html_with_data($content_data) {
        // Temporarily override the content data
        $original_content = $this->content_data;
        $this->content_data = $content_data;
        
        // Generate the HTML
        $html = $this->generate_static_html();
        
        // Inject styles into the HTML for standalone pages
        $html = $this->inject_styles_into_html($html);
        
        // Restore original content data
        $this->content_data = $original_content;
        
        return $html;
    }
    
    /**
     * Set featured image for a page
     */
    private function set_page_featured_image($page_id, $image_url) {
        if (empty($image_url)) {
            return false;
        }
        
        // Check if it's an internal WordPress media URL
        $upload_dir = wp_upload_dir();
        if (strpos($image_url, $upload_dir['baseurl']) === 0) {
            // It's an internal image, try to find the attachment ID
            global $wpdb;
            $attachment_id = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM $wpdb->posts WHERE guid = %s AND post_type = 'attachment'",
                $image_url
            ));
            
            if ($attachment_id) {
                set_post_thumbnail($page_id, $attachment_id);
                return true;
            }
        }
        
        // If we couldn't find the attachment ID by URL, try another method
        // This works for images that have been moved or have different URLs
        $attachment_id = attachment_url_to_postid($image_url);
        if ($attachment_id) {
            set_post_thumbnail($page_id, $attachment_id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Clean HTML content for static page generation
     */
    private function clean_html_for_static_page($html) {
        // Remove editing toolbar completely
        $html = preg_replace('/<div id="dhe-editing-toolbar">.*?<\/div>\s*<\/div>/s', '', $html);
        
        // Remove editing-specific classes but preserve other classes
        $html = preg_replace_callback('/class="([^"]*)"/', function($matches) {
            $classes = explode(' ', $matches[1]);
            $filtered = array_filter($classes, function($class) {
                // Remove only editing-related classes
                return !preg_match('/^dhe-(editable|editing|changed|hover|preview|bg-image-overlay)/', $class);
            });
            $cleaned = implode(' ', $filtered);
            return !empty($cleaned) ? 'class="' . $cleaned . '"' : '';
        }, $html);
        
        // Remove data-dhe attributes
        $html = preg_replace('/\s*data-dhe-[^=]*="[^"]*"/', '', $html);
        $html = preg_replace('/\s*data-dhe-editing="[^"]*"/', '', $html);
        
        // Remove background image overlay divs (editing UI elements)
        $html = preg_replace('/<div class="dhe-bg-image-overlay"[^>]*>.*?<\/div>/s', '', $html);
        
        // Remove only editor CSS/JS, not template CSS
        $html = preg_replace('/<link[^>]*dhe-editor\.css[^>]*>/', '', $html);
        $html = preg_replace('/<script[^>]*dhe[^>]*\.js[^>]*>.*?<\/script>/s', '', $html);
        
        // Remove WordPress admin bar elements
        $html = preg_replace('/<link[^>]*admin-bar[^>]*>/', '', $html);
        $html = preg_replace('/<style[^>]*admin-bar[^>]*>.*?<\/style>/s', '', $html);
        
        // Clean up notifications container
        $html = preg_replace('/<div id="dhe-notifications-container"[^>]*>.*?<\/div>/s', '', $html);
        
        // Clean up modals
        $html = preg_replace('/<div[^>]*class="[^"]*dhe-modal[^"]*"[^>]*>.*?<\/div>\s*<\/div>/s', '', $html);
        
        // Clean up multiple spaces and empty lines
        $html = preg_replace('/\n\s*\n/', "\n", $html);
        $html = preg_replace('/  +/', ' ', $html);
        
        return $html;
    }
    
    /**
     * Generate clean template-based clone HTML
     */
    private function generate_template_clone_html($content_data) {
        // Start clean template generation
        ob_start();
        
        // Temporarily set the content data and disable editing mode
        $original_content = $this->content_data;
        $this->content_data = array_merge($this->content_data, $content_data);
        
        // Temporarily disable WordPress hooks and set non-editing mode
        remove_action('wp_head', 'wp_print_styles', 8);
        remove_action('wp_head', 'wp_print_head_scripts', 9);
        remove_action('wp_footer', 'wp_print_footer_scripts', 20);
        
        // Override editing mode for clean template generation
        global $editing_mode;
        $original_editing_mode = $editing_mode ?? null;
        $editing_mode = false;
        
        // Generate template HTML
        include DHE_PLUGIN_DIR . 'templates/hardscape-template.php';
        
        // Get generated content
        $template_html = ob_get_clean();
        
        // Restore original state
        $this->content_data = $original_content;
        $editing_mode = $original_editing_mode;
        
        // Clean the HTML and inject styles
        $clean_html = $this->clean_template_for_standalone_page($template_html);
        $clean_html = $this->inject_styles_into_html($clean_html);
        
        return $clean_html;
    }
    
    /**
     * Clean template HTML for standalone pages (more targeted than DOM cleaning)
     */
    private function clean_template_for_standalone_page($html) {
        // Remove WordPress hooks and admin elements
        $html = preg_replace('/<\?php wp_head\(\); \?>/s', '', $html);
        $html = preg_replace('/<\?php wp_footer\(\); \?>/s', '', $html);
        
        // Remove editing toolbar and controls
        $html = preg_replace('/<div id="dhe-editing-toolbar">.*?<\/div>\s*<\/div>/s', '', $html);
        $html = preg_replace('/<div id="dhe-notifications-container"[^>]*>.*?<\/div>/s', '', $html);
        $html = preg_replace('/<div id="dhe-page-modal"[^>]*>.*?<\/div>\s*<\/div>/s', '', $html);
        $html = preg_replace('/<div id="instagram-reel-modal"[^>]*>.*?<\/div>\s*<\/div>/s', '', $html);
        
        // Remove editing attributes and classes
        $html = preg_replace('/\s*data-dhe-[^=]*="[^"]*"/s', '', $html);
        $html = preg_replace('/\s*dhe-editable-[^"]*"/', '"', $html);
        $html = preg_replace('/\s*dhe-[^"]*"/', '"', $html);
        $html = preg_replace('/class="\s*"/', '', $html);
        
        // Clean up social proof editor sections (keep only the content)
        $html = preg_replace('/<div class="dhe-social-proof-selector">.*?<\/div>/s', '', $html);
        $html = preg_replace('/<div class="dhe-shortcode-editor">.*?<\/div>/s', '', $html);
        $html = preg_replace('/<div class="instagram-reels-editor">.*?<\/div>/s', '', $html);
        
        // Remove empty lines and clean up formatting
        $html = preg_replace('/\n\s*\n/', "\n", $html);
        $html = preg_replace('/\s{2,}/', ' ', $html);
        
        return $html;
    }
    
    /**
     * Inject styles into HTML for standalone pages
     */
    private function inject_styles_into_html($html) {
        // Read the template CSS file
        $template_css_path = DHE_PLUGIN_DIR . 'assets/css/template.css';
        $template_css = '';
        
        if (file_exists($template_css_path)) {
            $template_css = file_get_contents($template_css_path);
            
            // Convert relative URLs to absolute URLs in CSS
            $plugin_url = DHE_PLUGIN_URL;
            $template_css = str_replace('../images/', $plugin_url . 'assets/images/', $template_css);
            $template_css = str_replace('../fonts/', $plugin_url . 'assets/fonts/', $template_css);
        }
        
        // Create comprehensive style block
        $style_block = "\n<!-- DeAngele Landing Page Styles -->\n";
        
        // Add viewport meta tag if not present
        if (strpos($html, 'viewport') === false) {
            $viewport_meta = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            if (strpos($html, '<head>') !== false) {
                $html = str_replace('<head>', '<head>' . "\n" . $viewport_meta, $html);
            }
        }
        
        // Add external dependencies
        $external_styles = '';
        
        // Google Fonts - check both CSS and HTML for font references
        $fonts_to_load = array();
        
        if (strpos($template_css, 'Poppins') !== false || strpos($html, 'Poppins') !== false) {
            $fonts_to_load[] = 'Poppins:wght@300;400;500;600;700';
        }
        
        if (strpos($template_css, 'Segoe UI') === false) {
            // Segoe UI is a system font, but we can add a web font fallback
            $fonts_to_load[] = 'Open+Sans:wght@300;400;500;600;700';
        }
        
        if (!empty($fonts_to_load)) {
            $external_styles .= '<link href="https://fonts.googleapis.com/css2?' . implode('&family=', $fonts_to_load) . '&display=swap" rel="stylesheet">' . "\n";
        }
        
        // Add the inline styles
        $style_block .= "<style type=\"text/css\">\n";
        
        // Add a CSS reset if not already in template.css
        if (strpos($template_css, 'box-sizing') === false) {
            $style_block .= "/* CSS Reset */\n";
            $style_block .= "*, *::before, *::after { box-sizing: border-box; }\n";
            $style_block .= "body { margin: 0; padding: 0; }\n\n";
        }
        
        // Add the template CSS
        $style_block .= $template_css;
        
        // Add any missing critical styles for WordPress themes
        $style_block .= "\n/* WordPress Compatibility Styles */\n";
        $style_block .= ".alignleft { float: left; margin-right: 1.5em; }\n";
        $style_block .= ".alignright { float: right; margin-left: 1.5em; }\n";
        $style_block .= ".aligncenter { clear: both; display: block; margin-left: auto; margin-right: auto; }\n";
        $style_block .= "img { max-width: 100%; height: auto; }\n";
        
        $style_block .= "\n</style>\n";
        
        // Inject styles before closing </head> tag
        if (strpos($html, '</head>') !== false) {
            $html = str_replace('</head>', $external_styles . $style_block . '</head>', $html);
        } else {
            // If no head tag, add one
            $head_content = '<head>' . "\n";
            $head_content .= '<meta charset="utf-8">' . "\n";
            $head_content .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
            $head_content .= $external_styles;
            $head_content .= $style_block;
            $head_content .= '</head>' . "\n";
            
            // Insert after opening HTML tag
            if (strpos($html, '<html') !== false) {
                $html = preg_replace('/(<html[^>]*>)/i', '$1' . "\n" . $head_content, $html);
            } else {
                // Add HTML structure if missing
                $html = '<!DOCTYPE html><html lang="en">' . "\n" . $head_content . '<body>' . $html . '</body></html>';
            }
        }
        
        return $html;
    }
    
    /**
     * AJAX: Force update content with defaults (enhanced fix for materials issue)
     */
    public function ajax_force_update_content() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('edit_pages')) {
            wp_send_json_error('Permission denied');
            return;
        }
        
        // Get current content
        $current_content = get_option('dhe_content_data', array());
        
        // Force reload default content structure
        $this->load_content_data();
        $default_content = $this->content_data;
        
        // Merge: Keep user content but ensure all default keys exist
        $merged_content = array_merge($default_content, $current_content);
        
        // Force initialize new hardscape materials (third row) in version 2.3.0+
        $new_hardscape_materials = array(
            'hardscape_material1_title' => 'Natural Stone Veneer',
            'hardscape_material1_description' => 'Desert Creek stone veneer creates stunning focal points for outdoor fireplaces and accent walls.',
            'hardscape_material1_image' => 'https://deangele.com/wp-content/uploads/2024/05/natural-stone-veneer-fireplace-hingham-ma.jpg',
            'hardscape_material2_title' => 'Granite Countertop Material', 
            'hardscape_material2_description' => 'Premium granite surfaces for outdoor kitchen islands and bar tops, engineered for weather resistance.',
            'hardscape_material2_image' => 'https://deangele.com/wp-content/uploads/2024/05/granite-outdoor-kitchen-countertop-hingham.jpg',
            'hardscape_material3_title' => 'Stacked Stone Base',
            'hardscape_material3_description' => 'Durable stacked stone construction for outdoor kitchen islands and structural elements.',
            'hardscape_material3_image' => 'https://deangele.com/wp-content/uploads/2024/05/stacked-stone-outdoor-kitchen-base-hingham.jpg'
        );
        
        // Remove old material7-9 entries if they exist
        unset($merged_content['material7_title'], $merged_content['material7_description'], $merged_content['material7_image']);
        unset($merged_content['material8_title'], $merged_content['material8_description'], $merged_content['material8_image']);
        unset($merged_content['material9_title'], $merged_content['material9_description'], $merged_content['material9_image']);
        
        // Force set new hardscape materials content
        foreach ($new_hardscape_materials as $key => $value) {
            $merged_content[$key] = $value;
        }
        
        // Ensure Instagram reel structure is correct with new thumbnail system
        for ($i = 1; $i <= 3; $i++) {
            // Ensure we have the new thumbnail structure
            if (!isset($merged_content["instagram_reel{$i}_thumbnail"])) {
                $merged_content["instagram_reel{$i}_thumbnail"] = '';
            }
            if (!isset($merged_content["instagram_reel{$i}_title"])) {
                $merged_content["instagram_reel{$i}_title"] = $default_content["instagram_reel{$i}_title"];
            }
            if (!isset($merged_content["instagram_reel{$i}_description"])) {
                $merged_content["instagram_reel{$i}_description"] = $default_content["instagram_reel{$i}_description"];
            }
            
            // Remove any old auto-thumbnail keys that are no longer used
            if (isset($merged_content["instagram_reel{$i}_auto_thumbnail"])) {
                unset($merged_content["instagram_reel{$i}_auto_thumbnail"]);
            }
        }
        
        // Update the merged content
        $this->content_data = $merged_content;
        update_option('dhe_content_data', $merged_content);
        
        wp_send_json_success(array(
            'message' => 'Content structure rebuilt! Materials 1-6 + new hardscape materials 1-3 available.',
            'content_count' => count($merged_content),
            'materials_fixed' => 'Third row rebuilt with hardscape_material1-3 structure',
            'old_materials_removed' => 'Old material7-9 entries cleaned from database'
        ));
    }
    
    /**
     * AJAX: Render shortcode
     */
    public function ajax_render_shortcode() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('edit_pages')) {
            wp_send_json_error('Permission denied');
            return;
        }
        
        $shortcode = sanitize_text_field($_POST['shortcode']);
        
        if (empty($shortcode)) {
            wp_send_json_error('Empty shortcode');
            return;
        }
        
        // Validate shortcode format
        if (!preg_match('/^\[[\w\-_]+[^\]]*\]$/', $shortcode)) {
            wp_send_json_error('Invalid shortcode format');
            return;
        }
        
        // Render the shortcode
        $rendered_content = do_shortcode($shortcode);
        
        // Check if shortcode was actually processed
        if ($rendered_content === $shortcode) {
            wp_send_json_error('Shortcode not recognized - make sure the plugin is installed and active');
            return;
        }
        
        wp_send_json_success($rendered_content);
    }
    
    
    
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Add rewrite rules
        $this->add_rewrite_rules();
        flush_rewrite_rules();
        
        // Initialize default content
        $this->load_content_data();
        
        // Always update to ensure we have the latest default content including materials 7-9
        $existing_content = get_option('dhe_content_data', array());
        $merged_content = array_merge($this->content_data, $existing_content);
        update_option('dhe_content_data', $merged_content);
        
        // Also save as default variant for new multi-page system
        update_option('dhe_content_data_default', $merged_content);
        
        // Set activation flag
        update_option('dhe_activated', current_time('timestamp'));
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clean up activation flag
        delete_option('dhe_activated');
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'DeAngele Hardscape Settings',
            'DeAngele Hardscape',
            'manage_options',
            'deangele-hardscape-settings',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Initialize admin settings
     */
    public function admin_init() {
        // Instagram API settings removed - using standard embeds only
    }
    
    /**
     * Admin page HTML
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>DeAngele Hardscape Settings</h1>
            
            
            <div class="notice notice-info">
                <p><strong>Instagram Embeds:</strong> You can embed Instagram posts by pasting Instagram URLs directly in the editor. No API setup required.</p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Settings callbacks
     */
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        if (get_option('dhe_activated')) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>DeAngele Hardscape Editor:</strong> Plugin activated! Visit <a href="<?php echo home_url('/deangele-hardscape'); ?>" target="_blank"><?php echo home_url('/deangele-hardscape'); ?></a> to see your hardscape page.</p>
            </div>
            <?php
            delete_option('dhe_activated');
        }
        
    }
    
    
    /**
     * AJAX: Save as new page - Creates an actual WordPress page with complete visual state
     */
    public function ajax_save_as_new_page() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('publish_pages')) {
            wp_send_json_error('Permission denied - you need publish_pages capability');
            return;
        }
        
        // Get the page title from variant name or payload
        $page_title = sanitize_text_field($_POST['page_title'] ?? '');
        $variant_name = sanitize_title($_POST['variant_name'] ?? '');
        $source_variant = sanitize_text_field($_POST['source_variant'] ?? 'default');
        $status = sanitize_text_field($_POST['status'] ?? 'draft');
        
        // NEW: Get complete visual state from frontend
        $visual_html = wp_unslash($_POST['visual_html'] ?? '');
        $computed_styles = wp_unslash($_POST['computed_styles'] ?? '');
        
        // Validate status
        if (!in_array($status, array('draft', 'publish', 'private'))) {
            $status = 'draft';
        }
        
        // Get the current edited content data
        $content_data = array();
        
        // If payload contains edited content, use that
        if (!empty($_POST['content']) && is_array($_POST['content'])) {
            foreach ($_POST['content'] as $key => $value) {
                $content_data[sanitize_text_field($key)] = wp_kses_post($value);
            }
        } else {
            // Fallback to getting content from the variant
            $source_option_key = 'dhe_content_data_' . $source_variant;
            $content_data = get_option($source_option_key, $this->content_data);
        }
        
        // If no page title provided, use the page_title from content or generate one
        if (empty($page_title)) {
            if (!empty($variant_name)) {
                $page_title = ucwords(str_replace('-', ' ', $variant_name));
            } elseif (!empty($content_data['page_title'])) {
                $page_title = $content_data['page_title'];
            } else {
                $page_title = 'Landing Page - ' . current_time('mysql');
            }
        }
        
        // ENHANCED: Use clean template-based cloning instead of DOM capture
        $html_content = $this->generate_template_clone_html($content_data);
        
        // Create the WordPress page
        $page_args = array(
            'post_title'    => $page_title,
            'post_content'  => $html_content,
            'post_status'   => $status,
            'post_type'     => 'page',
            'post_author'   => get_current_user_id(),
            'meta_input'    => array(
                'dlpb_generated_page' => true,
                'dlpb_generated_date' => current_time('mysql'),
                'dlpb_source_variant' => $source_variant,
                'dlpb_content_data'   => $content_data,
                'dlpb_social_proof_type' => $content_data['social_proof_type'] ?? 'instagram',
                'dlpb_visual_clone' => !empty($visual_html), // Track if this is a visual clone
            )
        );
        
        // Add meta description if available
        if (!empty($content_data['meta_description'])) {
            // Support for Yoast SEO
            $page_args['meta_input']['_yoast_wpseo_metadesc'] = $content_data['meta_description'];
            // Support for All in One SEO
            $page_args['meta_input']['_aioseo_description'] = $content_data['meta_description'];
            // Our own meta field
            $page_args['meta_input']['dlpb_meta_description'] = $content_data['meta_description'];
        }
        
        // Insert the page
        $page_id = wp_insert_post($page_args);
        
        if (is_wp_error($page_id)) {
            wp_send_json_error('Failed to create page: ' . $page_id->get_error_message());
            return;
        }
        
        // Set featured image if hero background image exists
        if (!empty($content_data['hero_bg_image'])) {
            $this->set_page_featured_image($page_id, $content_data['hero_bg_image']);
        }
        
        // Get the URLs for the created page
        $view_url = get_permalink($page_id);
        $edit_url = admin_url('post.php?post=' . $page_id . '&action=edit');
        
        // Return success with page details
        wp_send_json_success(array(
            'message' => 'WordPress page created successfully!',
            'page_id' => $page_id,
            'view_url' => $view_url,
            'edit_url' => $edit_url,
            'admin_url' => $edit_url,
            'page_title' => $page_title,
            'status' => $status,
            'variant_name' => $variant_name,
            'visual_clone' => !empty($visual_html)
        ));
    }
    
    /**
     * AJAX: Duplicate current page
     */
    public function ajax_duplicate_page() {
        // Verify nonce and permissions
        if (!wp_verify_nonce($_POST['nonce'], 'dhe_editor_nonce') || !current_user_can('edit_pages')) {
            wp_send_json_error('Permission denied');
            return;
        }
        
        $source_variant = sanitize_text_field($_POST['source_variant'] ?? 'default');
        $new_variant_name = sanitize_title($_POST['variant_name']);
        
        if (empty($new_variant_name)) {
            wp_send_json_error('Page name is required');
            return;
        }
        
        // Check if page already exists
        $new_option_key = 'dhe_content_data_' . $new_variant_name;
        if (get_option($new_option_key)) {
            wp_send_json_error('A page with this name already exists');
            return;
        }
        
        // Get current page content
        $source_option_key = 'dhe_content_data_' . $source_variant;
        $content = get_option($source_option_key, array());
        
        // Save as new page
        update_option($new_option_key, $content);
        
        $new_url = home_url('/deangele-hardscape/' . $new_variant_name);
        
        wp_send_json_success(array(
            'message' => 'Page duplicated successfully!',
            'new_url' => $new_url,
            'variant_name' => $new_variant_name
        ));
    }
}

// Initialize the plugin
function dhe_init() {
    return DeAngele_Hardscape_Editor::get_instance();
}

// Start the plugin
dhe_init();

// Helper function for templates with hardscape material fallbacks
function dhe_get_content($key, $default = '') {
    $instance = DeAngele_Hardscape_Editor::get_instance();
    $content = $instance->get_content($key, $default);
    
    // Step 5: Fallback for hardscape materials if empty
    if (empty($content) && strpos($key, 'hardscape_material') === 0) {
        $fallbacks = array(
            'hardscape_material1_title' => 'Natural Stone Veneer',
            'hardscape_material1_description' => 'Desert Creek stone veneer creates stunning focal points for outdoor fireplaces and accent walls.',
            'hardscape_material1_image' => 'https://deangele.com/wp-content/uploads/2024/05/natural-stone-veneer-fireplace-hingham-ma.jpg',
            'hardscape_material2_title' => 'Granite Countertop Material',
            'hardscape_material2_description' => 'Premium granite surfaces for outdoor kitchen islands and bar tops, engineered for weather resistance.',
            'hardscape_material2_image' => 'https://deangele.com/wp-content/uploads/2024/05/granite-outdoor-kitchen-countertop-hingham.jpg',
            'hardscape_material3_title' => 'Stacked Stone Base',
            'hardscape_material3_description' => 'Durable stacked stone construction for outdoor kitchen islands and structural elements.',
            'hardscape_material3_image' => 'https://deangele.com/wp-content/uploads/2024/05/stacked-stone-outdoor-kitchen-base-hingham.jpg'
        );
        
        return $fallbacks[$key] ?? $default;
    }
    
    return $content;
}