# DeAngele Hardscape Front-End Editor

**Version:** 1.0.0  
**Requires:** WordPress 5.0+  
**Tested up to:** WordPress 6.4  
**License:** GPL v2 or later  

## Overview

A simplified WordPress plugin for front-end editing of the DeAngele hardscape landing page template. Built following the detailed implementation plan in `/docs/plan.md`.

## Features ✅

- **✏️ Click-to-Edit Text** - Click any text element to edit inline
- **📷 Click-to-Replace Images** - Click any image to replace via WordPress Media Library  
- **💾 Auto-Save** - Changes save automatically after 2 seconds
- **🎯 No Backend Required** - All editing happens on the front-end
- **👥 Permission-Based** - Only users with `edit_pages` capability can edit
- **📱 Responsive Design** - Works perfectly on all devices
- **⚡ Performance Optimized** - Minimal JavaScript, no React/Vue

## Quick Start

### 1. Plugin Activation
1. The plugin is already in the correct location
2. Go to WordPress Admin → Plugins
3. Find "DeAngele Hardscape Front-End Editor"
4. Click **Activate**

### 2. Access Your Page
- Visit: `pagebuilder.local/deangele-hardscape`
- You'll see the hardscape template with all original styling

### 3. Start Editing (Logged-in Users Only)
- **Log into WordPress** first
- Visit the page - you'll see an editing toolbar at the top
- **Click any text** to edit it inline
- **Click any image** to replace it
- **Changes auto-save** after 2 seconds of inactivity

## How to Use

### Text Editing
1. **Click any text** you want to edit
2. **Type your changes** directly
3. **Press Enter** to save (or click ✓)
4. **Press Escape** to cancel (or click ✕)

### Image Replacement  
1. **Click any image** you want to replace
2. **Select from Media Library** or upload new
3. **Changes save automatically**

### Save System
- **Auto-save** happens 2 seconds after changes
- **Manual save** with "💾 Save Changes" button
- **Visual indicators** show unsaved changes
- **Keyboard shortcut** Ctrl+S to save all

## Architecture

Following the implementation plan, this plugin uses:

### Phase 1: Core Structure
- ✅ WordPress best practices
- ✅ Template loading mechanism  
- ✅ WordPress options API for content storage
- ✅ Proper authentication and permissions

### Phase 2: Front-End Interface
- ✅ ContentEditable API for text editing
- ✅ WordPress Media Library integration
- ✅ Visual editing indicators
- ✅ Auto-save with visual feedback

## File Structure
```
deangele-page-builder/
├── deangele-hardscape-editor.php    # Main plugin file
├── templates/
│   └── hardscape-template.php       # HTML template with dynamic content
├── assets/
│   ├── css/
│   │   ├── template.css            # Original hardscape design styles
│   │   └── editor.css              # Editing interface styles
│   └── js/
│       └── editor.js               # Front-end editing functionality
└── README.md                       # This file
```

## Technical Details

### Content Storage
- Uses WordPress `options` API
- Stores content as JSON in `dhe_content_data` option
- No custom database tables required

### Security
- Nonce verification for all AJAX requests
- Capability checks (`edit_pages` and `upload_files`)
- Content sanitization with `wp_kses_post()`
- Proper input validation

### Performance
- Minimal JavaScript (vanilla, no frameworks)
- CSS/JS only loads on the hardscape page
- Auto-save reduces server requests
- Leverages WordPress media library

### Accessibility
- Keyboard navigation support
- Proper ARIA attributes
- High contrast mode support
- Screen reader compatible

## Troubleshooting

### "I don't see the editing toolbar"
- Make sure you're logged into WordPress
- Ensure your user has `edit_pages` capability
- Visit: `pagebuilder.local/deangele-hardscape`

### "Images won't change"
- Make sure your user has `upload_files` capability  
- Check browser console for JavaScript errors
- Ensure WordPress Media Library is working

### "Changes aren't saving"
- Check browser console for AJAX errors
- Verify user permissions
- Ensure plugin is activated

### "Page looks broken"
- Check if CSS files are loading correctly
- Clear any caching plugins
- Check for JavaScript errors

## Browser Support

- ✅ Chrome 80+
- ✅ Firefox 75+  
- ✅ Safari 13+
- ✅ Edge 80+

## Development

Built strictly following the requirements in `/docs/plan.md`:

- **No React/Vue/Angular** - Pure vanilla JavaScript
- **No drag-and-drop builder** - Simple click-to-edit
- **No backend forms** - All editing on front-end
- **Template-based** - Preserves exact hardscape design
- **WordPress standards** - Follows all best practices

## Support

For technical support or questions, contact the DeAngele development team.

---

**Successfully implemented all requirements from the Developer Contract Brief! 🎉**