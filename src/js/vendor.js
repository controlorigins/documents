// Vendor JavaScript imports
// Import jQuery first (required by Bootstrap and DataTables)
import $ from 'jquery';

// Import Bootstrap JavaScript and make it globally available
import * as bootstrap from 'bootstrap';

// Import DataTables core and extensions
import 'datatables.net';
import 'datatables.net-bs5';

// Import DataTables Buttons extensions
import 'datatables.net-buttons';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-buttons/js/buttons.colVis.mjs';

// Import JSZip for Excel export functionality
import JSZip from 'jszip';
window.JSZip = JSZip;

// PrismJS implementation following official best practices
// Set manual mode to prevent auto-highlighting before bundle is ready
window.Prism = window.Prism || {};
window.Prism.manual = true;

// Import PrismJS core
import Prism from 'prismjs';

// Import language components in the correct dependency order
import 'prismjs/components/prism-markup-templating';  // Required for PHP
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-javascript'; 
import 'prismjs/components/prism-css';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-sql';
import 'prismjs/components/prism-bash';

// Import essential plugins (avoiding tokenizePlaceholders dependencies)
import 'prismjs/plugins/line-numbers/prism-line-numbers';
import 'prismjs/plugins/normalize-whitespace/prism-normalize-whitespace';

// Make jQuery and Bootstrap globally available for any inline scripts
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;

// Make Prism globally available
window.Prism = Prism;

// Initialize PrismJS when DOM is ready with proper configuration
$(document).ready(function() {
    // Configure normalize-whitespace plugin
    Prism.plugins.NormalizeWhitespace.setDefaults({
        'remove-trailing': true,
        'remove-indent': true,
        'left-trim': true,
        'right-trim': true,
        'break-lines': 80,
        'indent': 2,
        'remove-initial-line-feed': false,
        'tabs-to-spaces': 4,
        'spaces-to-tabs': 0
    });
    
    // Manual highlighting of all code blocks
    Prism.highlightAll();
});
