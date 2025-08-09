<?php
// layout.php - Base Layout Template
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Control Origins Documentation'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables Bootstrap 5 Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }
        
        /* Sidebar styles */
        .sidebar {
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        
        /* Custom card styling */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), #0b5ed7);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.25rem;
        }
        
        .card-footer {
            background-color: var(--light-bg);
            border-radius: 0 0 10px 10px !important;
        }
        
        /* Navbar customization */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        /* Button styling */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.25rem;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        
        /* Table styling */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* Custom thumbnail image size */
        .thumbnail-img {
            max-width: 100px;
            max-height: 80px;
            object-fit: cover;
        }
        
        /* Footer styling */
        footer {
            background-color: var(--dark-bg);
            color: white;
            padding: 1.5rem 0;
        }
        
        /* Animation effects */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Navigation pills */
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid #dee2e6;
                margin-bottom: 1rem;
            }
        }
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
                        <a class="nav-link" href="/?page=chart">
                            <i class="bi bi-bar-chart me-1"></i> Charts
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
                        <a class="nav-link" href="/?page=crud">
                            <i class="bi bi-database me-1"></i> CRUD
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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- DataTables Initialization Script -->
    <script>
        $(document).ready(function() {
            if ($('#myTable').length) {
                $('#myTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
                });
            }
        });
    </script>
</body>
</html>