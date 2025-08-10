<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joke Debug</title>
    <link href="assets/css/site.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Joke Debug Test</h1>
        
        <div id="joke-container" class="border p-3 mb-3">
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        
        <button onclick="testFetchJoke()" class="btn btn-primary">Test Fetch Joke</button>
        <button onclick="testJQuery()" class="btn btn-secondary">Test jQuery</button>
        <button onclick="testAjax()" class="btn btn-success">Test AJAX</button>
        
        <div id="debug-output" class="mt-3"></div>
    </div>

    <script src="assets/js/site.js"></script>
    <script>
        function testFetchJoke() {
            console.log('Testing fetchJoke function...');
            console.log('fetchJoke exists:', typeof window.fetchJoke);
            
            if (typeof window.fetchJoke === 'function') {
                window.fetchJoke();
            } else {
                document.getElementById('debug-output').innerHTML = '<div class="alert alert-danger">fetchJoke function not found!</div>';
            }
        }
        
        function testJQuery() {
            console.log('Testing jQuery...');
            console.log('$ exists:', typeof $);
            console.log('jQuery exists:', typeof jQuery);
            
            if (typeof $ !== 'undefined') {
                $('#debug-output').html('<div class="alert alert-success">jQuery is working! Version: ' + $.fn.jquery + '</div>');
            } else {
                document.getElementById('debug-output').innerHTML = '<div class="alert alert-danger">jQuery not found!</div>';
            }
        }
        
        function testAjax() {
            console.log('Testing AJAX call...');
            if (typeof $ !== 'undefined') {
                $.ajax({
                    url: 'pages/fetch_joke.php',
                    method: 'GET',
                    success: function(response) {
                        $('#debug-output').html('<div class="alert alert-success">AJAX Success:<br>' + response + '</div>');
                    },
                    error: function(xhr, status, error) {
                        $('#debug-output').html('<div class="alert alert-danger">AJAX Error: ' + error + '</div>');
                    }
                });
            } else {
                document.getElementById('debug-output').innerHTML = '<div class="alert alert-danger">jQuery required for AJAX test!</div>';
            }
        }
        
        // Test on page load
        $(document).ready(function() {
            console.log('Document ready fired');
            console.log('joke-container found:', $('#joke-container').length);
            
            setTimeout(function() {
                testFetchJoke();
            }, 1000);
        });
    </script>
</body>
</html>
