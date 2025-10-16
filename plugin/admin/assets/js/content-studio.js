/**
 * ContentCraft AI - Content Studio JavaScript
 */

(function($) {
	'use strict';

	const ContentStudio = {
		currentContent: null,

		init: function() {
			this.bindEvents();
			this.initTabs();
		},

		bindEvents: function() {
			// Form submission
			$('#contentcraft-content-form').on('submit', this.handleGenerate.bind(this));
			
			// Regenerate button
			$('#regenerate-btn').on('click', this.handleRegenerate.bind(this));
			
			// Retry button (error state)
			$('#retry-btn').on('click', this.handleRetry.bind(this));
			
			// Insert to editor
			$('#insert-editor-btn').on('click', this.handleInsertToEditor.bind(this));
			
			// Save as draft
			$('#create-draft-btn').on('click', this.handleSaveDraft.bind(this));
			
			// Copy HTML
			$('#copy-html-btn').on('click', this.handleCopyHTML.bind(this));
		},

		initTabs: function() {
			$('.contentcraft-tab').on('click', function() {
				const tab = $(this).data('tab');
				
				// Update active tab
				$('.contentcraft-tab').removeClass('active');
				$(this).addClass('active');
				
				// Update active pane
				$('.contentcraft-tab-pane').removeClass('active');
				$('#tab-' + tab).addClass('active');
			});
		},

		handleGenerate: function(e) {
			e.preventDefault();
			
			// Get form data
			const formData = this.getFormData();
			
			// Validate
			if (!formData.topic) {
				this.showNotice('error', 'Please enter a topic.');
				return;
			}
			
			// Generate
			this.generateContent(formData);
		},

		handleRegenerate: function(e) {
			e.preventDefault();
			const formData = this.getFormData();
			this.generateContent(formData);
		},

		handleRetry: function(e) {
			e.preventDefault();
			const formData = this.getFormData();
			this.generateContent(formData);
		},

		getFormData: function() {
			const keywords = $('#content-keywords').val()
				.split(',')
				.map(k => k.trim())
				.filter(k => k.length > 0);
			
			const sections = [];
			$('input[name="sections[]"]:checked').each(function() {
				sections.push($(this).val());
			});
			
			return {
				topic: $('#content-topic').val(),
				keywords: keywords,
				audience: $('#content-audience').val(),
				tone: $('#content-tone').val(),
				length: $('#content-length').val(),
				language: $('#content-language').val(),
				sections: sections
			};
		},

		generateContent: function(formData) {
			// Show loading state
			this.showState('loading');
			
			// Disable button
			$('#generate-content-btn').prop('disabled', true)
				.find('.btn-text').text('Generating...');
			
			const startTime = Date.now();
			
			// Make API request
			$.ajax({
				url: contentcraftAdmin.restUrl + '/content/generate',
				method: 'POST',
				headers: {
					'X-WP-Nonce': contentcraftAdmin.nonce
				},
				data: JSON.stringify(formData),
				contentType: 'application/json',
				success: (response) => {
					const endTime = Date.now();
					const latency = ((endTime - startTime) / 1000).toFixed(2);
					
					if (response.success && response.data) {
						this.currentContent = response.data;
						this.displayContent(response.data, latency, response.metadata);
					} else {
						this.showError(response.error || 'Generation failed');
					}
				},
				error: (xhr) => {
					let errorMsg = 'An error occurred. Please try again.';
					
					if (xhr.responseJSON && xhr.responseJSON.error) {
						errorMsg = xhr.responseJSON.error;
					} else if (xhr.statusText) {
						errorMsg = xhr.statusText;
					}
					
					this.showError(errorMsg);
				},
				complete: () => {
					// Re-enable button
					$('#generate-content-btn').prop('disabled', false)
						.find('.btn-text').text('Generate Content');
				}
			});
		},

		displayContent: function(data, latency, metadata) {
			// Show result state
			this.showState('result');
			
			// Update preview
			let previewHTML = '';
			if (data.title) {
				previewHTML += '<h1>' + this.escapeHtml(data.title) + '</h1>';
			}
			if (data.excerpt) {
				previewHTML += '<div class="excerpt" style="font-size: 16px; font-style: italic; color: #646970; margin: 16px 0; padding: 16px; background: #f6f7f7; border-left: 4px solid #2271b1;">' 
					+ this.escapeHtml(data.excerpt) + '</div>';
			}
			if (data.body_html) {
				previewHTML += data.body_html;
			}
			
			$('#content-preview').html(previewHTML);
			
			// Update HTML tab
			$('#content-html').val(data.body_html || '');
			
			// Update SEO data
			if (data.meta) {
				$('#seo-title').html('<code>' + this.escapeHtml(data.meta.seo_title) + '</code>');
				$('#seo-desc').html('<code>' + this.escapeHtml(data.meta.meta_desc) + '</code>');
				$('#seo-slug').html('<code>' + this.escapeHtml(data.meta.slug) + '</code>');
			}
			
			if (data.headings && data.headings.length > 0) {
				const headingsList = data.headings.map(h => '<li>' + this.escapeHtml(h) + '</li>').join('');
				$('#seo-headings').html('<ul style="margin: 8px 0; padding-left: 20px;">' + headingsList + '</ul>');
			}
			
			// Update stats
			$('#stat-time').text(latency + 's');
			
			if (metadata && metadata.tokens_used) {
				$('#stat-tokens').text(metadata.tokens_used.toLocaleString() + ' tokens');
			}
			
			// Count words
			const wordCount = this.countWords(data.body_html || '');
			$('#stat-words').text(wordCount.toLocaleString() + ' words');
			
			// Success notification
			this.showNotice('success', 'Content generated successfully!');
		},

		handleInsertToEditor: function(e) {
			e.preventDefault();
			
			if (!this.currentContent) return;
			
			// Check if we're on edit page
			if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
				// Gutenberg editor
				const blocks = wp.blocks.parse(this.currentContent.body_html || '');
				wp.data.dispatch('core/editor').insertBlocks(blocks);
				
				// Set title if empty
				const currentTitle = wp.data.select('core/editor').getEditedPostAttribute('title');
				if (!currentTitle && this.currentContent.title) {
					wp.data.dispatch('core/editor').editPost({
						title: this.currentContent.title
					});
				}
				
				this.showNotice('success', 'Content inserted into editor!');
			} else {
				// Classic editor or not on edit page
				this.showNotice('info', 'Please open post editor first, then use this feature.');
			}
		},

		handleSaveDraft: function(e) {
			e.preventDefault();
			
			if (!this.currentContent) return;
			
			// Create new post via REST API
			$.ajax({
				url: '/wp-json/wp/v2/posts',
				method: 'POST',
				headers: {
					'X-WP-Nonce': contentcraftAdmin.nonce
				},
				data: JSON.stringify({
					title: this.currentContent.title || 'Untitled',
					content: this.currentContent.body_html || '',
					excerpt: this.currentContent.excerpt || '',
					status: 'draft',
					meta: {
						_yoast_wpseo_title: this.currentContent.meta?.seo_title || '',
						_yoast_wpseo_metadesc: this.currentContent.meta?.meta_desc || ''
					}
				}),
				contentType: 'application/json',
				success: (response) => {
					this.showNotice('success', 'Draft saved! Opening editor...');
					
					// Redirect to edit page
					setTimeout(() => {
						window.location.href = '/wp-admin/post.php?post=' + response.id + '&action=edit';
					}, 1000);
				},
				error: () => {
					this.showNotice('error', 'Failed to save draft. Please try again.');
				}
			});
		},

		handleCopyHTML: function(e) {
			e.preventDefault();
			
			const html = $('#content-html').val();
			
			if (!html) return;
			
			// Copy to clipboard
			const textarea = document.getElementById('content-html');
			textarea.select();
			
			try {
				document.execCommand('copy');
				this.showNotice('success', 'HTML copied to clipboard!');
			} catch (err) {
				this.showNotice('error', 'Failed to copy. Please select and copy manually.');
			}
		},

		showState: function(state) {
			$('#content-loading').hide();
			$('#content-empty').hide();
			$('#content-result').hide();
			$('#content-error').hide();
			
			switch(state) {
				case 'loading':
					$('#content-loading').show();
					break;
				case 'empty':
					$('#content-empty').show();
					break;
				case 'result':
					$('#content-result').show();
					break;
				case 'error':
					$('#content-error').show();
					break;
			}
		},

		showError: function(message) {
			this.showState('error');
			$('#error-message').text(message);
		},

		showNotice: function(type, message) {
			// Remove existing notices
			$('.contentcraft-notice').remove();
			
			// Create notice
			const notice = $('<div>')
				.addClass('notice notice-' + type + ' is-dismissible contentcraft-notice')
				.html('<p>' + message + '</p>');
			
			// Insert after title
			$('.contentcraft-page-title').after(notice);
			
			// Auto dismiss after 5 seconds
			setTimeout(() => {
				notice.fadeOut(() => notice.remove());
			}, 5000);
			
			// Manual dismiss
			notice.find('.notice-dismiss').on('click', function() {
				notice.remove();
			});
		},

		escapeHtml: function(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		},

		countWords: function(html) {
			// Strip HTML tags
			const text = $('<div>').html(html).text();
			// Count words
			const words = text.trim().split(/\s+/);
			return words.filter(w => w.length > 0).length;
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		if ($('.contentcraft-content-studio').length > 0) {
			ContentStudio.init();
		}
	});

})(jQuery);

