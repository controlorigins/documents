// Vendor JavaScript imports
// Import jQuery first (required by Bootstrap and DataTables)
import $ from 'jquery';

// Import Bootstrap JavaScript and make it globally available
import * as bootstrap from 'bootstrap';

// Import DataTables
import 'datatables.net';
import 'datatables.net-bs5';

// Make jQuery and Bootstrap globally available for any inline scripts
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;
