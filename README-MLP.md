# MLP (Modern Landing Page) Editor

A comprehensive inline landing page editor for WordPress that provides click-to-edit functionality with instant feedback, state management, and robust saving capabilities.

## Features

### 🎯 Core Functionality
- **Click-to-edit anywhere** - Edit any visible element directly on the page
- **Instant visual feedback** - See changes immediately with toast notifications
- **State management** - Robust operation logging with undo/redo support
- **Autosave** - Automatic saving to localStorage with 2-3 second debouncing
- **Template-based cloning** - Clean page generation without editing artifacts

### 📝 Content Types Supported
- **Text** - Plain text editing with inline toolbar
- **Rich Text** - HTML editing with formatting options (bold, italic, links, lists)
- **Images** - WordPress Media Library integration with drag & drop
- **Background Images** - Hero section background image editing
- **Links** - URL and target attribute editing
- **Shortcodes** - Whitelisted shortcode support (Trustindex, Contact Form 7, etc.)
- **Embeds** - YouTube and Instagram embed support

### 🎨 User Experience
- **Modern UI** - Clean, accessible interface with CSS variables for theming
- **Keyboard shortcuts** - Ctrl+S to save, Ctrl+Z to undo, Esc to cancel
- **Responsive design** - Works on desktop, tablet, and mobile
- **Accessibility** - ARIA roles, keyboard navigation, screen reader support
- **Dark mode support** - Automatic theme detection

### 🔧 Technical Features
- **REST API** - Modern WordPress REST endpoints for all operations
- **Security** - Nonce verification, capability checks, content sanitization
- **Performance** - Event delegation, debounced updates, minimal DOM manipulation
- **Extensibility** - Plugin architecture for custom editor types

## Installation

1. **Upload the plugin** to your WordPress site
2. **Activate the plugin** in WordPress admin
3. **Navigate to** `/deangele-hardscape` on your site
4. **Log in** with a user account that has `edit_pages` capability
5. **Start editing** by clicking on any editable element

## Usage

### Basic Editing

1. **Click any editable element** on the page
2. **Make your changes** in the inline editor
3. **Press Enter** to save or **Esc** to cancel
4. **See instant feedback** via toast notifications

### Keyboard Shortcuts

- `Ctrl+S` / `Cmd+S` - Save current state
- `Ctrl+Z` / `Cmd+Z` - Undo last change
- `Ctrl+Shift+Z` / `Cmd+Shift+Z` - Redo last change
- `Esc` - Cancel current edit
- `Enter` - Save current edit (text mode)

### Image Editing

1. **Click on any image** to open the WordPress Media Library
2. **Select or upload** a new image
3. **Image updates immediately** on the page
4. **Background images** work the same way for hero sections

### Rich Text Editing

1. **Click on rich text areas** (descriptions, content blocks)
2. **Use the formatting toolbar** for bold, italic, links, lists
3. **HTML is sanitized** automatically for security
4. **Changes save automatically** when you finish editing

### Social Proof Sections

The editor supports three types of social proof:

#### Google Reviews
- Click the shortcode area to edit
- Enter your Trustindex or Google Reviews shortcode
- Preview renders immediately

#### YouTube Testimonials
- Click to edit the video URL
- Enter YouTube URL (auto-detects video ID)
- Add custom title and description

#### Instagram Reviews
- Upload thumbnails for each reel
- Edit titles and descriptions
- Supports 3-reel grid layout

### Saving Options

#### Save Draft
- **Automatic** - Every 3 seconds when changes are made
- **Manual** - Click "Save" button or press Ctrl+S
- **Local storage** - Drafts persist across browser sessions

#### Save as New Page
- **Click "Save as New Page"** button
- **Enter page title** and select status (Draft/Publish/Private)
- **WordPress page created** with clean HTML
- **Links provided** to view and edit the new page

## Technical Architecture

### Frontend (JavaScript)

```javascript
// Editor State Management
class EditorState {
    constructor() {
        this.meta = { title: '', slug: '', status: 'draft', template_key: 'hardscape' };
        this.nodes = {}; // All editable content
        this.bottom = { mode: 'instagram', data: {} }; // Social proof section
        this.ops = []; // Operation log for undo/redo
    }
}

// Editor Registry
const Editors = {
    text: TextEditor,
    richtext: RichTextEditor,
    image: ImageEditor,
    'bg-image': BgImageEditor,
    link: LinkEditor,
    shortcode: ShortcodeEditor,
    embed: EmbedEditor
};
```

### Backend (PHP)

```php
// REST API Endpoints
register_rest_route('mlp/v1', '/save-draft', array(
    'methods' => 'POST',
    'callback' => array($this, 'mlp_save_draft'),
    'permission_callback' => array($this, 'mlp_check_permissions')
));

register_rest_route('mlp/v1', '/save-page', array(
    'methods' => 'POST',
    'callback' => array($this, 'mlp_save_page'),
    'permission_callback' => array($this, 'mlp_check_permissions')
));
```

### Template Structure

```html
<!-- Editable elements with MLP attributes -->
<h1 data-mlp-id="hero-title" data-mlp-type="text">
    Your Title Here
</h1>

<img src="image.jpg" data-mlp-id="hero-image" data-mlp-type="image">

<div data-mlp-id="hero-description" data-mlp-type="richtext">
    Rich text content here
</div>
```

## Configuration

### MLP_CONFIG Object

```javascript
MLP_CONFIG = {
    pageId: 'default',
    nonce: 'wp_rest_nonce',
    rest: {
        saveDraft: '/wp-json/mlp/v1/save-draft',
        savePage: '/wp-json/mlp/v1/save-page',
        sideloadImage: '/wp-json/mlp/v1/sideload-image',
        getAttachment: '/wp-json/mlp/v1/get-attachment',
        renderShortcode: '/wp-json/mlp/v1/render-shortcode',
        renderEmbed: '/wp-json/mlp/v1/render-embed'
    },
    limits: {
        maxTextLength: 1000,
        maxHtmlLength: 5000,
        allowedTags: ['p', 'strong', 'em', 'ul', 'ol', 'li', 'br', 'a']
    },
    templateKey: 'hardscape',
    sourcePlugin: 'deangele-hardscape-editor'
};
```

### CSS Variables

```css
:root {
    --mlp-primary: #0073aa;
    --mlp-secondary: #005a87;
    --mlp-success: #46b450;
    --mlp-warning: #ffb900;
    --mlp-error: #dc3232;
    --mlp-text: #333;
    --mlp-border: #ddd;
    --mlp-bg: #fff;
    --mlp-radius: 6px;
    --mlp-spacing: 8px;
    --mlp-transition: all 0.2s ease;
}
```

## Security

### Content Sanitization

- **Text**: `sanitize_text_field()` for plain text
- **HTML**: `wp_kses_post()` for rich text with allowed tags
- **URLs**: `esc_url_raw()` for links and images
- **Shortcodes**: Whitelist validation before rendering

### Permission Checks

- **REST endpoints**: `current_user_can('edit_pages')`
- **Page creation**: `current_user_can('publish_pages')`
- **Media upload**: `current_user_can('upload_files')`
- **Nonce verification**: All AJAX and REST requests

### Allowed Shortcodes

```php
$allowed_shortcodes = array(
    'trustindex',
    'google-reviews-pro', 
    'youtube',
    'instagram',
    'contact-form-7'
);
```

## Performance

### Optimization Strategies

- **Event delegation** - Single click listener on document
- **Debounced autosave** - 2-3 second delay to prevent excessive requests
- **Minimal DOM updates** - Only update changed elements
- **Lazy loading** - Media picker loads on first use
- **CSS containment** - Isolated editor styles

### Memory Management

- **Operation log cleanup** - Clear after successful save
- **LocalStorage limits** - Check available space before saving
- **Event listener cleanup** - Remove listeners when editors close

## Browser Support

- **Chrome** 80+
- **Firefox** 75+
- **Safari** 13+
- **Edge** 80+

## Troubleshooting

### Common Issues

1. **Editor not loading**
   - Check user permissions (`edit_pages` capability)
   - Verify REST API is enabled
   - Check browser console for JavaScript errors

2. **Images not uploading**
   - Verify `upload_files` capability
   - Check WordPress upload directory permissions
   - Ensure Media Library is properly enqueued

3. **Changes not saving**
   - Check REST API nonce validity
   - Verify server can handle POST requests
   - Check browser localStorage availability

4. **Shortcodes not rendering**
   - Verify shortcode is in allowed list
   - Check if required plugin is active
   - Test shortcode manually in WordPress

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Development

### Adding New Editor Types

1. **Create editor function**:
```javascript
function CustomEditor(node, id, state) {
    function startEdit() {
        // Custom editing logic
    }
    
    return { startEdit };
}
```

2. **Register in Editors object**:
```javascript
const Editors = {
    // ... existing editors
    custom: CustomEditor
};
```

3. **Add to template**:
```html
<div data-mlp-id="custom-element" data-mlp-type="custom">
    Custom content
</div>
```

### Extending REST API

1. **Add new endpoint**:
```php
register_rest_route('mlp/v1', '/custom-endpoint', array(
    'methods' => 'POST',
    'callback' => array($this, 'mlp_custom_endpoint'),
    'permission_callback' => array($this, 'mlp_check_permissions')
));
```

2. **Implement callback**:
```php
public function mlp_custom_endpoint($request) {
    // Custom logic here
    return new WP_REST_Response(array('success' => true), 200);
}
```

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support and feature requests, please contact the development team.

---

**MLP Editor v1.0** - Modern, accessible, and powerful inline editing for WordPress landing pages.



