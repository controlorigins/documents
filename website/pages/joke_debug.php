<?php require_once __DIR__ . '/../includes/docs.php'; ?>
<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-emoji-laughing me-2"></i>Random Jokes - DEBUG VERSION</h2>
            <p class="text-light mb-0">Enjoy some humor from an external API</p>
        </div>
        <div>
            <a href="<?= doc_pretty_url('ChatGPT/Sessions/Create PHP Joke Page'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/markhazleton/PHPDocSpark/blob/main/website/pages/joke.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card joke-card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-chat-square-quote me-2"></i>Today's Joke</h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="joke-container" class="py-3">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button onclick="debugFetchJoke()" class="btn btn-primary me-2">
                            <i class="bi bi-arrow-repeat me-1"></i> Get Another Joke (Debug)
                        </button>
                        <button onclick="originalFetchJoke()" class="btn btn-secondary">
                            <i class="bi bi-arrow-repeat me-1"></i> Original Function
                        </button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-lightbulb me-2"></i>Debug Information</h5>
                        <div id="debug-info" class="alert alert-info">
                            Loading debug info...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Debug version of fetchJoke
function debugFetchJoke() {
    console.log('DEBUG: Starting debugFetchJoke');
    
    const container = document.getElementById('joke-container');
    if (!container) {
        console.error('DEBUG: joke-container not found');
        return;
    }
    
    // Show loading spinner
    container.innerHTML = `
        <div class="d-flex justify-content-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    console.log('DEBUG: Making AJAX request');
    
    // Use vanilla JavaScript AJAX instead of jQuery to avoid dependencies
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'pages/fetch_joke.php', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('DEBUG: AJAX response received', xhr.status);
            
            if (xhr.status === 200) {
                console.log('DEBUG: Success response:', xhr.responseText);
                
                container.innerHTML = `
                    <div class="text-center">
                        <div class="joke-content">
                            ${xhr.responseText}
                        </div>
                        <div class="mt-3 text-muted">
                            <small>Debug Joke - Success!</small>
                        </div>
                    </div>
                `;
            } else {
                console.error('DEBUG: AJAX error:', xhr.status, xhr.statusText);
                container.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Debug Error: HTTP ${xhr.status} - ${xhr.statusText}
                    </div>
                `;
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('DEBUG: Network error');
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Debug Error: Network error occurred
            </div>
        `;
    };
    
    xhr.send();
}

function originalFetchJoke() {
    console.log('DEBUG: Trying original fetchJoke function');
    if (typeof window.fetchJoke === 'function') {
        window.fetchJoke();
    } else {
        document.getElementById('joke-container').innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Original fetchJoke function not found!
            </div>
        `;
    }
}

// Debug info on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DEBUG: DOM Content Loaded');
    
    const debugInfo = document.getElementById('debug-info');
    if (debugInfo) {
        let info = '<strong>Debug Information:</strong><br>';
        info += '• jQuery available: ' + (typeof $ !== 'undefined' ? 'Yes (v' + ($.fn ? $.fn.jquery : 'unknown') + ')' : 'No') + '<br>';
        info += '• window.fetchJoke available: ' + (typeof window.fetchJoke !== 'undefined' ? 'Yes' : 'No') + '<br>';
        info += '• joke-container found: ' + (document.getElementById('joke-container') ? 'Yes' : 'No') + '<br>';
        info += '• Current time: ' + new Date().toLocaleString() + '<br>';
        
        debugInfo.innerHTML = info;
    }
    
    // Auto-run debug version
    console.log('DEBUG: Auto-running debugFetchJoke in 2 seconds');
    setTimeout(debugFetchJoke, 2000);
});
</script>

<style>
    .joke-card {
        box-shadow: 0 6px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .joke-card:hover {
        transform: translateY(-5px);
    }
    
    #joke-container {
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .joke-content {
        font-size: 1.2rem;
    }
</style>
