// Vendor JavaScript imports
// Import jQuery first (required by Bootstrap and DataTables)
import $ from 'jquery';

// Import Bootstrap JavaScript and make it globally available
import * as bootstrap from 'bootstrap';

// Import DataTables
import 'datatables.net';
import 'datatables.net-bs5';

// Import PrismJS core
import Prism from 'prismjs';

// Import PrismJS language components
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-css';
import 'prismjs/components/prism-markup'; // HTML/XML
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-sql';

// Import PrismJS plugins for enhanced functionality
import 'prismjs/plugins/line-numbers/prism-line-numbers';
import 'prismjs/plugins/copy-to-clipboard/prism-copy-to-clipboard';
import 'prismjs/plugins/toolbar/prism-toolbar';

// Make jQuery, Bootstrap, and Prism globally available for any inline scripts
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;
window.Prism = Prism;
