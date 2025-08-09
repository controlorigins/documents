// Custom JavaScript for Control Origins Documentation

// DataTables initialization
$(document).ready(function() {
    console.log('Custom JavaScript loaded');
    
    // Initialize DataTables if table exists
    if ($('#myTable').length) {
        console.log('Initializing DataTables');
        $('#myTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
        });
    }
    
    // Data Analysis Page - DataTable initialization
    if ($('#dataTable').length) {
        console.log('Initializing DataTable for data-analysis');
        $('#dataTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="bi bi-download"></i> Export',
                    buttons: [
                        'csv',
                        'excel',
                        'pdf'
                    ]
                },
                'colvis'
            ]
        });
    }
    
    // Data Analysis Page - Auto-submit CSV form on selection change
    $('input[name="csvFile"]').change(function() {
        $('#csvSelectForm').submit();
    });
    
    // Database Page - Initialize DataTable for contacts
    if ($('#contactsTable').length) {
        console.log('Initializing DataTable for contacts');
        $('#contactsTable').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]]
        });
    }
    
    // Database Page - Handle edit button clicks and modal
    const editButtons = document.querySelectorAll('.edit-btn');
    const editModalElement = document.getElementById('editContactModal');
    
    if (editButtons.length > 0 && editModalElement) {
        const editModal = new bootstrap.Modal(editModalElement);
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
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
        refreshButton.addEventListener('click', function() {
            window.location.reload();
        });
    }
    
    // Database Page - Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        });
    }, 5000);
    
    // Project List Page - Pagination and filtering functionality
    if ($('#projectsContainer').length) {
        console.log('Initializing project list functionality');
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
            $('.page-link').click(function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                
                if (page >= 1 && page <= totalPages) {
                    currentPage = page;
                    displayProjects();
                    
                    // Scroll to top of projects container
                    $('html, body').animate({
                        scrollTop: $('#projectsContainer').offset().top - 100
                    }, 300);
                }
            });
        }
        
        // Filter projects by letter
        $('.btn-group .btn').click(function() {
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
        $('#projectSearch').on('input', function() {
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
                    filteredProjects = $('.project-item').filter(function() {
                        const projectTitle = $(this).find('.card-title').text().toLowerCase();
                        const projectDesc = $(this).find('.card-text').text().toLowerCase();
                        return projectTitle.includes(searchText) || projectDesc.includes(searchText);
                    });
                } else {
                    filteredProjects = $(`.project-item[data-letter="${letterFilter}"]`).filter(function() {
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
    
    // Joke Page - AJAX functionality
    if ($('#joke-container').length) {
        console.log('Initializing joke page functionality');
        let jokeCount = 0;
        
        window.fetchJoke = function() {
            // Show loading spinner
            document.getElementById('joke-container').innerHTML = `
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Fetch joke with AJAX
            $.ajax({
                url: 'pages/fetch_joke.php',
                method: 'GET',
                success: function(response) {
                    jokeCount++;
                    
                    // Add animation class
                    const jokeContainer = $('#joke-container');
                    jokeContainer.fadeOut(200, function() {
                        // Update content
                        jokeContainer.html(`
                            <div class="text-center">
                                <div class="joke-content">
                                    ${response}
                                </div>
                                <div class="mt-3 text-muted">
                                    <small>Joke #${jokeCount}</small>
                                </div>
                            </div>
                        `);
                        
                        // Fade back in
                        jokeContainer.fadeIn(200);
                    });
                },
                error: function() {
                    $('#joke-container').html(`
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Oops! Failed to fetch a joke. Please try again later.
                        </div>
                    `);
                }
            });
        };
        
        // Auto-load first joke
        fetchJoke();
    }
    
    // DataTable component initialization (for datatable.php)
    if ($('#myTable').length && !$('#dataTable').length && !$('#contactsTable').length) {
        console.log('Initializing generic DataTable');
        $('#myTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="bi bi-download"></i> Export',
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<i class="bi bi-filetype-csv"></i> CSV'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bi bi-filetype-xlsx"></i> Excel'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bi bi-filetype-pdf"></i> PDF'
                        }
                    ]
                },
                {
                    extend: 'colvis',
                    text: '<i class="bi bi-eye"></i> Columns'
                }
            ]
        });
    }
    
    // Add any other custom initialization here
    console.log('Control Origins Documentation initialized');
});
