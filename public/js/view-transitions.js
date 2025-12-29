/**
 * View Transitions API - Enhanced SPA-like Navigation
 * 
 * This script enhances the native View Transitions API to provide
 * smooth, app-like page transitions similar to mobile SPAs.
 */

(function () {
    'use strict';

    // Check if View Transitions API is supported
    const supportsViewTransitions = 'startViewTransition' in document;

    // Track navigation history for back/forward detection
    let navigationHistory = [];
    let isBackNavigation = false;

    // Create loading indicator
    function createLoadingIndicator() {
        if (document.querySelector('.vt-loading-indicator')) return;

        const indicator = document.createElement('div');
        indicator.className = 'vt-loading-indicator';
        indicator.setAttribute('aria-hidden', 'true');
        document.body.appendChild(indicator);
        return indicator;
    }

    // Show loading indicator
    function showLoading() {
        const indicator = document.querySelector('.vt-loading-indicator') || createLoadingIndicator();
        if (indicator) {
            indicator.classList.add('active');
        }
    }

    // Hide loading indicator
    function hideLoading() {
        const indicator = document.querySelector('.vt-loading-indicator');
        if (indicator) {
            indicator.classList.remove('active');
        }
    }

    // Detect if this is a back/forward navigation
    function detectBackNavigation(url) {
        const currentIndex = navigationHistory.indexOf(window.location.href);
        const targetIndex = navigationHistory.indexOf(url);

        if (targetIndex !== -1 && targetIndex < currentIndex) {
            return true;
        }
        return false;
    }

    // Add current page to history
    function trackNavigation() {
        const currentUrl = window.location.href;
        if (!navigationHistory.includes(currentUrl)) {
            navigationHistory.push(currentUrl);
        }
        // Keep history limited to prevent memory issues
        if (navigationHistory.length > 50) {
            navigationHistory.shift();
        }
    }

    // Handle link navigation with View Transitions
    function handleNavigation(event) {
        const link = event.target.closest('a');

        if (!link) return;

        // Skip if not a proper navigation link
        if (
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.href.startsWith('javascript:') ||
            link.href.startsWith('mailto:') ||
            link.href.startsWith('tel:') ||
            link.href.startsWith('#') ||
            link.classList.contains('no-view-transition') ||
            event.ctrlKey ||
            event.metaKey ||
            event.shiftKey
        ) {
            return;
        }

        // Check if same origin
        try {
            const url = new URL(link.href);
            if (url.origin !== window.location.origin) {
                return;
            }
        } catch (e) {
            return;
        }

        // Skip form submissions and special links
        if (link.closest('form')) return;

        // For browsers without View Transitions API, just navigate normally
        if (!supportsViewTransitions) {
            return;
        }

        // Prevent default navigation
        event.preventDefault();

        // Detect back navigation
        isBackNavigation = detectBackNavigation(link.href);

        // Apply back navigation class if needed
        if (isBackNavigation) {
            document.documentElement.classList.add('vt-back-navigation');
        } else {
            document.documentElement.classList.remove('vt-back-navigation');
        }

        // Show loading indicator
        showLoading();

        // Start view transition
        const transition = document.startViewTransition(async () => {
            // Fetch the new page
            try {
                const response = await fetch(link.href, {
                    headers: {
                        'X-Requested-With': 'ViewTransition'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update the document
                // Update title
                document.title = doc.title;

                // Update main content
                const newMain = doc.querySelector('main');
                const currentMain = document.querySelector('main');
                if (newMain && currentMain) {
                    currentMain.innerHTML = newMain.innerHTML;
                }

                // Update admin content if exists
                const newAdminContent = doc.querySelector('.main-content-admin');
                const currentAdminContent = document.querySelector('.main-content-admin');
                if (newAdminContent && currentAdminContent) {
                    currentAdminContent.innerHTML = newAdminContent.innerHTML;
                }

                // Update URL
                window.history.pushState({}, '', link.href);

                // Track navigation
                trackNavigation();

                // Re-run any initialized scripts for the new content
                reinitializeScripts();

            } catch (error) {
                console.error('View transition failed:', error);
                // Fallback to normal navigation
                window.location.href = link.href;
            }
        });

        // Handle transition completion
        transition.finished.then(() => {
            hideLoading();
            document.documentElement.classList.remove('vt-back-navigation');
            // Scroll to top on new page
            window.scrollTo({ top: 0, behavior: 'instant' });
        }).catch(() => {
            hideLoading();
            document.documentElement.classList.remove('vt-back-navigation');
        });
    }

    // Re-initialize scripts after content update
    function reinitializeScripts() {
        // Re-initialize Bootstrap components
        if (typeof bootstrap !== 'undefined') {
            // Reinit dropdowns
            document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                new bootstrap.Dropdown(el);
            });

            // Reinit tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

            // Reinit modals
            document.querySelectorAll('.modal').forEach(el => {
                new bootstrap.Modal(el);
            });
        }

        // Re-initialize Swiper if exists
        if (typeof Swiper !== 'undefined') {
            document.querySelectorAll('.swiper:not(.swiper-initialized)').forEach(el => {
                // Basic swiper init - specific swipers should reinit themselves
            });
        }

        // Dispatch custom event for other scripts to hook into
        document.dispatchEvent(new CustomEvent('viewTransitionComplete'));
    }

    // Handle browser back/forward
    function handlePopState(event) {
        if (!supportsViewTransitions) return;

        // Detect direction
        const currentIndex = navigationHistory.indexOf(window.location.href);
        isBackNavigation = true;

        document.documentElement.classList.add('vt-back-navigation');
        showLoading();

        // For popstate, let the browser handle the navigation
        // but add our transition effect
        const transition = document.startViewTransition(async () => {
            // Content will be updated by the browser
            await new Promise(resolve => setTimeout(resolve, 50));
        });

        transition.finished.then(() => {
            hideLoading();
            document.documentElement.classList.remove('vt-back-navigation');
        }).catch(() => {
            hideLoading();
            document.documentElement.classList.remove('vt-back-navigation');
        });
    }

    // Initialize
    function init() {
        // Create loading indicator
        createLoadingIndicator();

        // Track initial page
        trackNavigation();

        // Use native View Transitions API - let the browser handle it
        // We just add the loading indicator and back navigation detection

        // For enhanced control, we can intercept clicks
        // But for simplicity and reliability, let's use the native API
        // document.addEventListener('click', handleNavigation, { capture: true });

        // Handle popstate for back/forward
        window.addEventListener('popstate', handlePopState);

        // Log support status
        if (supportsViewTransitions) {
            console.log('View Transitions API: Enabled ✓');
        } else {
            console.log('View Transitions API: Not supported (using fallback)');
        }
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose for debugging
    window.ViewTransitions = {
        isSupported: supportsViewTransitions,
        showLoading,
        hideLoading,
        history: navigationHistory
    };

})();
