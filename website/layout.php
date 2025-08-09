<?php
// layout.php - Base Layout Template
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Control Origins Documentation'; ?></title>
    <!-- Compiled and minified CSS -->
    <link href="/assets/css/site.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Any page-specific inline styles can go here if needed */
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="bi bi-journals me-2"></i>Control Origins Docs
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/?page=document_view">
                            <i class="bi bi-file-earmark-text me-1"></i> Documents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/?page=data-analysis">
                            <i class="bi bi-table me-1"></i> Data Analysis
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/?page=database">
                            <i class="bi bi-database me-1"></i> Database
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/?page=project_list">
                            <i class="bi bi-kanban me-1"></i> Projects
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/?page=github">
                            <i class="bi bi-github me-1"></i> GitHub
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/?page=joke">
                            <i class="bi bi-emoji-laughing me-1"></i> Jokes
                        </a>
                    </li>
                </ul>
                <form class="d-flex" action="/?page=search" method="POST">
                    <input class="form-control me-2" type="search" name="searchTerm" placeholder="Search documents...">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4 mb-5">
        <div class="row">
            <!-- Page Content -->
            <div class="col-12">
                <?php echo $pageContent ?? ''; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Control Origins Documentation</h5>
                    <p>
                        <i class="bi bi-link-45deg"></i> 
                        <a href="https://controlorigins-docs.azurewebsites.net/" class="text-light">Published Website</a>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>
                        <a href="https://github.com/controlorigins/documents" class="text-light me-3">
                            <i class="bi bi-github"></i> GitHub Repository
                        </a>
                        <a href="https://markhazleton.com/creating-a-php-website-with-chat-gpt.html" class="text-light">
                            <i class="bi bi-file-earmark-text"></i> Creation Article
                        </a>
                    </p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <small>© <?php echo date('Y'); ?> Control Origins. All documentation in this repository is proprietary and confidential.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Compiled and minified JavaScript -->
    <script src="/assets/js/site.js"></script>
</body>
</html>