<div class='card'>
    <div class='card-header'>
        <h1>Database CRUD</h1>  
        <a href='?file=ChatGPT%252FSessions%252FPHP%2BDatabase%2BCRUD.md'>
        How this page was created</a><br/>
        <a href='https://github.com/controlorigins/documents/blob/main/website/pages/crud.php'target='_blank'>View Page Source</a>
    </div>
    <div class="card-body">


<?php
// Database connection
$db = new PDO('sqlite:data/database.db');

// Create table if not exists
$db->exec("CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT)");

// Function to check if a record already exists
function recordExists($name, $email, $excludeId = null)
{
    global $db;
    $query = 'SELECT * FROM contacts WHERE name = :name AND email = :email';
    $params = [':name' => $name, ':email' => $email];
    if ($excludeId !== null) {
        $query .= ' AND id != :id';
        $params[':id'] = $excludeId;
    }
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch() !== false;
}

// Function to insert a new record
function create($name, $email)
{
    if (!recordExists($name, $email)) {
        global $db;
        $stmt = $db->prepare('INSERT INTO contacts (name, email) VALUES (:name, :email)');
        $stmt->execute([':name' => $name, ':email' => $email]);
    }
}

// Function to read all records
function read()
{
    global $db;
    $stmt = $db->query('SELECT * FROM contacts');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to update a record
function update($id, $name, $email)
{
    if (!recordExists($name, $email, $id)) {
        global $db;
        $stmt = $db->prepare('UPDATE contacts SET name = :name, email = :email WHERE id = :id');
        $stmt->execute([':id' => $id, ':name' => $name, ':email' => $email]);
    }
}

// Function to delete a record
function delete($id)
{
    global $db;
    $stmt = $db->prepare('DELETE FROM contacts WHERE id = :id');
    $stmt->execute([':id' => $id]);
}
// Function to seed the database with Star Trek characters
function seedDatabase()
{
    global $db;
    $characters = [
        ['name' => 'James T. Kirk', 'email' => 'kirk@starfleet.com'],
        ['name' => 'Spock', 'email' => 'spock@starfleet.com'],
        ['name' => 'Leonard McCoy', 'email' => 'mccoy@starfleet.com'],
        ['name' => 'Montgomery Scott', 'email' => 'scottie@starfleet.com'],
        ['name' => 'Hikaru Sulu', 'email' => 'sulu@starfleet.com'],
        ['name' => 'Nyota Uhura', 'email' => 'uhura@starfleet.com'],
        ['name' => 'Pavel Chekov', 'email' => 'chekov@starfleet.com'],
        ['name' => 'Christine Chapel', 'email' => 'nurse.chapel@starfleet.com'],
        ['name' => 'Janice Rand', 'email' => 'jrand@starfleet.com'],
        ['name' => 'Carol Marcus', 'email' => 'cmarcus@starfleet.com'],
        ['name' => 'David Marcus', 'email' => 'dmarcus@starfleet.com']
    ];
    foreach ($characters as $character) {
        create($character['name'], $character['email']);
    }
}
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    if ($action === 'create' || $action === 'update') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        if ($action === 'create') {
            create($name, $email);
        } else {
            update($id, $name, $email);
        }
    } elseif ($action === 'delete') {
        delete($id);
    } elseif ($action === 'seed') {
        seedDatabase();
    }
}

// Output all records in an HTML table
$contacts = read();
?>

<div class="container">
    <form method="post">
        <input type="hidden" name="action" value="seed">
        <button type="submit">Seed Database</button>
    </form>
    <br/><br/>
    <!-- Form to create a new record -->
    <form method="post">
        <input type="hidden" name="action" value="create">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Add Contact</button>
    </form>
    <br/><br/>
    <table id="myTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['id']); ?></td>
                                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td>
                                    <!-- Form to update a record -->
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($contact['id']); ?>">
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($contact['name']); ?>" required>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>" required>
                                        <button type="submit">Update</button>
                                    </form>
                                    <!-- Form to delete a record -->
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($contact['id']); ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
    <div class="card-footer">
    <div class="executive-summary">
        <h3>Explanation</h3>
    <p>
        In this exercise, we have crafted a digital platform that allows us to manage a list of contacts. This platform is akin to an online address book, where we can add new contacts, modify existing contacts, or remove contacts as needed. This is achieved through a web page which interacts with a database, where all our contact information is securely stored.
        <br><br>
        Here are the steps we took to create this platform:
        <ol>
            <li><strong>Database Setup</strong>: We established a database—a secure digital vault—to hold our contact information. Created a table within the database specifically for storing contacts, with fields for the contact's name and email address.</li>
            <li><strong>Web Page Creation</strong>: We developed a web page that serves as the user interface for interacting with our contact list. Incorporated user-friendly forms to add new contacts or update existing ones. Integrated a visual table displaying all contacts, with options to modify or delete each contact.</li>
            <li><strong>Functional Code</strong>: Behind the scenes, we wrote code that takes our actions on the web page (like adding a new contact), and updates the database accordingly. Ensured that the code checks for duplicate contacts to avoid redundancy, and implemented a smooth process to modify or remove contacts.</li>
            <li><strong>Styling and User Experience</strong>: Utilized Bootstrap, a popular styling framework, to ensure our platform is visually appealing and easy to navigate. Ensured that the platform is responsive, meaning it’s easy to use whether accessed on a computer, tablet, or smartphone.</li>
            <li><strong>Testing and Troubleshooting</strong>: Conducted tests to ensure the platform works as intended, identifying and correcting a couple of errors to ensure a smooth user experience.</li>
        </ol>
        This platform now stands as a robust tool for managing our contact list, ensuring we have an organized, easily accessible database of contacts which can be managed effortlessly from a user-friendly web interface.
    </p>

    </div>
    </div>
</div>
<br/><br/><br/><br/>
