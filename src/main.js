// Main entry point for Vite build
// Import all CSS
import './css/site.scss';

// Import all JavaScript
import './js/vendor.js';
import './js/custom.js';

// Initialize page-specific functionality after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get current page from URL or data attribute
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 'document_view';
    
    // Page-specific initialization
    if (currentPage === 'data-analysis') {
        import('./js/pages/data-analysis.js').then(module => {
            if (module.initDataAnalysis) {
                module.initDataAnalysis();
            }
        }).catch(_err => console.log('Data analysis module not available'));
    } else if (currentPage === 'chart') {
        import('./js/pages/chart.js').then(module => {
            if (module.initChart) {
                module.initChart();
            }
        }).catch(_err => console.log('Chart module not available'));
    }
});

