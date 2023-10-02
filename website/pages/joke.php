<div class='card'>
    <div class='card-header'>
        <h1>Fetch Joke</h1>  
        <a href='/?file=ChatGPT%252FSessions%252FCreate%2BPHP%2BJoke%2BPage.md'>
        How this page was created</a>
    </div>
    <div class="card-body">

    <div class="container mt-5">
    <div id="joke-container" class="mb-3 p-3 border rounded">
        <!-- Joke will be displayed here -->
    </div>

    <button onclick="fetchJoke()" class="btn btn-primary mb-3">Fetch a new Joke</button>

    <div class="executive-summary">
        <h3>Explanation</h3>
        <p>
            The primary objective of this page was to showcase the capability of PHP 
            in making calls to an external API and dynamically displaying the fetched data 
            on the company's website. A 'Joke Display' feature was developed and integrated, 
            which fetches humor content from an external REST API and displays it on the website, 
            providing a clear demonstration of PHP's capacity for real-time data retrieval 
            and manipulation on a web platform.</p>
        <h4>Key Points</h4>
        <dl>
            <dt>PHP to API Interaction:</dt>
            <dd>The core of this demonstration revolved around developing a PHP script capable of making GET requests to an external joke API. This illustrated how PHP can interact with APIs to fetch real-time data.</dd>
            <dt>Dynamic Content Display:</dt>
            <dd> The fetched data was dynamically displayed on the website, exemplifying how real-time data from external sources can be incorporated into our web pages to enhance content dynamism.</dd>
            <dt>User Engagement: </dt>
            <dd>A 'Fetch a new Joke' button was added, allowing users to interact and view new content, thus demonstrating a simple yet effective user engagement feature.</dd>
            <dt>Styling and Formatting: </dt>
            <dd>Employing the Bootstrap framework, the visual presentation of the dynamic content was enhanced to ensure a pleasant user experience while keeping the focus on the PHP to API interaction demonstration.</dd>
            <dt>Issue Resolution: </dt>
            <dd>An initial script loading issue was resolved ensuring smooth operation of the feature on both manual user interaction and automatic page load, demonstrating our ability to troubleshoot and optimize the web feature.</dd>
        </dl>
        <p>The success of this demonstration underscores the versatility and capabilities of PHP in interacting with external APIs to enrich web content dynamically.</p>   
        <p>As we REST assured, the project added not only a technical demonstration but also a touch of humor to our digital platform, providing a lighthearted example of how APIs can be leveraged to keep the content fresh and engaging.</p>
        
        <h4>Step-by-Step Breakdown</h4>
        <ol>
            <li>Developed a PHP script to make GET requests to the external joke API.</li>
            <li>Created a button on the webpage to trigger the fetching of new jokes.</li>
            <li>Resolved an initial script loading issue to ensure smooth operation of the feature.</li>
        </ol>
    </div>
</div>

</div>
    <div class="card-footer">
        <br/>
        <br/>
    </div>
</div>
<br/><br/><br/><br/>


<script type="text/javascript">
    function fetchJoke() {
        $.ajax({
            url: 'pages/fetch_joke.php',
            method: 'GET',
            success: function(response) {
                $('#joke-container').html(response);
            }
        });
    }

    // Use window.onload to ensure jQuery is loaded
    window.onload = function() {
        fetchJoke();
    };

</script>
