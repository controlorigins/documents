/* global $, bootstrap, Prism */
// Custom JavaScript for PHPDocSpark (Mark Hazleton)

// Global fetchJoke function - defined outside of document ready for immediate availability
let jokeCount = 0;
window.fetchJoke = function () {
  // Show loading spinner
  const jokeContainer = document.getElementById('joke-container');
  if (!jokeContainer) {
    return;
  }
  
  jokeContainer.innerHTML = `
    <div class="d-flex justify-content-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  `;

  // Use native fetch API for better compatibility
  fetch('pages/fetch_joke.php')
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.text();
    })
    .then(data => {
      jokeCount++;
      
      // Check if jQuery is available for animations, otherwise just set content
      if (typeof $ !== 'undefined' && $.fn.fadeOut) {
        const $jokeContainer = $('#joke-container');
        $jokeContainer.fadeOut(200, function () {
          $jokeContainer.html(`
            <div class="text-center">
              <div class="joke-content">
                ${data}
              </div>
              <div class="mt-3 text-muted">
                <small>Joke #${jokeCount}</small>
              </div>
            </div>
          `);
          $jokeContainer.fadeIn(200);
        });
      } else {
        // Fallback without jQuery animations
        jokeContainer.innerHTML = `
          <div class="text-center">
            <div class="joke-content">
              ${data}
            </div>
            <div class="mt-3 text-muted">
              <small>Joke #${jokeCount}</small>
            </div>
          </div>
        `;
      }
    })
    .catch(error => {
      // Show error in the UI instead of console
      jokeContainer.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Failed to fetch joke: ${error.message}
        </div>
      `;
    });
};

// Auto-load joke immediately if on joke page
if (document.getElementById('joke-container')) {
  // Use DOMContentLoaded to ensure the page is ready but don't wait for all assets
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => window.fetchJoke(), 100);
    });
  } else {
    // DOM is already ready
    setTimeout(() => window.fetchJoke(), 100);
  }
}

// PrismJS Initialization and Configuration
$(document).ready(function () {
  // Configure PrismJS if available
  if (typeof Prism !== 'undefined') {
    // Manual highlighting mode - we'll trigger it after DOM is ready
    Prism.manual = false;
    
    // Add line numbers to all code blocks automatically
    $('pre[class*="language-"]').addClass('line-numbers');
    
    // Initialize copy-to-clipboard for all code blocks
    Prism.plugins.toolbar.registerButton('copy-to-clipboard', function (_env) {
      const button = document.createElement('button');
      button.innerHTML = '<i class="bi bi-clipboard"></i>';
      button.title = 'Copy to clipboard';
      
      return button;
    });
    
    // Re-highlight any code blocks that might have been loaded dynamically
    Prism.highlightAll();
    
    // Custom event for when highlighting is complete
    $(document).on('prismHighlightComplete', function() {
      // PrismJS highlighting completed - ready for custom interactions
    });
    
    // Trigger highlighting complete event
    setTimeout(function() {
      $(document).trigger('prismHighlightComplete');
    }, 100);
  }
  
  // DataTables initialization
  // (debug log removed)

  // Initialize DataTables if table exists
  if ($('#myTable').length) {
  // (debug log removed)
    $('#myTable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [
        [5, 10, 25, 50, -1],
        [5, 10, 25, 50, 'All'],
      ],
    });
  }

  // Data Analysis Page - DataTable initialization
  if ($('#dataTable').length) {
  // (debug log removed)
    $('#dataTable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [
        [5, 10, 25, 50, -1],
        [5, 10, 25, 50, 'All'],
      ],
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'collection',
          text: '<i class="bi bi-download"></i> Export',
          buttons: ['csv', 'excel', 'pdf'],
        },
        'colvis',
      ],
    });
  }

  // Data Analysis Page - Auto-submit CSV form on selection change
  $('input[name="csvFile"]').change(function () {
    $('#csvSelectForm').submit();
  });

  // Database Page - Initialize DataTable for contacts
  if ($('#contactsTable').length) {
  // (debug log removed)
    $('#contactsTable').DataTable({
      responsive: true,
      pageLength: 5,
      lengthMenu: [
        [5, 10, 25, -1],
        [5, 10, 25, 'All'],
      ],
    });
  }

  // Database Page - Handle edit button clicks and modal
  const editButtons = document.querySelectorAll('.edit-btn');
  const editModalElement = document.getElementById('editContactModal');

  if (editButtons.length > 0 && editModalElement) {
    const editModal = new bootstrap.Modal(editModalElement);

    editButtons.forEach(button => {
      button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const email = this.getAttribute('data-email');

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;

        editModal.show();
      });
    });
  }

  // Database Page - Handle refresh button
  const refreshButton = document.getElementById('refreshTable');
  if (refreshButton) {
    refreshButton.addEventListener('click', function () {
      window.location.reload();
    });
  }

  // Database Page - Auto-dismiss alerts after 5 seconds
  setTimeout(function () {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
      if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
        bootstrap.Alert.getOrCreateInstance(alert).close();
      }
    });
  }, 5000);

  // Project List Page - Pagination and filtering functionality
  if ($('#projectsContainer').length) {
  // (debug log removed)
    const projectsPerPage = 6;
    let currentPage = 1;
    let filteredProjects = $('.project-item');

    // Function to show projects based on current page and filters
    function displayProjects() {
      const startIndex = (currentPage - 1) * projectsPerPage;
      const endIndex = startIndex + projectsPerPage;

      // Hide all projects first
      $('.project-item').hide();

      // Show only the projects for current page
      filteredProjects.slice(startIndex, endIndex).show();

      // Update project count
      $('#projectCount').text(filteredProjects.length);

      // Generate pagination
      generatePagination();
    }

    // Generate pagination links
    function generatePagination() {
      const totalPages = Math.ceil(filteredProjects.length / projectsPerPage);
      const pagination = $('#projectPagination');
      pagination.empty();

      // Don't show pagination if only one page
      if (totalPages <= 1) {
        return;
      }

      // Previous button
      pagination.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `);

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        pagination.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
      }

      // Next button
      pagination.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `);

      // Attach click event to pagination links
      $('.page-link').click(function (e) {
        e.preventDefault();
        const page = $(this).data('page');

        if (page >= 1 && page <= totalPages) {
          currentPage = page;
          displayProjects();

          // Scroll to top of projects container
          $('html, body').animate(
            {
              scrollTop: $('#projectsContainer').offset().top - 100,
            },
            300
          );
        }
      });
    }

    // Filter projects by letter
    $('.btn-group .btn').click(function () {
      // Update active button
      $('.btn-group .btn').removeClass('active');
      $(this).addClass('active');

      const filter = $(this).data('filter');

      if (filter === 'all') {
        filteredProjects = $('.project-item');
      } else {
        filteredProjects = $(`.project-item[data-letter="${filter}"]`);
      }

      // Reset to first page
      currentPage = 1;
      displayProjects();
    });

    // Search functionality
    $('#projectSearch').on('input', function () {
      const searchText = $(this).val().toLowerCase();

      // Get current letter filter
      const letterFilter = $('.btn-group .btn.active').data('filter');

      if (searchText === '') {
        // If search is empty, just apply letter filter
        if (letterFilter === 'all') {
          filteredProjects = $('.project-item');
        } else {
          filteredProjects = $(`.project-item[data-letter="${letterFilter}"]`);
        }
      } else {
        // Apply both search and letter filter
        if (letterFilter === 'all') {
          filteredProjects = $('.project-item').filter(function () {
            const projectTitle = $(this).find('.card-title').text().toLowerCase();
            const projectDesc = $(this).find('.card-text').text().toLowerCase();
            return projectTitle.includes(searchText) || projectDesc.includes(searchText);
          });
        } else {
          filteredProjects = $(`.project-item[data-letter="${letterFilter}"]`).filter(function () {
            const projectTitle = $(this).find('.card-title').text().toLowerCase();
            const projectDesc = $(this).find('.card-text').text().toLowerCase();
            return projectTitle.includes(searchText) || projectDesc.includes(searchText);
          });
        }
      }

      // Reset to first page
      currentPage = 1;
      displayProjects();
    });

    // Initial display
    displayProjects();
  }

  // DataTable component initialization (for datatable.php)
  if ($('#myTable').length && !$('#dataTable').length && !$('#contactsTable').length) {
  // (debug log removed)
    $('#myTable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [
        [5, 10, 25, 50, -1],
        [5, 10, 25, 50, 'All'],
      ],
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'collection',
          text: '<i class="bi bi-download"></i> Export',
          buttons: [
            {
              extend: 'csv',
              text: '<i class="bi bi-filetype-csv"></i> CSV',
            },
            {
              extend: 'excel',
              text: '<i class="bi bi-filetype-xlsx"></i> Excel',
            },
            {
              extend: 'pdf',
              text: '<i class="bi bi-filetype-pdf"></i> PDF',
            },
          ],
        },
        {
          extend: 'colvis',
          text: '<i class="bi bi-eye"></i> Columns',
        },
      ],
    });
  }

  // Add any other custom initialization here
  // (debug log removed)
});
