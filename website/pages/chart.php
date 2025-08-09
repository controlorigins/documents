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
        return true;
    }
    return false;
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
        return true;
    }
    return false;
}

// Function to delete a record
function delete($id)
{
    global $db;
    $stmt = $db->prepare('DELETE FROM contacts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return true;
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
    return true;
}

// Initialize message array
$messages = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    if ($action === 'create' || $action === 'update') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        
        if ($action === 'create') {
            if (create($name, $email)) {
                $messages[] = ['type' => 'success', 'text' => "Contact {$name} successfully added!"];
            } else {
                $messages[] = ['type' => 'danger', 'text' => "Contact with name {$name} and email {$email} already exists."];
            }
        } else {
            if (update($id, $name, $email)) {
                $messages[] = ['type' => 'success', 'text' => "Contact {$name} successfully updated!"];
            } else {
                $messages[] = ['type' => 'danger', 'text' => "Contact with name {$name} and email {$email} already exists."];
            }
        }
    } elseif ($action === 'delete') {
        if (delete($id)) {
            $messages[] = ['type' => 'success', 'text' => "Contact successfully deleted!"];
        }
    } elseif ($action === 'seed') {
        if (seedDatabase()) {
            $messages[] = ['type' => 'success', 'text' => "Database seeded with Star Trek characters!"];
        }
    }
}

// Output all records in an HTML table
$contacts = read();
?>

<div class="card shadow-sm fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-database me-2"></i>Database CRUD Operations</h2>
            <p class="text-light mb-0">Create, Read, Update and Delete contacts</p>
        </div>
        <div>
            <a href="/?file=ChatGPT%252FSessions%252FPHP%2BDatabase%2BCRUD.md" class="btn btn-light btn-sm">
                <i class="bi bi-info-circle me-1"></i> How this page was created
            </a>
            <a href="https://github.com/controlorigins/documents/blob/main/website/pages/crud.php" target="_blank" class="btn btn-light btn-sm">
                <i class="bi bi-code-slash me-1"></i> View Source
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Alert Messages -->
        <?php foreach ($messages as $message): ?>
        <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i><?php echo $message['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endforeach; ?>
        
        <div class="row">
            <!-- Create & Seed Section -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Add New Contact</h5>
                    </div>
                    <div class="card-body">
                        <!-- Create Form -->
                        <form method="post" class="mb-4">
                            <input type="hidden" name="action" value="create">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i> Name
                                </label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i> Email
                                </label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle me-1"></i> Add Contact
                            </button>
                        </form>
                        
                        <!-- Seed Form -->
                        <form method="post">
                            <input type="hidden" name="action" value="seed">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-database-fill me-1"></i> Seed Database with Characters
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact List Section -->
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-person-lines-fill me-2"></i>Contact List
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($contacts) > 0): ?>
                        <div class="table-responsive">
                            <table id="contactsTable" class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" width="50">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col" width="200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contacts as $contact): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($contact['id']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary edit-btn" 
                                                data-id="<?php echo $contact['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($contact['name']); ?>"
                                                data-email="<?php echo htmlspecialchars($contact['email']); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this contact?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info m-3">
                            <i class="bi bi-info-circle me-2"></i> No contacts found. Use the form to add new contacts or seed the database.
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (count($contacts) > 0): ?>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-badge me-1"></i> <span class="badge bg-primary rounded-pill"><?php echo count($contacts); ?></span> total contacts
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshTable">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="row">
            <div class="col-md-12">
                <h5><i class="bi bi-lightbulb me-2"></i>What is CRUD?</h5>
                <p>CRUD stands for <strong>C</strong>reate, <strong>R</strong>ead, <strong>U</strong>pdate, and <strong>D</strong>elete. These are the four basic operations of persistent storage:</p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="alert alert-success mb-3">
                            <h6><i class="bi bi-plus-circle me-2"></i>Create</h6>
                            <p class="mb-0">Add new records to the database</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-info mb-3">
                            <h6><i class="bi bi-eye me-2"></i>Read</h6>
                            <p class="mb-0">Retrieve and display existing records</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning mb-3">
                            <h6><i class="bi bi-pencil me-2"></i>Update</h6>
                            <p class="mb-0">Modify existing records with new data</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-danger mb-3">
                            <h6><i class="bi bi-trash me-2"></i>Delete</h6>
                            <p class="mb-0">Remove records from the database</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contact Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editContactModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Contact
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit-id">
                    
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">
                            <i class="bi bi-person me-1"></i> Name
                        </label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">
                            <i class="bi bi-envelope me-1"></i> Email
                        </label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('contactsTable')) {
            $('#contactsTable').DataTable({
                responsive: true,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]]
            });
        }
        
        // Handle edit button clicks
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editContactModal'));
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-email').value = email;
                
                editModal.show();
            });
        });
        
        // Handle refresh button
        const refreshButton = document.getElementById('refreshTable');
        if (refreshButton) {
            refreshButton.addEventListener('click', function() {
                window.location.reload();
            });
        }
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
    });
</script>