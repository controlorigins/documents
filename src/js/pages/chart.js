// Chart page functionality
let currentChart = null;

export function initChart() {
    const csvSelect = document.getElementById('csv-file-select');
    const chartTypeSelect = document.getElementById('chart-type');
    const generateBtn = document.getElementById('generate-chart');
    
    if (!csvSelect || !window.Chart) {
        console.error('Chart.js not available');
        return;
    }

    // Handle CSV file selection
    csvSelect.addEventListener('change', function() {
        const selectedFile = this.value;
        if (selectedFile) {
            loadCSVColumns(selectedFile);
            document.getElementById('chart-config').style.display = 'block';
            document.getElementById('chart-title').textContent = selectedFile;
        } else {
            hideChartConfig();
            showNoChart();
        }
    });

    // Handle chart type change
    chartTypeSelect.addEventListener('change', function() {
        updateLimitVisibility();
    });

    // Handle generate button
    generateBtn.addEventListener('click', generateChart);

    // Initialize from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const csvParam = urlParams.get('csv');
    if (csvParam && csvSelect.value !== csvParam) {
        csvSelect.value = csvParam;
        csvSelect.dispatchEvent(new Event('change'));
    }
}

function loadCSVColumns(csvFile) {
    const loadingEl = document.getElementById('chart-loading');
    loadingEl.classList.remove('d-none');

    fetch(`/pages/ajax/csv-summary.php?file=${encodeURIComponent(csvFile)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            populateColumnSelects(data.columns);
            document.getElementById('column-config').style.display = 'block';
            updateLimitVisibility();
        })
        .catch(error => {
            console.error('Error loading CSV columns:', error);
            alert('Error loading CSV columns: ' + error.message);
            hideChartConfig();
        })
        .finally(() => {
            loadingEl.classList.add('d-none');
        });
}

function populateColumnSelects(columns) {
    const labelSelect = document.getElementById('label-column');
    const dataSelect = document.getElementById('data-column');
    
    // Clear existing options
    labelSelect.innerHTML = '<option value="">Select column...</option>';
    dataSelect.innerHTML = '<option value="">Select column...</option>';
    
    // Add column options
    columns.forEach(column => {
        labelSelect.innerHTML += `<option value="${column}">${column}</option>`;
        dataSelect.innerHTML += `<option value="${column}">${column}</option>`;
    });
    
    // Show generate button when both selects are populated
    labelSelect.addEventListener('change', checkGenerateReady);
    dataSelect.addEventListener('change', checkGenerateReady);
    
    document.getElementById('generate-config').style.display = 'block';
}

function checkGenerateReady() {
    const labelCol = document.getElementById('label-column').value;
    const dataCol = document.getElementById('data-column').value;
    const generateBtn = document.getElementById('generate-chart');
    
    generateBtn.disabled = !labelCol || !dataCol;
}

function updateLimitVisibility() {
    const chartType = document.getElementById('chart-type').value;
    const limitConfig = document.getElementById('limit-config');
    
    // Hide limit for pie charts (they work better with aggregated data)
    limitConfig.style.display = chartType === 'pie' ? 'none' : 'block';
}

function generateChart() {
    const csvFile = document.getElementById('csv-file-select').value;
    const chartType = document.getElementById('chart-type').value;
    const labelColumn = document.getElementById('label-column').value;
    const dataColumn = document.getElementById('data-column').value;
    const limitRows = document.getElementById('limit-rows').value;
    
    if (!csvFile || !labelColumn || !dataColumn) {
        alert('Please select all required fields');
        return;
    }
    
    const loadingEl = document.getElementById('chart-loading');
    loadingEl.classList.remove('d-none');
    
    // Build query parameters
    const params = new URLSearchParams({
        file: csvFile,
        labelCol: labelColumn,
        dataCol: dataColumn,
        limit: limitRows
    });
    
    fetch(`/pages/ajax/chart-data.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            createChart(data, chartType, labelColumn, dataColumn);
            showChart();
        })
        .catch(error => {
            console.error('Error generating chart:', error);
            alert('Error generating chart: ' + error.message);
        })
        .finally(() => {
            loadingEl.classList.add('d-none');
        });
}

function createChart(data, chartType, labelColumn, dataColumn) {
    const ctx = document.getElementById('data-chart').getContext('2d');
    
    // Destroy existing chart
    if (currentChart) {
        currentChart.destroy();
    }
    
    const chartConfig = {
        type: chartType,
        data: {
            labels: data.labels,
            datasets: [{
                label: dataColumn,
                data: data.values,
                backgroundColor: generateColors(data.labels.length, chartType === 'pie'),
                borderColor: chartType === 'pie' ? '#fff' : generateColors(data.labels.length, false),
                borderWidth: chartType === 'pie' ? 2 : 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: `${chartType.charAt(0).toUpperCase() + chartType.slice(1)} Chart: ${labelColumn} vs ${dataColumn}`
                },
                legend: {
                    display: chartType === 'pie',
                    position: chartType === 'pie' ? 'right' : 'top'
                }
            },
            scales: chartType === 'pie' ? {} : {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: dataColumn
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: labelColumn
                    }
                }
            }
        }
    };
    
    currentChart = new Chart(ctx, chartConfig);
}

function generateColors(count, isPie) {
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384',
        '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
    ];
    
    if (isPie) {
        return colors.slice(0, count);
    }
    
    // For bar/line charts, use primary color with transparency
    return Array(count).fill('rgba(54, 162, 235, 0.7)');
}

function showChart() {
    document.getElementById('no-chart').style.display = 'none';
    document.getElementById('chart-container').style.display = 'block';
}

function showNoChart() {
    document.getElementById('no-chart').style.display = 'block';
    document.getElementById('chart-container').style.display = 'none';
    document.getElementById('chart-title').textContent = 'Select a file to begin';
}

function hideChartConfig() {
    document.getElementById('chart-config').style.display = 'none';
    document.getElementById('column-config').style.display = 'none';
    document.getElementById('generate-config').style.display = 'none';
}
