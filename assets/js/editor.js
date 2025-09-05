/**
 * DeAngele Hardscape Editor
 * Phase 2: Front-End Editing Interface
 * 
 * Following plan.md specifications:
 * - ContentEditable API for inline text editing
 * - WordPress media library integration for images
 * - Visual indicators and editing mode toggle
 * - Auto-saving with visual feedback
 */

jQuery(document).ready(function($) {
    'use strict';
    
    if (!dhe_ajax || !dhe_ajax.editing_mode) {
        return; // Exit if not in editing mode
    }
    
    // Initialize the editor
    var DHEEditor = {
        init: function() {
            console.log('DHE Editor initializing...');
            this.setupTextEditing();
            this.setupImageEditing();
            this.setupSaveSystem();
            this.setupKeyboardShortcuts();
            this.addEditingIndicators();
            this.setupSocialProofSelection();
            this.setupPageManagement();
        },
        
        // Phase 2: ContentEditable API for inline text editing
        setupTextEditing: function() {
            var self = this;
            
            // Make all marked text elements editable
            $('.dhe-editable-text').each(function() {
                var $element = $(this);
                var contentKey = $element.data('dhe-content-key');
                
                if (!contentKey) return;
                
                // Add editing indicators
                $element.attr('title', 'Click to edit this text');
                $element.addClass('dhe-text-ready');
                
                // Single click to enter editing mode
                $element.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.startTextEditing($element, contentKey);
                });
                
                // Hover effects for better UX
                $element.on('mouseenter', function() {
                    if (!$element.hasClass('dhe-editing')) {
                        $element.addClass('dhe-text-hover');
                    }
                }).on('mouseleave', function() {
                    $element.removeClass('dhe-text-hover');
                });
            });
        },
        
        startTextEditing: function($element, contentKey) {
            var self = this;
            
            // Prevent multiple editing sessions
            if ($element.hasClass('dhe-editing')) {
                return;
            }
            
            // Store original content
            var originalContent = $element.text().trim();
            
            // Add editing class
            $element.addClass('dhe-editing').removeClass('dhe-text-hover');
            
            // Make element contenteditable
            $element.attr('contenteditable', 'true');
            $element.focus();
            
            // Select all text
            self.selectAllText($element[0]);
            
            // Create save/cancel controls
            var $controls = $('<div class="dhe-text-controls">' +
                '<button class="dhe-save-text" title="Save (Enter)">✓</button>' +
                '<button class="dhe-cancel-text" title="Cancel (Esc)">✕</button>' +
                '</div>');
            
            $element.after($controls);
            
            // Handle keyboard shortcuts
            $element.on('keydown.dhe-edit', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    self.saveTextEdit($element, contentKey, originalContent);
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    self.cancelTextEdit($element, originalContent);
                }
            });
            
            // Handle control buttons
            $controls.find('.dhe-save-text').on('click', function(e) {
                e.preventDefault();
                self.saveTextEdit($element, contentKey, originalContent);
            });
            
            $controls.find('.dhe-cancel-text').on('click', function(e) {
                e.preventDefault();
                self.cancelTextEdit($element, originalContent);
            });
            
            // Handle click outside to save
            $(document).on('click.dhe-outside', function(e) {
                if (!$element.is(e.target) && !$controls.is(e.target) && $controls.has(e.target).length === 0) {
                    self.saveTextEdit($element, contentKey, originalContent);
                }
            });
        },
        
        saveTextEdit: function($element, contentKey, originalContent) {
            var newContent = $element.text().trim();
            
            // Clean up editing mode
            this.cleanupTextEditing($element);
            
            // Check if content changed
            if (newContent !== originalContent && newContent !== '') {
                $element.addClass('dhe-changed');
                this.saveContent(contentKey, newContent);
                this.showStatus('Content updated!', 'success');
            } else if (newContent === '') {
                // Restore original content if empty
                $element.text(originalContent);
                this.showStatus('Content cannot be empty', 'warning');
            }
        },
        
        cancelTextEdit: function($element, originalContent) {
            // Restore original content
            $element.text(originalContent);
            
            // Clean up editing mode
            this.cleanupTextEditing($element);
            
            this.showStatus('Edit cancelled', 'info');
        },
        
        cleanupTextEditing: function($element) {
            $element.removeClass('dhe-editing dhe-text-hover')
                   .removeAttr('contenteditable')
                   .off('keydown.dhe-edit');
            
            // Remove controls
            $element.next('.dhe-text-controls').remove();
            
            // Remove outside click handler
            $(document).off('click.dhe-outside');
        },
        
        selectAllText: function(element) {
            if (window.getSelection && document.createRange) {
                var range = document.createRange();
                range.selectNodeContents(element);
                var selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            }
        },
        
        // Phase 2: WordPress media library integration for images
        setupImageEditing: function() {
            var self = this;
            
            // Handle regular images
            $('.dhe-editable-image').each(function() {
                var $img = $(this);
                var contentKey = $img.data('dhe-content-key');
                
                if (!contentKey) return;
                
                // Create image overlay
                var $overlay = $('<div class="dhe-image-overlay">' +
                    '<div class="dhe-image-controls">' +
                        '<button class="dhe-change-image">📷 Change Image</button>' +
                    '</div>' +
                '</div>');
                
                // Wrap image in container
                if (!$img.parent().hasClass('dhe-image-container')) {
                    $img.wrap('<div class="dhe-image-container"></div>');
                }
                
                var $container = $img.parent();
                $container.append($overlay);
                
                // Handle image click - only when not in editing mode
                $container.on('click', function(e) {
                    // Don't open media library if we're in editing mode
                    if ($img.hasClass('dhe-editing')) {
                        return;
                    }
                    e.preventDefault();
                    self.openMediaLibrary($img, contentKey);
                });
                
                // Handle change image button click
                $overlay.find('.dhe-change-image').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.openMediaLibrary($img, contentKey);
                });
            });
            
            // Handle background image editing for hero section
            $('.dhe-bg-image-overlay').each(function() {
                var $overlay = $(this);
                var contentKey = $overlay.data('dhe-content-key');
                
                if (!contentKey) return;
                
                // Handle background image click
                $overlay.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.openBackgroundMediaLibrary(contentKey);
                });
            });
        },
        
        openMediaLibrary: function($img, contentKey) {
            var self = this;
            
            // Check if wp.media exists
            if (typeof wp === 'undefined' || !wp.media) {
                this.showStatus('Media library not available', 'error');
                return;
            }
            
            // Store original image source for potential cancellation
            var originalSrc = $img.attr('src');
            
            // Create media frame
            var mediaFrame = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Use This Image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            // Handle selection
            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                
                if (attachment && attachment.url) {
                    // Update image source
                    $img.attr('src', attachment.url);
                    $img.addClass('dhe-changed');
                    
                    // Create save/cancel controls for image editing
                    self.createImageControls($img, contentKey, attachment.id, attachment.url, originalSrc);
                }
            });
            
            // Open media frame
            mediaFrame.open();
        },
        
        openBackgroundMediaLibrary: function(contentKey) {
            var self = this;
            
            // Check if wp.media exists
            if (typeof wp === 'undefined' || !wp.media) {
                this.showStatus('Media library not available', 'error');
                return;
            }
            
            // Store original background style for potential cancellation
            var $hero = $('.hero');
            var originalStyle = $hero.attr('style');
            
            // Create media frame
            var mediaFrame = wp.media({
                title: 'Select Background Image',
                button: {
                    text: 'Use This Background'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            // Handle selection
            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                
                if (attachment && attachment.url) {
                    // Update hero section background
                    var currentStyle = $hero.attr('style');
                    var newStyle = currentStyle.replace(/url\([^)]*\)/, "url('" + attachment.url + "')");
                    $hero.attr('style', newStyle);
                    $hero.addClass('dhe-changed');
                    
                    // Create save/cancel controls for background image editing
                    self.createBackgroundImageControls($hero, contentKey, attachment.id, attachment.url, originalStyle);
                }
            });
            
            // Open media frame
            mediaFrame.open();
        },
        
        saveMediaContent: function(contentKey, attachmentId, imageUrl) {
            var self = this;
            
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_upload_media',
                    nonce: dhe_ajax.nonce,
                    content_key: contentKey,
                    attachment_id: attachmentId
                },
                success: function(response) {
                    if (response.success) {
                        self.showStatus('Image updated!', 'success');
                        self.updateSaveButton();
                    } else {
                        self.showStatus('Failed to save image: ' + (response.data || 'Unknown error'), 'error');
                    }
                },
                error: function() {
                    self.showStatus('Network error while saving image', 'error');
                }
            });
        },
        
        createImageControls: function($img, contentKey, attachmentId, imageUrl, originalSrc) {
            var self = this;
            
            // Remove any existing controls
            $img.siblings('.dhe-image-save-controls').remove();
            
            // Add editing class for visual feedback
            $img.addClass('dhe-editing');
            
            // Create a separate container for save controls that's completely independent
            var $saveContainer = $('<div class="dhe-image-save-controls">' +
                '<button class="dhe-save-image" title="Save (Enter)">✓</button>' +
                '<button class="dhe-cancel-image" title="Cancel (Esc)">✕</button>' +
                '</div>');
            
            // Position the save controls outside the image container to avoid conflicts
            $img.closest('.dhe-image-container').after($saveContainer);
            
            // Handle save button with complete event isolation
            $saveContainer.find('.dhe-save-image').on('click.dhe-save', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Save image content and update page save system
                self.saveImageContent(contentKey, attachmentId, imageUrl);
                self.cleanupImageControls($img);
            });
            
            // Handle cancel button with complete event isolation
            $saveContainer.find('.dhe-cancel-image').on('click.dhe-cancel', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                $img.attr('src', originalSrc);
                $img.removeClass('dhe-changed');
                self.cleanupImageControls($img);
                self.showStatus('Image change cancelled', 'info');
            });
            
            // Handle keyboard shortcuts with proper namespace
            $(document).on('keydown.dhe-image-edit-' + contentKey, function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    self.saveImageContent(contentKey, attachmentId, imageUrl);
                    self.cleanupImageControls($img);
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    $img.attr('src', originalSrc);
                    $img.removeClass('dhe-changed');
                    self.cleanupImageControls($img);
                    self.showStatus('Image change cancelled', 'info');
                }
            });
        },
        
        createBackgroundImageControls: function($hero, contentKey, attachmentId, imageUrl, originalStyle) {
            var self = this;
            
            // Remove any existing controls
            $hero.find('.dhe-bg-image-controls').remove();
            
            // Add editing class for visual feedback
            $hero.addClass('dhe-editing');
            
            // Create controls
            var $controls = $('<div class="dhe-bg-image-controls">' +
                '<button class="dhe-save-bg-image" title="Save (Enter)">✓</button>' +
                '<button class="dhe-cancel-bg-image" title="Cancel (Esc)">✕</button>' +
                '</div>');
            
            $hero.append($controls);
            
            // Handle save button
            $controls.find('.dhe-save-bg-image').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event from bubbling up
                // Save background image content and update page save system
                self.saveBackgroundImageContent(contentKey, attachmentId, imageUrl);
                self.cleanupBackgroundImageControls($hero);
            });
            
            // Handle cancel button
            $controls.find('.dhe-cancel-bg-image').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event from bubbling up
                $hero.attr('style', originalStyle);
                $hero.removeClass('dhe-changed');
                self.cleanupBackgroundImageControls($hero);
                self.showStatus('Background image change cancelled', 'info');
            });
            
            // Handle keyboard shortcuts
            $(document).on('keydown.dhe-bg-image-edit', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    self.saveBackgroundImageContent(contentKey, attachmentId, imageUrl);
                    self.cleanupBackgroundImageControls($hero);
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    $hero.attr('style', originalStyle);
                    $hero.removeClass('dhe-changed');
                    self.cleanupBackgroundImageControls($hero);
                    self.showStatus('Background image change cancelled', 'info');
                }
            });
        },
        
        cleanupImageControls: function($img) {
            $img.removeClass('dhe-editing');
            
            // Remove save controls from outside the image container
            $img.closest('.dhe-image-container').siblings('.dhe-image-save-controls').remove();
            
            // Remove namespaced keyboard events
            var contentKey = $img.data('dhe-content-key');
            if (contentKey) {
                $(document).off('keydown.dhe-image-edit-' + contentKey);
            }
            
            // Remove namespaced click events
            $('.dhe-image-save-controls').off('.dhe-save .dhe-cancel');
        },
        
        cleanupBackgroundImageControls: function($hero) {
            $hero.removeClass('dhe-editing');
            $hero.find('.dhe-bg-image-controls').remove();
            $(document).off('keydown.dhe-bg-image-edit');
        },
        
        // New function to save image content and update page save system
        saveImageContent: function(contentKey, attachmentId, imageUrl) {
            var self = this;
            
            // First save the media content
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_upload_media',
                    nonce: dhe_ajax.nonce,
                    content_key: contentKey,
                    attachment_id: attachmentId
                },
                success: function(response) {
                    if (response.success) {
                        // Update the page save system to show there are changes
                        self.saveContent(contentKey, imageUrl);
                        self.showStatus('Image updated!', 'success');
                    } else {
                        self.showStatus('Failed to save image: ' + (response.data || 'Unknown error'), 'error');
                    }
                },
                error: function() {
                    self.showStatus('Network error while saving image', 'error');
                }
            });
        },
        
        // New function to save background image content and update page save system
        saveBackgroundImageContent: function(contentKey, attachmentId, imageUrl) {
            var self = this;
            
            // First save the media content
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_upload_media',
                    nonce: dhe_ajax.nonce,
                    content_key: contentKey,
                    attachment_id: attachmentId
                },
                success: function(response) {
                    if (response.success) {
                        // Update the page save system to show there are changes
                        self.saveContent(contentKey, imageUrl);
                        self.showStatus('Background image updated!', 'success');
                    } else {
                        self.showStatus('Failed to save background image: ' + (response.data || 'Unknown error'), 'error');
                    }
                },
                error: function() {
                    self.showStatus('Network error while saving background image', 'error');
                }
            });
        },
        
        // Phase 2: Save/publish mechanism with auto-saving
        setupSaveSystem: function() {
            var self = this;
            
            // Handle save all button
            $('#dhe-save-all').on('click', function(e) {
                e.preventDefault();
                self.saveAllChanges();
            });
            
            // Handle generate page button
            $('#dhe-generate-page').on('click', function(e) {
                e.preventDefault();
                self.generatePage();
            });
            
            // Handle preview toggle button
            $('#dhe-preview-toggle').on('click', function(e) {
                e.preventDefault();
                self.togglePreviewMode();
            });
            
            // Handle force update button (for debugging materials issue)
            $('#dhe-force-update').on('click', function(e) {
                e.preventDefault();
                self.forceUpdateContent();
            });
            
            
            // Diagnose Template
            $('#dhe-diagnose-template').on('click', function(e) {
                e.preventDefault();
                self.diagnoseTemplate();
            });
            
            
            // Auto-save timer
            this.autoSaveTimer = null;
            this.pendingChanges = {};
        },
        
        saveContent: function(contentKey, contentValue) {
            var self = this;
            
            // Add to pending changes
            this.pendingChanges[contentKey] = contentValue;
            
            // Clear existing auto-save timer
            if (this.autoSaveTimer) {
                clearTimeout(this.autoSaveTimer);
            }
            
            // Show immediate visual feedback
            this.showStatus('Changes pending...', 'info');
            
            // Set new auto-save timer with longer delay for better UX
            this.autoSaveTimer = setTimeout(function() {
                self.performAutoSave();
            }, 3000); // Auto-save after 3 seconds of inactivity (increased from 2)
            
            // Update UI immediately
            this.updateSaveButton();
        },
        
        performAutoSave: function() {
            var self = this;
            var changesCount = Object.keys(this.pendingChanges).length;
            
            if (changesCount === 0) return;
            
            // Set saving state
            this.isSaving = true;
            this.showStatus('Auto-saving ' + changesCount + ' change(s)...', 'info');
            
            // Save each pending change
            var promises = [];
            var savedChanges = Object.assign({}, this.pendingChanges); // Copy for cleanup
            
            Object.keys(this.pendingChanges).forEach(function(contentKey) {
                var promise = $.ajax({
                    url: dhe_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'dhe_save_content',
                        nonce: dhe_ajax.nonce,
                        content_key: contentKey,
                        content_value: self.pendingChanges[contentKey],
                        variant: dhe_ajax.current_variant || 'default'
                    }
                });
                promises.push(promise);
            });
            
            // Clear pending changes immediately to prevent duplicate saves
            this.pendingChanges = {};
            
            // Handle completion
            $.when.apply($, promises).then(
                function() {
                    // All saves successful
                    self.isSaving = false;
                    self.showStatus('All changes saved!', 'success');
                    self.updateSaveButton();
                    
                    // Remove changed indicators with slight delay for visual feedback
                    setTimeout(function() {
                        $('.dhe-changed').removeClass('dhe-changed');
                    }, 500);
                },
                function(xhr, status, error) {
                    // Some saves failed - restore failed changes
                    self.isSaving = false;
                    Object.assign(self.pendingChanges, savedChanges);
                    self.showStatus('Save failed: ' + (error || 'Network error'), 'error');
                    self.updateSaveButton();
                }
            );
        },
        
        saveAllChanges: function() {
            if (this.autoSaveTimer) {
                clearTimeout(this.autoSaveTimer);
            }
            this.performAutoSave();
        },
        
        // NEW WORKFLOW: Generate WordPress page
        generatePage: function() {
            var self = this;
            
            // Prompt for page title
            var pageTitle = prompt('Enter a title for your new page:', 'My Hardscape Page');
            if (pageTitle === null) {
                return; // User cancelled
            }
            
            // Save any pending changes first
            if (Object.keys(this.pendingChanges).length > 0) {
                this.showStatus('Saving changes before generating page...', 'info');
                this.performAutoSave();
                
                // Wait a moment for saves to complete, then generate
                setTimeout(function() {
                    self.performPageGeneration(pageTitle);
                }, 1000);
            } else {
                this.performPageGeneration(pageTitle);
            }
        },
        
        performPageGeneration: function(pageTitle) {
            var self = this;
            
            this.showStatus('Generating new WordPress page...', 'info');
            
            // Disable generate button during process
            $('#dhe-generate-page').prop('disabled', true).text('🔄 Generating...');
            
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_generate_page',
                    nonce: dhe_ajax.nonce,
                    page_title: pageTitle
                },
                success: function(response) {
                    if (response.success) {
                        self.showStatus('Page generated successfully!', 'success');
                        
                        // Show success message with link
                        var message = 'New page created: "' + response.data.page_title + '"';
                        if (confirm(message + '\n\nWould you like to view the new page?')) {
                            window.open(response.data.page_url, '_blank');
                        }
                    } else {
                        self.showStatus('Failed to generate page: ' + (response.data || 'Unknown error'), 'error');
                    }
                },
                error: function() {
                    self.showStatus('Network error while generating page', 'error');
                },
                complete: function() {
                    // Re-enable generate button
                    $('#dhe-generate-page').prop('disabled', false).text('📄 Generate New Page');
                }
            });
        },
        
        updateSaveButton: function() {
            var $saveBtn = $('#dhe-save-all');
            var changesCount = Object.keys(this.pendingChanges).length;
            var hasVisualChanges = $('.dhe-changed').length > 0;
            
            if (changesCount > 0 || hasVisualChanges) {
                $saveBtn.prop('disabled', false)
                       .removeClass('dhe-btn-disabled')
                       .addClass('dhe-btn-active');
                
                if (changesCount > 0) {
                    $saveBtn.text('💾 Save ' + changesCount + ' Change(s)');
                } else {
                    $saveBtn.text('💾 Save Changes');
                }
            } else {
                $saveBtn.prop('disabled', true)
                       .removeClass('dhe-btn-active')
                       .addClass('dhe-btn-disabled')
                       .text('💾 No Changes');
            }
        },
        
        // Elegant notification system
        showStatus: function(message, type) {
            var self = this;
            var $container = $('#dhe-notifications-container');
            
            // Create notification element
            var notificationId = 'dhe-notification-' + Date.now();
            var $notification = $('<div class="dhe-notification dhe-notification-' + type + '" id="' + notificationId + '">' +
                '<div class="dhe-notification-text">' + message + '</div>' +
                '<button class="dhe-notification-close" title="Close">&times;</button>' +
                '</div>');
            
            // Add to container
            $container.append($notification);
            
            // Trigger show animation
            setTimeout(function() {
                $notification.addClass('show');
            }, 50);
            
            // Handle close button
            $notification.find('.dhe-notification-close').on('click', function() {
                self.hideNotification(notificationId);
            });
            
            // Auto-hide after delay
            var delay = type === 'error' ? 5000 : 
                       type === 'success' ? 3000 : 
                       type === 'warning' ? 4000 : 3000;
                       
            setTimeout(function() {
                self.hideNotification(notificationId);
            }, delay);
        },
        
        // Hide specific notification
        hideNotification: function(notificationId) {
            var $notification = $('#' + notificationId);
            if ($notification.length) {
                $notification.addClass('hide');
                setTimeout(function() {
                    $notification.remove();
                }, 400); // Match CSS transition duration
            }
        },
        
        // Phase 2: Visual indicators and editing mode
        addEditingIndicators: function() {
            var self = this;
            
            // Add body class for editing mode
            $('body').addClass('dhe-editing-active');
            
            // Add tooltips for editable elements
            $('.dhe-editable-text').attr('data-dhe-tooltip', 'Click to edit text');
            $('.dhe-editable-image').attr('data-dhe-tooltip', 'Click to change image');
            
            // Setup button editing with right-click
            this.setupButtonEditing();
        },
        
        // Setup button editing with right-click functionality
        setupButtonEditing: function() {
            var self = this;
            
            // Find all buttons that should be editable
            $('button, .btn, .cta-button, .sticky-cta, input[type="submit"], input[type="button"]').each(function() {
                var $button = $(this);
                
                // Skip if already has content key or is a system button
                if ($button.data('dhe-content-key') || $button.hasClass('dhe-save-image') || $button.hasClass('dhe-cancel-image') || $button.hasClass('dhe-save-text') || $button.hasClass('dhe-cancel-text')) {
                    return;
                }
                
                // Add editable class and content key
                $button.addClass('dhe-editable-button');
                var contentKey = 'button_text_' + $button.text().replace(/[^a-zA-Z0-9]/g, '_').toLowerCase() + '_' + Date.now();
                $button.attr('data-dhe-content-key', contentKey);
                
                // Add hover hint
                $button.attr('data-dhe-tooltip', 'Right-click to edit text');
                
                // Handle right-click to edit
                $button.on('contextmenu', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.startButtonEditing($button, contentKey);
                });
                
                // Handle hover for tooltip
                $button.on('mouseenter', function() {
                    if (!$button.hasClass('dhe-editing')) {
                        $button.addClass('dhe-button-hover');
                    }
                }).on('mouseleave', function() {
                    $button.removeClass('dhe-button-hover');
                });
            });
        },
        
        // Start button editing
        startButtonEditing: function($button, contentKey) {
            var self = this;
            
            // Prevent multiple editing sessions
            if ($button.hasClass('dhe-editing')) {
                return;
            }
            
            // Store original content
            var originalContent = $button.text().trim();
            
            // Add editing class
            $button.addClass('dhe-editing').removeClass('dhe-button-hover');
            
            // Create input field
            var $input = $('<input type="text" class="dhe-button-edit-input" value="' + originalContent + '">');
            
            // Replace button content with input
            $button.html($input);
            $input.focus().select();
            
            // Create save/cancel controls
            var $controls = $('<div class="dhe-button-controls">' +
                '<button class="dhe-save-button" title="Save (Enter)">✓</button>' +
                '<button class="dhe-cancel-button" title="Cancel (Esc)">✕</button>' +
                '</div>');
            
            $button.after($controls);
            
            // Handle keyboard shortcuts
            $input.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    self.saveButtonEdit($button, contentKey, originalContent);
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    self.cancelButtonEdit($button, originalContent);
                }
            });
            
            // Handle control buttons
            $controls.find('.dhe-save-button').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.saveButtonEdit($button, contentKey, originalContent);
            });
            
            $controls.find('.dhe-cancel-button').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.cancelButtonEdit($button, originalContent);
            });
            
            // Handle click outside to save
            $(document).on('click.dhe-button-outside', function(e) {
                if (!$button.is(e.target) && !$controls.is(e.target) && $controls.has(e.target).length === 0) {
                    self.saveButtonEdit($button, contentKey, originalContent);
                }
            });
        },
        
        // Save button edit
        saveButtonEdit: function($button, contentKey, originalContent) {
            var newContent = $button.find('.dhe-button-edit-input').val().trim();
            
            // Clean up editing mode
            this.cleanupButtonEditing($button);
            
            // Check if content changed
            if (newContent !== originalContent && newContent !== '') {
                $button.text(newContent);
                $button.addClass('dhe-changed');
                this.saveContent(contentKey, newContent);
                this.showStatus('Button text updated!', 'success');
            } else if (newContent === '') {
                // Restore original content if empty
                $button.text(originalContent);
                this.showStatus('Button text cannot be empty', 'warning');
            }
        },
        
        // Cancel button edit
        cancelButtonEdit: function($button, originalContent) {
            // Restore original content
            $button.text(originalContent);
            
            // Clean up editing mode
            this.cleanupButtonEditing($button);
            
            this.showStatus('Button edit cancelled', 'info');
        },
        
        // Cleanup button editing
        cleanupButtonEditing: function($button) {
            $button.removeClass('dhe-editing dhe-button-hover');
            
            // Remove controls
            $button.next('.dhe-button-controls').remove();
            
            // Remove outside click handler
            $(document).off('click.dhe-button-outside');
        },
        
        // Keyboard shortcuts
        setupKeyboardShortcuts: function() {
            var self = this;
            
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + S to save
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    self.saveAllChanges();
                }
                
                // Ctrl/Cmd + Z to undo (future feature)
                if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                    // Placeholder for undo functionality
                    e.preventDefault();
                    self.showStatus('Undo feature coming soon!', 'info');
                }
            });
        },
        
        // NEW FEATURE: Social Proof Selection Handling
        setupSocialProofSelection: function() {
            var self = this;
            
            // Initialize the correct section on load
            var selectedType = $('input[name="social_proof_type"]:checked').val() || 'instagram';
            self.switchSocialProofContent(selectedType);
            
            // Handle radio button changes
            $('input[name="social_proof_type"]').on('change', function() {
                var selectedType = $(this).val();
                self.saveContent('social_proof_type', selectedType);
                
                // Show status
                self.showStatus('Social proof type changed to: ' + selectedType, 'success');
                
                // Switch content without reloading
                self.switchSocialProofContent(selectedType);
            });
            
            // Handle YouTube URL editing
            $('.dhe-editable-url').on('change blur', function() {
                var $input = $(this);
                var contentKey = $input.data('dhe-content-key');
                var newValue = $input.val();
                
                if (contentKey) {
                    self.saveContent(contentKey, newValue);
                    self.showStatus('YouTube URL updated!', 'success');
                    
                    // Update YouTube embed without reloading
                    if (contentKey === 'youtube_video_url') {
                        self.updateYouTubeEmbed(newValue);
                    }
                }
            });
            
            // Handle shortcode input editing
            $('.dhe-shortcode-input').on('change blur', function() {
                var $input = $(this);
                var contentKey = $input.data('dhe-content-key');
                var newValue = $input.val();
                
                if (contentKey) {
                    self.saveContent(contentKey, newValue);
                    self.showStatus('Reviews shortcode updated!', 'success');
                    
                    // Update reviews display without reloading
                    self.updateReviewsDisplay(newValue);
                }
            });
            
            // Handle Instagram embed input editing
            $('.dhe-instagram-embed-input').on('change blur', function() {
                var $input = $(this);
                var contentKey = $input.data('dhe-content-key');
                var newValue = $input.val().trim();
                
                if (contentKey) {
                    // Validate Instagram URL
                    if (newValue && self.isValidInstagramUrl(newValue)) {
                        // Save the new URL
                        self.saveContent(contentKey, newValue);
                        self.showStatus('Instagram URL updated! Reload page to see changes.', 'success');
                    } else if (newValue === '') {
                        self.saveContent(contentKey, newValue);
                        self.showStatus('Instagram URL cleared', 'info');
                    } else if (newValue) {
                        self.showStatus('Please enter a valid Instagram reel URL', 'warning');
                    }
                }
            });
            
            // Handle Instagram title input editing
            $('.dhe-instagram-title-input').on('change blur', function() {
                var $input = $(this);
                var contentKey = $input.data('dhe-content-key');
                var newValue = $input.val();
                
                if (contentKey) {
                    self.saveContent(contentKey, newValue);
                    self.showStatus('Instagram title updated!', 'success');
                }
            });
            
            // Handle Instagram description input editing
            $('.dhe-instagram-description-input').on('change blur', function() {
                var $input = $(this);
                var contentKey = $input.data('dhe-content-key');
                var newValue = $input.val();
                
                if (contentKey) {
                    self.saveContent(contentKey, newValue);
                    self.showStatus('Instagram description updated!', 'success');
                }
            });
            
            // Handle thumbnail upload buttons
            $('.dhe-upload-thumbnail').on('click', function(e) {
                e.preventDefault();
                var $button = $(this);
                var contentKey = $button.data('dhe-content-key');
                var reelIndex = $button.data('reel-index');
                
                self.openThumbnailUpload(contentKey, reelIndex);
            });
            
            // Handle text input editing for other fields
            $('.dhe-editable-text-input').on('change blur', function() {
                var $input = $(this);
                var contentKey = $input.data('dhe-content-key');
                var newValue = $input.val();
                
                if (contentKey) {
                    self.saveContent(contentKey, newValue);
                    self.showStatus('Content updated!', 'success');
                }
            });
            
            // Handle Instagram reel modal - updated for new structure
            $(document).on('click', '.reel-card.active', function(e) {
                e.preventDefault();
                var reelUrl = $(this).data('reel-url');
                if (reelUrl && !reelUrl.includes('example') && reelUrl.trim() !== '') {
                    
                    // Convert Instagram reel URL to embed format
                    var embedUrl = reelUrl;
                    if (reelUrl.includes('/reel/')) {
                        // Extract reel ID and create embed URL
                        var reelId = reelUrl.split('/reel/')[1].split('/')[0].split('?')[0];
                        embedUrl = 'https://www.instagram.com/p/' + reelId + '/embed/';
                    } else if (reelUrl.includes('/p/')) {
                        embedUrl = reelUrl + (reelUrl.endsWith('/') ? '' : '/') + 'embed/';
                    }
                    
                    // Open modal
                    $('#reel-iframe').attr('src', embedUrl);
                    $('#instagram-reel-modal').show();
                    $('body').addClass('modal-open');
                }
            });
            
            // Close Instagram reel modal
            $('.reel-modal-close').on('click', function(e) {
                e.preventDefault();
                self.closeInstagramModal();
            });
            
            $('.reel-modal').on('click', function(e) {
                if (e.target === this) {
                    self.closeInstagramModal();
                }
            });
            
            // Close modal with escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#instagram-reel-modal').is(':visible')) {
                    self.closeInstagramModal();
                }
            });
        },
        
        // Switch social proof content without reloading
        switchSocialProofContent: function(selectedType) {
            // Hide all social proof sections
            $('.google-reviews-section, .youtube-video-section, .instagram-reviews-section').hide();
            
            // Show the selected section
            if (selectedType === 'google') {
                $('.google-reviews-section').show();
            } else if (selectedType === 'youtube') {
                $('.youtube-video-section').show();
            } else {
                $('.instagram-reviews-section').show();
            }
        },
        
        // Update YouTube embed without reloading
        updateYouTubeEmbed: function(videoUrl) {
            var videoId = '';
            if (videoUrl && videoUrl !== 'https://www.youtube.com/watch?v=EXAMPLE') {
                var matches = videoUrl.match(/[\\?\\&]v=([^\\?\\&]+)/);
                if (matches) {
                    videoId = matches[1];
                }
            }
            
            var $embedContainer = $('.youtube-embed');
            if (videoId) {
                $embedContainer.html('<iframe width="560" height="315" ' +
                    'src="https://www.youtube.com/embed/' + videoId + '?mute=1&controls=1" ' +
                    'title="YouTube video player" frameborder="0" ' +
                    'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" ' +
                    'allowfullscreen></iframe>');
            } else {
                $embedContainer.html('<p class="youtube-placeholder">Please enter a valid YouTube URL above.</p>');
            }
        },
        
        // Update reviews display without reloading
        updateReviewsDisplay: function(shortcode) {
            var $container = $('.reviews-display-container');
            if (shortcode && shortcode !== '[your_reviews_shortcode]' && shortcode.trim() !== '') {
                // Show loading message
                $container.html('<p style="text-align: center; color: rgba(255,255,255,0.8); font-style: italic;">Loading reviews...</p>');
                
                // Make AJAX call to render shortcode
                $.ajax({
                    url: dhe_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'dhe_render_shortcode',
                        nonce: dhe_ajax.nonce,
                        shortcode: shortcode
                    },
                    success: function(response) {
                        if (response.success) {
                            $container.html(response.data);
                        } else {
                            $container.html('<p class="reviews-placeholder">Error loading reviews: ' + (response.data || 'Unknown error') + '</p>');
                        }
                    },
                    error: function() {
                        $container.html('<p class="reviews-placeholder">Network error while loading reviews.</p>');
                    }
                });
            } else {
                $container.html('<p class="reviews-placeholder">Google Reviews will appear here when shortcode is added.</p>');
            }
        },
        
        // Close Instagram modal
        closeInstagramModal: function() {
            $('#instagram-reel-modal').hide();
            $('#reel-iframe').attr('src', '');
            $('body').removeClass('modal-open');
        },
        
        // Toggle preview mode
        togglePreviewMode: function() {
            var $body = $('body');
            var $toggleBtn = $('#dhe-preview-toggle');
            var $toolbarTitle = $('.dhe-toolbar-title');
            
            if ($body.hasClass('dhe-preview-mode')) {
                // Exit preview mode
                $body.removeClass('dhe-preview-mode');
                $toggleBtn.removeClass('active').text('👁️ Preview Mode');
                $toolbarTitle.text('✏️ Editing Mode Active');
                this.showStatus('Editing mode activated', 'info');
            } else {
                // Enter preview mode
                $body.addClass('dhe-preview-mode');
                $toggleBtn.addClass('active').text('✏️ Edit Mode');
                $toolbarTitle.text('Preview Mode Active');
                this.showStatus('Preview mode activated - editing disabled', 'info');
            }
        },
        
        // Force update content with defaults (for debugging materials issue)
        forceUpdateContent: function() {
            var self = this;
            
            if (!confirm('This will reset all content to defaults including materials 7-9. Continue?')) {
                return;
            }
            
            this.showStatus('Updating content with defaults...', 'info');
            
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_force_update_content',
                    nonce: dhe_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.showStatus('Content updated! Reloading page...', 'success');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        self.showStatus('Failed to update content: ' + (response.data || 'Unknown error'), 'error');
                    }
                },
                error: function() {
                    self.showStatus('Network error while updating content', 'error');
                }
            });
        },
        
        // Validate Instagram URL
        isValidInstagramUrl: function(url) {
            var instagramPattern = /(?:https?:\/\/)?(?:www\.)?instagram\.com\/(?:p|reel)\/([A-Za-z0-9_-]+)/;
            return instagramPattern.test(url);
        },
        
        // Clear old Instagram data (manual thumbnails/titles) to prevent conflicts
        clearOldInstagramData: function(reelIndex) {
            var self = this;
            
            // Clear old manual thumbnail and title data
            var oldKeys = [
                'instagram_reel' + reelIndex + '_thumbnail',  // Old manual thumbnail key
                'instagram_reel' + reelIndex + '_title'       // Old manual title key
            ];
            
            oldKeys.forEach(function(key) {
                self.saveContent(key, '');
            });
        },
        
        
        // Update Instagram reel display
        updateInstagramReelDisplay: function(reelIndex, url, thumbnail) {
            var $reelCard = $('.reel-card[data-reel-index="' + reelIndex + '"]');
            
            if ($reelCard.length) {
                var $wrapper = $reelCard.find('.reel-thumbnail-wrapper');
                
                if (url && thumbnail && url.trim() !== '') {
                    // Update to active state with thumbnail
                    $reelCard.removeClass('placeholder').addClass('active');
                    $reelCard.attr('data-reel-url', url);
                    
                    // Create the active content
                    var activeHtml = 
                        '<img src="' + thumbnail + '" alt="Instagram Reel ' + reelIndex + '" class="reel-thumbnail" loading="lazy">' +
                        '<div class="reel-play-button">' +
                            '<svg viewBox="0 0 24 24" width="48" height="48">' +
                                '<path fill="white" d="M8 5v14l11-7z"/>' +
                            '</svg>' +
                        '</div>' +
                        '<div class="reel-overlay">' +
                            '<span class="reel-label">Customer Review</span>' +
                        '</div>';
                    
                    $wrapper.html(activeHtml);
                } else {
                    // Update to placeholder state
                    $reelCard.removeClass('active').addClass('placeholder');
                    $reelCard.attr('data-reel-url', '');
                    
                    // Create the placeholder content
                    var placeholderHtml = 
                        '<div class="reel-placeholder">' +
                            '<div class="placeholder-icon">📱</div>' +
                            '<span>Add Instagram Reel URL</span>' +
                        '</div>';
                    
                    $wrapper.html(placeholderHtml);
                }
            }
        },
        
        // Test Instagram API connection
        
        // Diagnose template issues
        diagnoseTemplate: function() {
            var self = this;
            
            this.showStatus('Diagnosing template issues...', 'info');
            
            // Disable diagnose button during process
            $('#dhe-diagnose-template').prop('disabled', true).text('🔍 Diagnosing...');
            
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_diagnose_template',
                    nonce: dhe_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        var statusMessage = 'Template Diagnosis Results:\\n\\n';
                        
                        // Plugin template status
                        statusMessage += 'Plugin Template:\\n';
                        statusMessage += '  ✓ Exists: ' + (data.plugin_template.exists ? 'YES' : 'NO') + '\\n';
                        statusMessage += '  ✓ Readable: ' + (data.plugin_template.readable ? 'YES' : 'NO') + '\\n';
                        statusMessage += '  ✓ Size: ' + data.plugin_template.size + ' bytes\\n';
                        statusMessage += '  ✓ Modified: ' + data.plugin_template.modified + '\\n\\n';
                        
                        // Theme template status
                        statusMessage += 'Theme Template:\\n';
                        statusMessage += '  ✓ Exists: ' + (data.theme_template.exists ? 'YES' : 'NO') + '\\n';
                        statusMessage += '  ✓ Readable: ' + (data.theme_template.readable ? 'YES' : 'NO') + '\\n';
                        statusMessage += '  ✓ Size: ' + data.theme_template.size + ' bytes\\n';
                        statusMessage += '  ✓ Modified: ' + data.theme_template.modified + '\\n\\n';
                        
                        // Validation status
                        statusMessage += 'Validation:\\n';
                        statusMessage += '  ✓ Valid: ' + (data.validation.valid ? 'YES' : 'NO') + '\\n';
                        if (!data.validation.valid) {
                            statusMessage += '  ⚠️ Error: ' + data.validation.error + '\\n';
                            statusMessage += '  🔧 Fix: ' + data.validation.fix + '\\n';
                        }
                        
                        // Theme directory status
                        statusMessage += '\\nTheme Directory:\\n';
                        statusMessage += '  ✓ Writable: ' + (data.theme_directory.writable ? 'YES' : 'NO') + '\\n';
                        statusMessage += '  ✓ Readable: ' + (data.theme_directory.readable ? 'YES' : 'NO') + '\\n';
                        
                        // Copy status
                        if (data.copy_attempted) {
                            statusMessage += '\\nTemplate Copy:\\n';
                            statusMessage += '  ✓ Attempted: YES\\n';
                            statusMessage += '  ✓ Success: ' + (data.copy_success ? 'YES' : 'NO') + '\\n';
                        }
                        
                        // Show results
                        if (data.validation.valid && data.theme_template.exists) {
                            self.showStatus('Template diagnosis complete - all systems operational!', 'success');
                            alert(statusMessage + '\\n🎉 Template is working correctly!');
                        } else {
                            self.showStatus('Template diagnosis complete - issues detected', 'warning');
                            alert(statusMessage + '\\n⚠️ Template has issues. Check details above.');
                        }
                        
                        // Log detailed debug info to console
                        console.log('Template Diagnosis Results:', data);
                    } else {
                        self.showStatus('Template diagnosis failed: ' + response.data, 'error');
                        console.log('Template Diagnosis Error:', response.data);
                        alert('Template Diagnosis Failed:\\n\\n' + response.data + '\\n\\nCheck console for debug details.');
                    }
                },
                error: function(xhr, status, error) {
                    self.showStatus('Network error during template diagnosis', 'error');
                    console.log('Template Diagnosis Network Error:', {xhr: xhr, status: status, error: error});
                    alert('Network error during template diagnosis. Check console for details.');
                },
                complete: function() {
                    // Re-enable diagnose button
                    $('#dhe-diagnose-template').prop('disabled', false).text('🔍 Diagnose Template');
                }
            });
        },
        
        // Open thumbnail upload for Instagram reels
        openThumbnailUpload: function(contentKey, reelIndex) {
            var self = this;
            
            // Check if wp.media exists
            if (typeof wp === 'undefined' || !wp.media) {
                this.showStatus('Media library not available', 'error');
                return;
            }
            
            // Create media frame
            var mediaFrame = wp.media({
                title: 'Select Thumbnail for Reel ' + reelIndex,
                button: {
                    text: 'Use This Thumbnail'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            // Handle selection
            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                
                if (attachment && attachment.url) {
                    // Save thumbnail URL
                    self.saveContent(contentKey, attachment.url);
                    self.showStatus('Thumbnail updated! Reload page to see changes.', 'success');
                    
                    // Update the current thumbnail preview immediately
                    var $uploadArea = $('[data-dhe-content-key="' + contentKey + '"]').closest('.reel-editor-card').find('.thumbnail-upload-area');
                    var $currentImg = $uploadArea.find('.current-thumbnail');
                    
                    if ($currentImg.length) {
                        $currentImg.attr('src', attachment.url);
                    } else {
                        $uploadArea.prepend('<img src="' + attachment.url + '" class="current-thumbnail" alt="Current thumbnail">');
                    }
                    
                    // Update button text
                    $('[data-dhe-content-key="' + contentKey + '"]').text('📷 Change Thumbnail');
                }
            });
            
            // Open media frame
            mediaFrame.open();
        },
        
        // Page Management Functions
        setupPageManagement: function() {
            var self = this;
            
            // Save As New Page button
            $('#dhe-save-as-btn').on('click', function(e) {
                e.preventDefault();
                self.showPageModal('save-as', 'Save As New Page');
            });
            
            // Duplicate Page button
            $('#dhe-duplicate-btn').on('click', function(e) {
                e.preventDefault();
                self.showPageModal('duplicate', 'Duplicate Current Page');
            });
            
            // Modal functionality
            $('.dhe-modal-close, #dhe-modal-cancel').on('click', function() {
                self.hidePageModal();
            });
            
            // Modal confirm button
            $('#dhe-modal-confirm').on('click', function() {
                var action = $('#dhe-page-modal').data('action');
                var pageName = $('#dhe-page-name').val().trim();
                
                if (!pageName) {
                    alert('Please enter a page name');
                    return;
                }
                
                if (action === 'save-as') {
                    self.saveAsNewPage(pageName);
                } else if (action === 'duplicate') {
                    self.duplicatePage(pageName);
                }
            });
            
            // Enter key in modal
            $('#dhe-page-name').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    $('#dhe-modal-confirm').click();
                } else if (e.key === 'Escape') {
                    self.hidePageModal();
                }
            });
            
            // Close modal on outside click
            $('#dhe-page-modal').on('click', function(e) {
                if (e.target === this) {
                    self.hidePageModal();
                }
            });
        },
        
        showPageModal: function(action, title) {
            $('#dhe-modal-title').text(title);
            $('#dhe-page-modal').data('action', action).show();
            $('#dhe-page-name').focus();
        },
        
        hidePageModal: function() {
            $('#dhe-page-modal').hide();
            $('#dhe-page-name').val('');
        },
        
        saveAsNewPage: function(pageName) {
            var self = this;
            var $button = $('#dhe-modal-confirm');
            $button.prop('disabled', true).text('Creating WordPress Page...');
            
            // Collect all current edited content for template-based cloning
            var contentData = {};
            
            // Get all edited text content
            $('.dhe-editable-text').each(function() {
                var key = $(this).data('dhe-content-key');
                if (key) {
                    contentData[key] = $(this).text().trim();
                }
            });
            
            // Get all image URLs
            $('.dhe-editable-image').each(function() {
                var key = $(this).data('dhe-content-key');
                if (key) {
                    contentData[key] = $(this).attr('src');
                }
            });
            
            // Get social proof type
            var socialType = $('input[name="social_proof_type"]:checked').val() || 'instagram';
            contentData['social_proof_type'] = socialType;
            
            // Get shortcode if Google Reviews selected
            if (socialType === 'google') {
                contentData['google_reviews_shortcode'] = $('.dhe-shortcode-input').val();
            }
            
            // Get YouTube URL if YouTube selected
            if (socialType === 'youtube') {
                contentData['youtube_video_url'] = $('input[data-dhe-content-key="youtube_video_url"]').val();
                contentData['youtube_video_title'] = $('.youtube-video-title').text().trim();
                contentData['youtube_video_description'] = $('.youtube-video-description').text().trim();
            }
            
            // Get Instagram content
            if (socialType === 'instagram') {
                for (var i = 1; i <= 3; i++) {
                    var urlKey = 'instagram_reel' + i + '_url';
                    var titleKey = 'instagram_reel' + i + '_title';
                    var descKey = 'instagram_reel' + i + '_description';
                    var thumbKey = 'instagram_reel' + i + '_thumbnail';
                    
                    var urlInput = $('input[data-dhe-content-key="' + urlKey + '"]');
                    var titleEl = $('.dhe-editable-text[data-dhe-content-key="' + titleKey + '"]');
                    var descEl = $('.dhe-editable-text[data-dhe-content-key="' + descKey + '"]');
                    var thumbImg = $('img[data-dhe-content-key="' + thumbKey + '"]');
                    
                    if (urlInput.length) contentData[urlKey] = urlInput.val();
                    if (titleEl.length) contentData[titleKey] = titleEl.text().trim();
                    if (descEl.length) contentData[descKey] = descEl.text().trim();
                    if (thumbImg.length) contentData[thumbKey] = thumbImg.attr('src');
                }
            }
            
            // Get page status
            var pageStatus = $('#dhe-page-status').val() || 'draft';
            
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_save_as_new_page',
                    page_title: pageName,
                    variant_name: pageName ? pageName.toLowerCase().replace(/\s+/g, '-') : '',
                    source_variant: dhe_ajax.current_variant || 'default',
                    content: contentData,
                    status: pageStatus,
                    nonce: dhe_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.hidePageModal();
                        
                        // Show success modal with links
                        self.showSuccessModal(response.data);
                    } else {
                        self.showStatus('Error: ' + response.data, 'error');
                        $button.prop('disabled', false).text('Create Page');
                    }
                },
                error: function() {
                    self.showStatus('Failed to create page. Please try again.', 'error');
                    $button.prop('disabled', false).text('Create Page');
                }
            });
        },
        
        showSuccessModal: function(data) {
            var self = this;
            
            // Create success modal if it doesn't exist
            if ($('#dhe-success-modal').length === 0) {
                var modalHtml = `
                    <div id="dhe-success-modal" class="dhe-modal">
                        <div class="dhe-modal-content" style="max-width: 500px;">
                            <span class="dhe-modal-close">&times;</span>
                            <h2 style="color: #28a745; margin-bottom: 20px;">✅ Page Created Successfully!</h2>
                            <div id="dhe-success-content"></div>
                        </div>
                    </div>
                `;
                $('body').append(modalHtml);
                
                // Close modal handler
                $('#dhe-success-modal .dhe-modal-close').on('click', function() {
                    $('#dhe-success-modal').hide();
                });
            }
            
            // Build success content
            var statusBadge = '';
            if (data.status === 'publish') {
                statusBadge = '<span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 4px;">Published</span>';
            } else if (data.status === 'draft') {
                statusBadge = '<span style="background: #ffc107; color: #333; padding: 2px 8px; border-radius: 4px;">Draft</span>';
            } else {
                statusBadge = '<span style="background: #6c757d; color: white; padding: 2px 8px; border-radius: 4px;">Private</span>';
            }
            
            var contentHtml = `
                <p><strong>Page Title:</strong> ${data.page_title}</p>
                <p><strong>Status:</strong> ${statusBadge}</p>
                <p><strong>Page ID:</strong> #${data.page_id}</p>
                <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="${data.view_url}" target="_blank" class="dhe-btn dhe-btn-primary">
                        👁️ View Page
                    </a>
                    <a href="${data.edit_url}" target="_blank" class="dhe-btn dhe-btn-secondary">
                        ✏️ Edit in WordPress
                    </a>
                    <button id="dhe-stay-here" class="dhe-btn dhe-btn-warning">
                        🔙 Stay on Editor
                    </button>
                </div>
                <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                    <small>💡 Tip: ${data.status === 'publish' ? 'Your page is now live!' : data.status === 'draft' ? 'Your page has been saved as a draft. You can publish it from the WordPress admin.' : 'Your page is private and only visible to administrators.'}</small>
                </div>
            `;
            
            $('#dhe-success-content').html(contentHtml);
            $('#dhe-success-modal').show();
            
            // Stay here button handler
            $('#dhe-stay-here').on('click', function() {
                $('#dhe-success-modal').hide();
                self.showStatus('Page created! Continue editing or create another page.', 'success');
            });
        },
        
        duplicatePage: function(pageName) {
            var self = this;
            var $button = $('#dhe-modal-confirm');
            $button.prop('disabled', true).text('Duplicating...');
            
            $.ajax({
                url: dhe_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dhe_duplicate_page',
                    variant_name: pageName,
                    source_variant: dhe_ajax.current_variant || 'default',
                    nonce: dhe_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.hidePageModal();
                        self.showStatus('Page duplicated successfully!', 'success');
                        
                        // Redirect to new page
                        setTimeout(function() {
                            window.location.href = response.data.new_url;
                        }, 1000);
                    } else {
                        self.showStatus('Error: ' + response.data, 'error');
                        $button.prop('disabled', false).text('Duplicate Page');
                    }
                },
                error: function() {
                    self.showStatus('Failed to duplicate page. Please try again.', 'error');
                    $button.prop('disabled', false).text('Duplicate Page');
                }
            });
        }
    };
    
    // Global function for thumbnail overlay clicks
    window.openThumbnailUpload = function(reelIndex) {
        var contentKey = 'instagram_reel' + reelIndex + '_thumbnail';
        if (window.DHEEditor) {
            window.DHEEditor.openThumbnailUpload(contentKey, reelIndex);
        }
    };
    
    // Initialize the editor
    DHEEditor.init();
    
    // Make DHEEditor globally accessible
    window.DHEEditor = DHEEditor;
    
    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        var changesCount = Object.keys(DHEEditor.pendingChanges || {}).length;
        if (changesCount > 0) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    console.log('🎉 DHE Editor v2.0 initialized successfully');
    console.log('✅ Features loaded: Text editing, Image uploads, Instagram embeds, Thumbnail uploads, Auto-save');
    
    // Production ready check
    if (typeof wp !== 'undefined' && wp.media) {
        console.log('✅ WordPress Media Library available');
    } else {
        console.warn('⚠️ WordPress Media Library not available - image uploads may not work');
    }
    
    if (typeof $ !== 'undefined') {
        console.log('✅ jQuery loaded');
    } else {
        console.error('❌ jQuery not loaded - editor may not function properly');
    }
    
    // Check if we're in editing mode
    if (dhe_ajax && dhe_ajax.editing_mode) {
        console.log('✅ Editing mode active');
    } else {
        console.log('👁️ Viewing mode - editing disabled');
    }
});