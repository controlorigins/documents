<?php
// Define the file path to the JSON file
$jsonFile = 'content.json';

// Function to read and decode JSON file
function readJSONFile($file) {
    $contents = file_get_contents($file);
    return json_decode($contents, true);
}

// Function to save JSON data to the file
function saveJSONFile($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    if (file_put_contents($file, $json) !== false) {
        return true;
    } else {
        return false;
    }
}

// Initialize the data
$contents = readJSONFile($jsonFile);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which action is requested
    if (isset($_POST['add'])) {
        // ... (same as before)
    } elseif (isset($_POST['update'])) {
        // ... (same as before)
    } elseif (isset($_POST['delete'])) {
        // ... (same as before)
    } elseif (isset($_POST['rename'])) {
        // ... (same as before)
    }

    // Save the updated data back to the JSON file
    if (saveJSONFile($jsonFile, $contents)) {
        echo "Data successfully updated.";
    } else {
        echo "Error updating data.";
    }
}

?>

<div class="container">
        <h1>Contents</h1>
        <ul class="list-group">
            <?php foreach ($contents as $key => $entry) { ?>
                <li class="list-group-item">
                    <h4 class="mb-3"><?php echo $entry['title']; ?></h4>
                    <p><?php echo $entry['content']; ?></p>
                    <!-- Add a button to trigger the edit form -->
                    <button class="btn btn-primary" onclick="editEntry('<?php echo $key; ?>')">Edit</button>
                </li>
            <?php } ?>
        </ul>
        <h2 class="mt-4">Edit Entry</h2>
        <!-- The edit form is initially hidden -->
        <form method="post" id="editForm" style="display: none;">
            <input type="hidden" name="entry_key" id="editKey" value="">
            <div class="mb-3">
                <label for="edit_title" class="form-label">New Title</label>
                <input type="text" class="form-control" name="updated_title" id="editTitle" placeholder="New Title">
            </div>
            <div class="mb-3">
                <label for="edit_content" class="form-label">New Content</label>
                <input type="text" class="form-control" name="updated_content" id="editContent" placeholder="New Content">
            </div>
            <button type="submit" class="btn btn-primary" name="update">Update</button>
        </form>
    </div>

    <!-- JavaScript to show/hide the edit form -->
    <script>
        function editEntry(key) {
            // Get the entry data based on the key
            var entry = <?php echo json_encode($contents); ?>[key];
            if (entry) {
                // Populate the edit form with the entry data
                document.getElementById('editKey').value = key;
                document.getElementById('editTitle').value = entry.title;
                document.getElementById('editContent').value = entry.content;
                // Show the edit form
                document.getElementById('editForm').style.display = 'block';
            }
        }
    </script>