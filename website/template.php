<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : '404 - Not Found'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        /* Add alternating row background colors */
        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        pre {
            background-color: beige;
            max-width: 100%;
            border: 1px solid #ccc;
        }
        
        pre code {
            white-space: pre-wrap;
            word-wrap: break-word;
            max-width: 100%;
        }

    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1>PHP Document Website</h1>
        <h2><?php echo isset($content[$page]['title']) ? $content[$page]['title'] : '404 - Not Found'; ?></h2>
    </header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/">Documentation</a>  <!-- Add a Navbar brand (optional) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php
                    foreach ($content as $key => $value) {
                        if ($key !== '404') {
                            echo '<li class="nav-item"><a class="nav-link" href="?page=' . $key . '">' . $value['title'] . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>    <main class="container-fluid mt-4">
        <?php
        $phpFile = "pages/$page.php";

        if (file_exists($phpFile)) {
            include $phpFile;
        } elseif (isset($content[$page]['content'])) {
            echo '<div class="alert alert-info">' . $content[$page]['content'] . '</div>';
        } else {
            include "pages/404.php";
        }
        ?>
    </main>
    <footer class="bg-dark text-white py-2">
      <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 float-left text-left">
                <!-- Left column content goes here -->
            </div>
            <div class="col-md-6 text-center">
                <p>&copy; <?php echo date('Y'); ?> Mark Hazleton</p>
            </div>
            <div class="col-md-3 float-right text-right">
                <p><a href='https://markhazleton.com' target="_new" title='MarkHazleton.com' alt='MarkHazleton.com'>markhazleton.com</a></p>
            </div>
        </div>
      </div>
    </footer>

    <!-- Include Bootstrap JS and jQuery (if needed) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>
</html>
