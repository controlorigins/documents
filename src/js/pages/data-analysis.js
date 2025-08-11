// Data Analysis Page JavaScript
// This file contains all JavaScript functionality for the data-analysis page

import $ from 'jquery';

// Initialize data analysis functionality when DOM is ready
$(document).ready(function() {
    // Only run on data-analysis page
    if (!$('#csv-data-table').length) {
        return;
    }
    
    let dataTable = null;
    
    // Handle CSV file selection
    $('.csv-file-radio').on('change', function() {
        const selectedFile = this.value;
        // Do a full page refresh to avoid DataTable definition conflicts
        const currentUrl = window.location.href.split('?')[0];
        const newUrl = `${currentUrl}?page=data-analysis&csv=${encodeURIComponent(selectedFile)}`;
        window.location.href = newUrl;
    });
    
    // Check if a CSV file was selected via URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const selectedCsv = urlParams.get('csv');
    
    if (selectedCsv) {
        // Check the corresponding radio button
        const radioButton = document.getElementById(selectedCsv);
        if (radioButton) {
            radioButton.checked = true;
        }
        
        // Load the CSV data
        loadCSVData(selectedCsv);
    }
    
    function loadCSVData(filename) {
        // Show loading indicator
        $('#loading-indicator').removeClass('d-none');
        $('#analysis-results').addClass('d-none');
        $('#no-selection').addClass('d-none');
        
        // Update current file name
        $('#current-file-name').text(filename);
        
        // Destroy existing DataTable if it exists
        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }
        
        // Load summary data first
        $.ajax({
            url: 'pages/ajax/csv-summary.php',
            method: 'GET',
            data: { file: filename },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    showError('Error loading summary: ' + response.error);
                    return;
                }
                
                // Update summary table
                updateSummaryTable(response.summary);
                
                // Update statistics
                $('#total-rows').text(response.stats.rows.toLocaleString());
                $('#total-columns').text(response.stats.columns.toLocaleString());
                $('#total-cells').text(response.stats.cells.toLocaleString());
                
                // Now load the data table
                loadDataTable(filename);
            },
            error: function(xhr, status, error) {
                showError('Failed to load CSV summary: ' + error);
            }
        });
    }
    
    function loadDataTable(filename) {
        // First, get column information
        $.ajax({
            url: 'pages/ajax/csv-data.php',
            method: 'GET',
            data: { file: filename },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    showError('Error loading data: ' + response.error);
                    return;
                }
                
                // Create column headers
                let headerHtml = '<tr>';
                response.columns.forEach(function(column) {
                    headerHtml += '<th>' + column + '</th>';
                });
                headerHtml += '</tr>';
                $('#data-table-header').html(headerHtml);
                
                // Initialize DataTable with AJAX
                dataTable = $('#csv-data-table').DataTable({
                    ajax: {
                        url: 'pages/ajax/csv-data.php',
                        data: { file: filename },
                        dataSrc: 'data'
                    },
                    columns: response.columns.map(function(column) {
                        return { title: column };
                    }),
                    responsive: true,
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    processing: true,
                    language: {
                        processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Processing...',
                        search: '<i class="bi bi-search me-1"></i>Search:',
                        lengthMenu: '<i class="bi bi-list me-1"></i>Show _MENU_ entries',
                        info: '<i class="bi bi-info-circle me-1"></i>Showing _START_ to _END_ of _TOTAL_ entries',
                        paginate: {
                            first: '<i class="bi bi-chevron-double-left"></i>',
                            last: '<i class="bi bi-chevron-double-right"></i>',
                            next: '<i class="bi bi-chevron-right"></i>',
                            previous: '<i class="bi bi-chevron-left"></i>'
                        }
                    },
                    order: [[0, 'asc']],
                    initComplete: function() {
                        // Hide loading indicator and show results
                        $('#loading-indicator').addClass('d-none');
                        $('#analysis-results').removeClass('d-none');
                        
                        // Add column filters
                        this.api().columns().every(function() {
                            const column = this;
                            const select = $('<select class="form-select form-select-sm"><option value="">All</option></select>')
                                .appendTo($(column.header()))
                                .on('change', function() {
                                    const val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                })
                                .on('click', function(e) {
                                    e.stopPropagation();
                                });
                            
                            // Get unique values for the filter (limit to first 50 for performance)
                            column.data().unique().sort().each(function(d, j) {
                                if (j < 50) { // Limit options to prevent overwhelming dropdown
                                    select.append('<option value="' + d + '">' + d + '</option>');
                                }
                            });
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                showError('Failed to load CSV data: ' + error);
            }
        });
    }
    
    function updateSummaryTable(summaryData) {
        const tbody = $('#summary-tbody');
        tbody.empty();
        
        summaryData.forEach(function(summary) {
            const row = '<tr>' +
                '<td><strong>' + summary.field + '</strong></td>' +
                '<td>' + summary.min + '</td>' +
                '<td>' + summary.average + '</td>' +
                '<td>' + summary.max + '</td>' +
                '<td>' + summary.mostCommon + '</td>' +
                '<td>' + summary.leastCommon + '</td>' +
                '<td><span class="badge bg-primary rounded-pill">' + summary.distinctCount + '</span></td>' +
                '</tr>';
            tbody.append(row);
        });
    }
    
    function showError(message) {
        $('#loading-indicator').addClass('d-none');
        $('#analysis-results').addClass('d-none');
        $('#no-selection').removeClass('d-none');
        
        // Create error alert
        const errorAlert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            '<i class="bi bi-exclamation-triangle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
        
        // Show error in the no-selection card
        $('#no-selection .card-body').prepend(errorAlert);
    }
});
