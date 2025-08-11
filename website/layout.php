<?php
// layout.php - Base Layout Template
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? PHPSPARK_BRAND; ?></title>
    <?php require_once __DIR__ . '/includes/seo.php'; ?>
    <!-- Compiled and minified CSS -->
    <link href="/assets/css/site.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Any page-specific inline styles can go here if needed */
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <a href="#main-content" class="visually-hidden-focusable position-absolute top-0 start-0 p-2 bg-dark text-white">Skip to content</a>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" role="navigation" aria-label="Main navigation">
        <div class="container-fluid">
            <a class="navbar-brand" href="/" aria-label="Home: <?php echo e(PHPSPARK_BRAND); ?>">
                <i class="bi bi-journals me-2"></i><?php echo e(PHPSPARK_SHORT); ?>
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
                        <a class="nav-link" href="/?page=chart">
                            <i class="bi bi-bar-chart me-1"></i> Charts
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
                        <a class="nav-link" href="/?page=article_list">
                            <i class="bi bi-journal-text me-1"></i> Articles
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
    <main id="main-content" class="container-fluid mt-4 mb-5" role="main">
        <div class="row">
            <!-- Page Content -->
            <div class="col-12">
                <?php echo $pageContent ?? ''; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo e(PHPSPARK_BRAND); ?></h5>
                    <p>
                        <i class="bi bi-link-45deg"></i> 
                        <a href="https://phpdocspark.azurewebsites.net/" class="text-light">Live Demo</a>
                        <br>
                        <a href="<?php echo e(PHPSPARK_SUITE_URL); ?>" class="text-light">Part of the <?php echo e(PHPSPARK_SUITE_NAME); ?> Suite</a>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>
                        <a href="https://github.com/markhazleton/PHPDocSpark" class="text-light me-3">
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
                    <small>© <?php echo date('Y'); ?> <?php echo e(PHPSPARK_AUTHOR); ?>. Released under the MIT License. <a href="/LICENSE" class="text-decoration-underline text-light">View License</a>.</small>
                    <br>
                    <small class="text-muted">
                        <i class="bi bi-code-square me-1"></i>
                        <?php echo e(getSiteBuildString()); ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Compiled and minified JavaScript -->
    <script src="/assets/js/site.js"></script>
</body>
</html>