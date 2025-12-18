/**
 * Display Environment Type in Block Editor
 * 
 * Adds an environment type indicator to the Gutenberg block editor.
 */

(function () {
	// Get the environment data passed from PHP
	const envData = window.detEnvData || {};

	/**
	 * Create environment indicator near the save button
	 */
	function addEnvironmentIndicator() {
		// Check if indicator already exists
		if (document.querySelector('.det-editor-indicator')) {
			return;
		}

		const envType = envData.envType || 'production';
		const envTypeName = envData.envTypeName || 'Production';

		// Find the editor header toolbar (where save button is)
		const editorHeader = document.querySelector('.edit-post-header__settings') || 
		                     document.querySelector('.editor-header__settings');
		
		if (!editorHeader) {
			return;
		}

		// Create the indicator element
		const indicator = document.createElement('div');
		indicator.className = 'det-editor-indicator det-' + envType;
		
		// Create tooltip content
		const tooltipContent = [
			'<strong>' + envTypeName + '</strong>',
			envData.wpDebug ? 'WP_DEBUG: ' + envData.wpDebug : '',
			envData.wpDebugLog ? 'WP_DEBUG_LOG: ' + envData.wpDebugLog : '',
			envData.wpDebugDisplay ? 'WP_DEBUG_DISPLAY: ' + envData.wpDebugDisplay : '',
			envData.wpDevelopmentMode ? 'WP_DEVELOPMENT_MODE: ' + envData.wpDevelopmentMode : '',
			envData.scriptDebug ? 'SCRIPT_DEBUG: ' + envData.scriptDebug : '',
			envData.saveQueries ? 'SAVEQUERIES: ' + envData.saveQueries : '',
			envData.wpVersion ? 'WP: ' + envData.wpVersion : '',
			envData.phpVersion ? 'PHP: ' + envData.phpVersion : ''
		].filter(Boolean).join('<br>');

		indicator.innerHTML = '<span class="det-icon"></span><span class="det-text">' + envTypeName + '</span>';
		indicator.title = tooltipContent.replace(/<br>/g, '\n').replace(/<\/?strong>/g, '');
		
		// Insert before the first child in the settings area
		editorHeader.insertBefore(indicator, editorHeader.firstChild);
	}

	// Try to add the indicator when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			setTimeout(addEnvironmentIndicator, 500);
		});
	} else {
		setTimeout(addEnvironmentIndicator, 500);
	}

	// Also try again after a longer delay to catch late-loading editors
	setTimeout(addEnvironmentIndicator, 1500);
})();
