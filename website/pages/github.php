<div class='card'>
    <div class='card-header'>
        <h1>GitHub Repository Information</h1>  
        <a href='/?file=ChatGPT%252FSessions%252FGitHub%2BAPI%2BAccess%2BPHP.md'>
        How this page was created</a>
    </div>
    <div class="card-body">

<div class="container mt-5">
    <?php
    function fetchApiData($url, $token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: token ' . $token));
        curl_setopt($ch, CURLOPT_USERAGENT, 'ControlOriginsDocuments');
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    $token = "TBD";
    $cacheFile = 'data/commits_cache.json';
    $cacheTime = 60 * 60; // 1 hour
    
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        $cachedData = json_decode(file_get_contents($cacheFile), true);
    } else {
        $repoInfo = fetchApiData("https://api.github.com/repos/controlorigins/documents", $token);
        $commits = fetchApiData("https://api.github.com/repos/controlorigins/documents/commits?per_page=5", $token);

        foreach ($commits as &$commit) {
            $commit['details'] = fetchApiData($commit['url'], $token);
        }

        $cachedData = [
            'repoInfo' => $repoInfo,
            'commits' => $commits
        ];

        file_put_contents($cacheFile, json_encode($cachedData));
    }

    // Display general repository information
    if (isset($cachedData['repoInfo'])) {
        $repoInfo = $cachedData['repoInfo'];
        echo "<h2><a href='{$repoInfo['html_url']}'>{$repoInfo['html_url']}</a></h2>";
        echo "<p>{$repoInfo['description']}</p>";
        echo "<p>Size: {$repoInfo['size']}</p>";
        echo "<p>Language: {$repoInfo['language']}</p>";
        echo "<p>Open Issues: {$repoInfo['open_issues_count']}</p>";
        echo "<hr>";
    }
    // Display the last 5 commits
    if (isset($cachedData['commits']) && !empty($cachedData['commits'])) {
        echo '<h2>Last 5 Commits</h2>';
        echo '<ul class="list-group">';
        foreach ($cachedData['commits'] as $commit) {
            $date = new DateTime($commit['commit']['author']['date']);
            $formattedDate = $date->format('F j, Y g:i A');
            $commitDetails = $commit['details'];

            $filesImpacted = isset($commitDetails['files']) ? count($commitDetails['files']) : 'N/A';

            echo '<li class="list-group-item">';
            echo '<strong>Commit message:</strong> ' . $commit['commit']['message'] . '<br>';
            echo '<strong>Author:</strong> ' . $commit['commit']['author']['name'] . '<br>';
            echo '<strong>Date:</strong> ' . $formattedDate . '<br>';
            echo '<strong>Files Impacted:</strong> ' . $filesImpacted . '<br>';

            if (isset($commitDetails['files']) && is_array($commitDetails['files'])) {
                echo '<strong>File Names:</strong> <ul>';
                foreach ($commitDetails['files'] as $file) {
                    echo '<li>' . $file['filename'] . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<strong>File Names:</strong> N/A<br>';
            }

            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="alert alert-danger">No commits found or an error occurred.</div>';
    }
    ?>
</div>

<footer class="container mt-5">
    <ul>
        <?php
        if (file_exists($cacheFile)) {
            $cacheAge = time() - filemtime($cacheFile);
            $nextRefresh = $cacheTime - $cacheAge;
            $cacheDate = date('F j, Y g:i A', filemtime($cacheFile));

            // Format cache age and next refresh into hours, minutes, and seconds
            $cacheAgeFormatted = sprintf('%02d:%02d:%02d', ($cacheAge / 3600), ($cacheAge / 60 % 60), $cacheAge % 60);
            $nextRefreshFormatted = sprintf('%02d:%02d:%02d', ($nextRefresh / 3600), ($nextRefresh / 60 % 60), $nextRefresh % 60);

            echo "<li>Cache generated at: $cacheDate</li>";
            echo "<li>Cache age: $cacheAgeFormatted (hh:mm:ss)</li>";
            echo "<li>Time until next refresh: $nextRefreshFormatted (hh:mm:ss)</li>";
        } else {
            echo "<li>No cache information available</li>";
        }
        ?>
    </ul>
</footer>
</div>
    <div class="card-footer">
    <div class="container mt-5">
    <h2 class="text-center">Executive Summary</h2>
    <h4>Objective:</h4>
    <p>The primary goal of this project was to facilitate a deeper understanding of PHP programming and accessing the GitHub API. We aimed to create a web page that fetches and displays information about a specific GitHub repository.</p>

    <h4>Tasks Completed:</h4>
    <ul>
        <li>API Integration</li>
        <li>Data Display</li>
        <li>User-Friendly Formatting</li>
        <li>Caching Mechanism</li>
        <li>Security</li>
        <li>Error Handling</li>
    </ul>

    <h4>Summary of Steps Taken:</h4>
    <p>Initial Setup, API Calls, Data Processing, UI Design, Date Formatting, Caching, Security Measures, Footer Information, Testing and Debugging.</p>

    <p>By completing these tasks, we have successfully created a PHP-based web page that interacts with the GitHub API to fetch and display relevant information. This project serves as a practical example of how to leverage PHP for backend development and API interactions, providing valuable insights into real-world applications of these technologies.</p>
</div>
    </div>
</div>
<br/><br/><br/><br/>