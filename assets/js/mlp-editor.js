/**
 * MLP (Modern Landing Page) Editor
 * Comprehensive inline landing page editor with state management
 * 
 * Features:
 * - Click-to-edit anywhere with instant feedback
 * - State management with operation logging
 * - Autosave and undo/redo
 * - Media integration and content sanitization
 * - Accessibility and keyboard navigation
 */

(function() {
    'use strict';

    // Editor State Management
    class EditorState {
        constructor() {
            this.meta = {
                title: '',
                slug: '',
                status: 'draft',
                template_key: 'hardscape'
            };
            this.nodes = {};
            this.bottom = {
                mode: 'instagram',
                data: {}
            };
            this.ops = [];
            this.version = 1;
        }

        // Apply an edit and log the operation
        applyEdit(id, next) {
            const before = this.nodes[id];
            this.nodes[id] = next;
            
            const op = {
                id: this.generateOpId(),
                nodeId: id,
                before: before,
                after: next,
                ts: Date.now()
            };
            
            this.ops.push(op);
            return op;
        }

        // Undo last operation
        undo() {
            if (this.ops.length === 0) return null;
            
            const op = this.ops.pop();
            this.nodes[op.nodeId] = op.before;
            return op;
        }

        // Redo last undone operation
        redo() {
            // Implementation for redo stack
            return null;
        }

        // Generate unique operation ID
        generateOpId() {
            return 'op_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        // Export state for saving
        export() {
            return {
                meta: this.meta,
                nodes: this.nodes,
                bottom: this.bottom,
                version: this.version
            };
        }

        // Import state from saved data
        import(data) {
            this.meta = data.meta || this.meta;
            this.nodes = data.nodes || {};
            this.bottom = data.bottom || { mode: 'instagram', data: {} };
            this.version = data.version || 1;
            this.ops = []; // Clear operation log on import
        }
    }

    // Node Value Types
    const NodeValueTypes = {
        TEXT: 'text',
        HTML: 'html',
        IMAGE: 'image',
        LINK: 'link',
        SHORTCODE: 'shortcode',
        EMBED: 'embed'
    };

    // Image Specification
    class ImageSpec {
        static fromAttachmentId(id) {
            return { type: 'attachment_id', value: id };
        }

        static fromExternalUrl(url) {
            return { type: 'external_url', value: url };
        }

        static fromDataUrl(dataUrl) {
            return { type: 'data_url', value: dataUrl };
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

    // Text Editor Implementation
    function TextEditor(node, id, state) {
        const originalContent = node.textContent.trim();
        let isEditing = false;

        function startEdit() {
            if (isEditing) return;
            
            isEditing = true;
            node.contentEditable = true;
            node.focus();
            
            // Select all text
            const range = document.createRange();
            range.selectNodeContents(node);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            
            // Add editing class
            node.classList.add('mlp-editing');
            
            // Create inline toolbar
            createToolbar();
        }

        function createToolbar() {
            const toolbar = document.createElement('div');
            toolbar.className = 'mlp-text-toolbar';
            toolbar.innerHTML = `
                <button class="mlp-btn mlp-btn-save" title="Save (Enter)">✓</button>
                <button class="mlp-btn mlp-btn-cancel" title="Cancel (Esc)">✕</button>
            `;
            
            toolbar.querySelector('.mlp-btn-save').onclick = saveEdit;
            toolbar.querySelector('.mlp-btn-cancel').onclick = cancelEdit;
            
            node.parentNode.insertBefore(toolbar, node.nextSibling);
        }

        function saveEdit() {
            const newContent = node.textContent.trim();
            
            if (newContent !== originalContent) {
                const nodeValue = { kind: NodeValueTypes.TEXT, value: newContent };
                state.applyEdit(id, nodeValue);
                MLPEditor.patchDom(id, nodeValue);
                MLPEditor.scheduleAutosave();
                MLPEditor.showToast('Text updated');
            }
            
            endEdit();
        }

        function cancelEdit() {
            node.textContent = originalContent;
            endEdit();
        }

        function endEdit() {
            isEditing = false;
            node.contentEditable = false;
            node.classList.remove('mlp-editing');
            
            // Remove toolbar
            const toolbar = node.parentNode.querySelector('.mlp-text-toolbar');
            if (toolbar) toolbar.remove();
        }

        // Keyboard shortcuts
        node.addEventListener('keydown', function(e) {
            if (!isEditing) return;
            
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                saveEdit();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEdit();
            }
        });

        return {
            startEdit,
            saveEdit,
            cancelEdit,
            endEdit
        };
    }

    // Rich Text Editor Implementation
    function RichTextEditor(node, id, state) {
        const originalContent = node.innerHTML;
        let isEditing = false;

        function startEdit() {
            if (isEditing) return;
            
            isEditing = true;
            node.contentEditable = true;
            node.focus();
            node.classList.add('mlp-editing');
            
            createRichToolbar();
        }

        function createRichToolbar() {
            const toolbar = document.createElement('div');
            toolbar.className = 'mlp-richtext-toolbar';
            toolbar.innerHTML = `
                <button class="mlp-btn mlp-btn-bold" title="Bold (Ctrl+B)">B</button>
                <button class="mlp-btn mlp-btn-italic" title="Italic (Ctrl+I)">I</button>
                <button class="mlp-btn mlp-btn-link" title="Add Link">🔗</button>
                <button class="mlp-btn mlp-btn-list" title="Add List">📝</button>
                <button class="mlp-btn mlp-btn-save" title="Save (Enter)">✓</button>
                <button class="mlp-btn mlp-btn-cancel" title="Cancel (Esc)">✕</button>
            `;
            
            // Add event listeners
            toolbar.querySelector('.mlp-btn-bold').onclick = () => document.execCommand('bold');
            toolbar.querySelector('.mlp-btn-italic').onclick = () => document.execCommand('italic');
            toolbar.querySelector('.mlp-btn-link').onclick = addLink;
            toolbar.querySelector('.mlp-btn-list').onclick = addList;
            toolbar.querySelector('.mlp-btn-save').onclick = saveEdit;
            toolbar.querySelector('.mlp-btn-cancel').onclick = cancelEdit;
            
            node.parentNode.insertBefore(toolbar, node.nextSibling);
        }

        function addLink() {
            const url = prompt('Enter URL:');
            if (url) {
                document.execCommand('createLink', false, url);
            }
        }

        function addList() {
            document.execCommand('insertUnorderedList');
        }

        function saveEdit() {
            const newContent = sanitizeHtml(node.innerHTML);
            
            if (newContent !== originalContent) {
                const nodeValue = { kind: NodeValueTypes.HTML, value: newContent };
                state.applyEdit(id, nodeValue);
                MLPEditor.patchDom(id, nodeValue);
                MLPEditor.scheduleAutosave();
                MLPEditor.showToast('Content updated');
            }
            
            endEdit();
        }

        function cancelEdit() {
            node.innerHTML = originalContent;
            endEdit();
        }

        function endEdit() {
            isEditing = false;
            node.contentEditable = false;
            node.classList.remove('mlp-editing');
            
            const toolbar = node.parentNode.querySelector('.mlp-richtext-toolbar');
            if (toolbar) toolbar.remove();
        }

        // Keyboard shortcuts
        node.addEventListener('keydown', function(e) {
            if (!isEditing) return;
            
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                saveEdit();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEdit();
            }
        });

        return {
            startEdit,
            saveEdit,
            cancelEdit,
            endEdit
        };
    }

    // Image Editor Implementation
    function ImageEditor(node, id, state) {
        function startEdit() {
            // Open WordPress Media Modal
            if (typeof wp !== 'undefined' && wp.media) {
                const frame = wp.media({
                    title: 'Select Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    const imageSpec = ImageSpec.fromAttachmentId(attachment.id);
                    const nodeValue = { kind: NodeValueTypes.IMAGE, value: imageSpec };
                    
                    state.applyEdit(id, nodeValue);
                    MLPEditor.patchDom(id, nodeValue);
                    MLPEditor.scheduleAutosave();
                    MLPEditor.showToast('Image updated');
                });

                frame.open();
            } else {
                // Fallback to file input
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.onchange = handleFileSelect;
                input.click();
            }
        }

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const dataUrl = e.target.result;
                const imageSpec = ImageSpec.fromDataUrl(dataUrl);
                const nodeValue = { kind: NodeValueTypes.IMAGE, value: imageSpec };
                
                state.applyEdit(id, nodeValue);
                MLPEditor.patchDom(id, nodeValue);
                MLPEditor.scheduleAutosave();
                MLPEditor.showToast('Image updated');
            };
            reader.readAsDataURL(file);
        }

        return { startEdit };
    }

    // Background Image Editor Implementation
    function BgImageEditor(node, id, state) {
        function startEdit() {
            if (typeof wp !== 'undefined' && wp.media) {
                const frame = wp.media({
                    title: 'Select Background Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    const imageSpec = ImageSpec.fromAttachmentId(attachment.id);
                    const nodeValue = { kind: NodeValueTypes.IMAGE, value: imageSpec };
                    
                    state.applyEdit(id, nodeValue);
                    MLPEditor.patchDom(id, nodeValue);
                    MLPEditor.scheduleAutosave();
                    MLPEditor.showToast('Background image updated');
                });

                frame.open();
            }
        }

        return { startEdit };
    }

    // Link Editor Implementation
    function LinkEditor(node, id, state) {
        function startEdit() {
            const currentHref = node.href || '';
            const currentTarget = node.target || '_self';
            
            const url = prompt('Enter URL:', currentHref);
            if (url === null) return; // User cancelled
            
            const target = confirm('Open in new tab?') ? '_blank' : '_self';
            
            const nodeValue = {
                kind: NodeValueTypes.LINK,
                href: url,
                target: target
            };
            
            state.applyEdit(id, nodeValue);
            MLPEditor.patchDom(id, nodeValue);
            MLPEditor.scheduleAutosave();
            MLPEditor.showToast('Link updated');
        }

        return { startEdit };
    }

    // Shortcode Editor Implementation
    function ShortcodeEditor(node, id, state) {
        function startEdit() {
            const currentValue = node.textContent.trim();
            const newValue = prompt('Enter shortcode:', currentValue);
            
            if (newValue === null) return; // User cancelled
            
            // Validate shortcode
            if (!isValidShortcode(newValue)) {
                alert('Invalid shortcode. Please use a supported shortcode.');
                return;
            }
            
            const nodeValue = { kind: NodeValueTypes.SHORTCODE, value: newValue };
            state.applyEdit(id, nodeValue);
            MLPEditor.patchDom(id, nodeValue);
            MLPEditor.scheduleAutosave();
            MLPEditor.showToast('Shortcode updated');
        }

        function isValidShortcode(shortcode) {
            const allowedShortcodes = ['trustindex', 'google-reviews-pro', 'youtube', 'instagram'];
            return allowedShortcodes.some(allowed => shortcode.includes(allowed));
        }

        return { startEdit };
    }

    // Embed Editor Implementation
    function EmbedEditor(node, id, state) {
        function startEdit() {
            const currentUrl = node.dataset.embedUrl || '';
            const url = prompt('Enter embed URL (YouTube, Instagram):', currentUrl);
            
            if (url === null) return; // User cancelled
            
            const embedInfo = parseEmbedUrl(url);
            if (!embedInfo) {
                alert('Invalid embed URL. Please use YouTube or Instagram URLs.');
                return;
            }
            
            const nodeValue = {
                kind: NodeValueTypes.EMBED,
                provider: embedInfo.provider,
                url: url,
                meta: embedInfo.meta
            };
            
            state.applyEdit(id, nodeValue);
            MLPEditor.patchDom(id, nodeValue);
            MLPEditor.scheduleAutosave();
            MLPEditor.showToast('Embed updated');
        }

        function parseEmbedUrl(url) {
            // YouTube
            const youtubeMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
            if (youtubeMatch) {
                return {
                    provider: 'youtube',
                    meta: { videoId: youtubeMatch[1] }
                };
            }
            
            // Instagram
            const instagramMatch = url.match(/instagram\.com\/(?:p|reel)\/([a-zA-Z0-9_-]+)/);
            if (instagramMatch) {
                return {
                    provider: 'instagram',
                    meta: { postId: instagramMatch[1] }
                };
            }
            
            return null;
        }

        return { startEdit };
    }

    // Main Editor Class
    class MLPEditor {
        constructor(config) {
            this.config = config;
            this.state = new EditorState();
            this.autosaveTimer = null;
            this.currentEditor = null;
            
            this.init();
        }

        init() {
            this.setupEventDelegation();
            this.setupKeyboardShortcuts();
            this.setupAutosave();
            this.loadState();
            this.addEditingIndicators();
        }

        setupEventDelegation() {
            document.addEventListener('click', (e) => {
                const target = e.target.closest('[data-mlp-id]');
                if (!target) return;
                
                const id = target.getAttribute('data-mlp-id');
                const type = target.getAttribute('data-mlp-type');
                
                if (!id || !type) return;
                
                // Close current editor if different
                if (this.currentEditor && this.currentEditor.node !== target) {
                    this.currentEditor.endEdit();
                }
                
                // Open appropriate editor
                const EditorClass = Editors[type];
                if (EditorClass) {
                    this.currentEditor = EditorClass(target, id, this.state);
                    this.currentEditor.startEdit();
                }
            });
        }

        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Undo/Redo
                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    this.undo();
                } else if ((e.ctrlKey || e.metaKey) && e.key === 'z' && e.shiftKey) {
                    e.preventDefault();
                    this.redo();
                }
                
                // Save
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    this.save();
                }
            });
        }

        setupAutosave() {
            // Autosave every 3 seconds when there are changes
            setInterval(() => {
                if (this.state.ops.length > 0) {
                    this.autosave();
                }
            }, 3000);
        }

        scheduleAutosave() {
            if (this.autosaveTimer) {
                clearTimeout(this.autosaveTimer);
            }
            
            this.autosaveTimer = setTimeout(() => {
                this.autosave();
            }, 2000);
        }

        autosave() {
            const data = this.state.export();
            const key = `mlp_autosave_${this.config.pageId || 'draft'}`;
            
            try {
                localStorage.setItem(key, JSON.stringify({
                    data: data,
                    timestamp: Date.now()
                }));
            } catch (e) {
                console.warn('Autosave failed:', e);
            }
        }

        loadState() {
            const key = `mlp_autosave_${this.config.pageId || 'draft'}`;
            
            try {
                const saved = localStorage.getItem(key);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    this.state.import(parsed.data);
                    this.applyStateToDom();
                }
            } catch (e) {
                console.warn('Failed to load autosave:', e);
            }
        }

        applyStateToDom() {
            Object.entries(this.state.nodes).forEach(([id, nodeValue]) => {
                this.patchDom(id, nodeValue);
            });
        }

        patchDom(id, nodeValue) {
            const node = document.querySelector(`[data-mlp-id="${id}"]`);
            if (!node) return;

            switch (nodeValue.kind) {
                case NodeValueTypes.TEXT:
                    node.textContent = nodeValue.value;
                    break;
                    
                case NodeValueTypes.HTML:
                    node.innerHTML = nodeValue.value;
                    break;
                    
                case NodeValueTypes.IMAGE:
                    if (nodeValue.value.type === 'attachment_id') {
                        // Get attachment URL from WordPress
                        this.getAttachmentUrl(nodeValue.value.value).then(url => {
                            if (node.tagName === 'IMG') {
                                node.src = url;
                            } else {
                                node.style.backgroundImage = `url(${url})`;
                            }
                        });
                    } else {
                        const url = nodeValue.value.value;
                        if (node.tagName === 'IMG') {
                            node.src = url;
                        } else {
                            node.style.backgroundImage = `url(${url})`;
                        }
                    }
                    break;
                    
                case NodeValueTypes.LINK:
                    node.href = nodeValue.href;
                    node.target = nodeValue.target;
                    break;
                    
                case NodeValueTypes.SHORTCODE:
                    // Render shortcode on server side
                    this.renderShortcode(nodeValue.value).then(html => {
                        node.innerHTML = html;
                    });
                    break;
                    
                case NodeValueTypes.EMBED:
                    this.renderEmbed(nodeValue).then(html => {
                        node.innerHTML = html;
                    });
                    break;
            }
        }

        async getAttachmentUrl(attachmentId) {
            try {
                const response = await fetch(this.config.rest.getAttachment, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.config.nonce
                    },
                    body: JSON.stringify({ attachment_id: attachmentId })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    return data.url;
                }
            } catch (e) {
                console.warn('Failed to get attachment URL:', e);
            }
            
            return '';
        }

        async renderShortcode(shortcode) {
            try {
                const response = await fetch(this.config.rest.renderShortcode, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.config.nonce
                    },
                    body: JSON.stringify({ shortcode: shortcode })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    return data.html;
                }
            } catch (e) {
                console.warn('Failed to render shortcode:', e);
            }
            
            return shortcode;
        }

        async renderEmbed(embedValue) {
            try {
                const response = await fetch(this.config.rest.renderEmbed, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.config.nonce
                    },
                    body: JSON.stringify(embedValue)
                });
                
                if (response.ok) {
                    const data = await response.json();
                    return data.html;
                }
            } catch (e) {
                console.warn('Failed to render embed:', e);
            }
            
            return `<p>Embed: ${embedValue.url}</p>`;
        }

        addEditingIndicators() {
            document.querySelectorAll('[data-mlp-id]').forEach(node => {
                node.classList.add('mlp-editable');
                node.setAttribute('tabindex', '0');
                node.setAttribute('role', 'textbox');
                
                if (node.getAttribute('data-mlp-type') === 'richtext') {
                    node.setAttribute('aria-multiline', 'true');
                }
            });
        }

        undo() {
            const op = this.state.undo();
            if (op) {
                this.patchDom(op.nodeId, op.before);
                this.showToast('Undone');
            }
        }

        redo() {
            const op = this.state.redo();
            if (op) {
                this.patchDom(op.nodeId, op.after);
                this.showToast('Redone');
            }
        }

        async save() {
            try {
                const response = await fetch(this.config.rest.saveDraft, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.config.nonce
                    },
                    body: JSON.stringify(this.state.export())
                });
                
                if (response.ok) {
                    this.showToast('Saved successfully');
                    this.state.ops = []; // Clear operation log
                } else {
                    throw new Error(await response.text());
                }
            } catch (e) {
                this.showToast('Save failed: ' + e.message, 'error');
            }
        }

        async saveAsNewPage() {
            const pageTitle = prompt('Enter page title:');
            if (!pageTitle) return;
            
            try {
                // Convert MLP state to content data format
                const contentData = this.convertStateToContentData();
                
                // Use the existing AJAX method
                const formData = new FormData();
                formData.append('action', 'dhe_save_as_new_page');
                formData.append('page_title', pageTitle);
                formData.append('status', 'draft');
                formData.append('nonce', dhe_ajax.nonce);
                
                // Add content data
                Object.keys(contentData).forEach(key => {
                    formData.append('content[' + key + ']', contentData[key]);
                });
                
                const response = await fetch(dhe_ajax.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.showToast('Page created successfully!');
                        this.showSuccessModal(data.data);
                    } else {
                        throw new Error(data.data || 'Failed to create page');
                    }
                } else {
                    throw new Error('Network error');
                }
            } catch (e) {
                this.showToast('Failed to create page: ' + e.message, 'error');
            }
        }
        
        convertStateToContentData() {
            const contentData = {};
            
            // Convert MLP state nodes to content data format
            Object.keys(this.state.nodes).forEach(nodeId => {
                const node = this.state.nodes[nodeId];
                
                switch (node.type) {
                    case 'text':
                        contentData[nodeId] = node.value;
                        break;
                    case 'richtext':
                        contentData[nodeId] = node.value;
                        break;
                    case 'image':
                        contentData[nodeId] = node.src;
                        break;
                    case 'bg-image':
                        contentData[nodeId] = node.src;
                        break;
                    case 'link':
                        contentData[nodeId + '_url'] = node.href;
                        contentData[nodeId + '_target'] = node.target || '_self';
                        break;
                    case 'shortcode':
                        contentData[nodeId] = node.value;
                        break;
                    case 'embed':
                        contentData[nodeId + '_url'] = node.url;
                        contentData[nodeId + '_provider'] = node.provider;
                        break;
                }
            });
            
            // Add bottom section data
            if (this.state.bottom.mode) {
                contentData['social_proof_type'] = this.state.bottom.mode;
            }
            
            return contentData;
        }

        generateSlug(title) {
            return title.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `mlp-toast mlp-toast-${type}`;
            toast.textContent = message;
            toast.setAttribute('aria-live', 'polite');
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('mlp-toast-show');
            }, 100);
            
            setTimeout(() => {
                toast.classList.remove('mlp-toast-show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        showSuccessModal(data) {
            const modal = document.createElement('div');
            modal.className = 'mlp-success-modal';
            modal.innerHTML = `
                <div class="mlp-modal-content">
                    <h3>Page Created Successfully!</h3>
                    <p>Your new page has been created and is ready to view.</p>
                    <div class="mlp-modal-actions">
                        <a href="${data.view_url}" class="mlp-btn mlp-btn-primary" target="_blank">View Page</a>
                        <a href="${data.edit_url}" class="mlp-btn mlp-btn-secondary" target="_blank">Edit in WordPress</a>
                        <button class="mlp-btn mlp-btn-cancel" onclick="this.closest('.mlp-success-modal').remove()">Close</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }
    }

    // Utility Functions
    function sanitizeHtml(html) {
        // Basic HTML sanitization - in production, use DOMPurify or wp_kses_post equivalent
        const allowedTags = ['p', 'strong', 'em', 'ul', 'ol', 'li', 'br', 'a'];
        const div = document.createElement('div');
        div.innerHTML = html;
        
        // Remove disallowed tags
        div.querySelectorAll('*').forEach(el => {
            if (!allowedTags.includes(el.tagName.toLowerCase())) {
                el.outerHTML = el.innerHTML;
            }
        });
        
        return div.innerHTML;
    }

    // Initialize editor when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEditor);
    } else {
        initEditor();
    }

    function initEditor() {
        if (typeof MLP_CONFIG !== 'undefined') {
            window.mlpEditor = new MLPEditor(MLP_CONFIG);
        }
    }

    // Export for global access
    window.MLPEditor = MLPEditor;
    window.EditorState = EditorState;

})();
