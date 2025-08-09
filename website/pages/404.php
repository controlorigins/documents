<div class="container-fluid my-5">
    <div class="row">
        <div class="col-md-8 mx-auto text-center">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="display-1 text-danger mb-4">
                        <i class="bi bi-exclamation-triangle"></i> 404
                    </div>
                    
                    <h1 class="display-5 mb-4">Page Not Found</h1>
                    
                    <p class="lead mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                    
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="mb-3"><i class="bi bi-search me-2"></i>Try searching for it:</h5>
                                    <form action="/?page=search" method="POST">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Search documentation..." name="searchTerm">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Or try one of these popular pages:</h5>
                    
                    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                        <div class="col">
                            <a href="/?page=document_view" class="text-decoration-none">
                                <div class="card h-100 text-center hover-card">
                                    <div class="card-body">
                                        <i class="bi bi-file-earmark-text display-6 mb-3 text-primary"></i>
                                        <h5 class="card-title">Documents</h5>
                                        <p class="card-text">Browse all documentation files</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col">
                            <a href="/?page=data-analysis" class="text-decoration-none">
                                <div class="card h-100 text-center hover-card">
                                    <div class="card-body">
                                        <i class="bi bi-table display-6 mb-3 text-success"></i>
                                        <h5 class="card-title">Data Analysis</h5>
                                        <p class="card-text">Analyze CSV files</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col">
                            <a href="/?page=github" class="text-decoration-none">
                                <div class="card h-100 text-center hover-card">
                                    <div class="card-body">
                                        <i class="bi bi-github display-6 mb-3 text-dark"></i>
                                        <h5 class="card-title">GitHub</h5>
                                        <p class="card-text">View repository information</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <a href="/" class="btn btn-lg btn-primary">
                        <i class="bi bi-house-door me-2"></i> Go to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 2px solid transparent;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        border-color: #0d6efd;
    }
    
    /* Animation for the 404 text */
    .display-1 {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }
</style>