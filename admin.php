<?php
<<<<<<< HEAD
// Authentication check - MUST be admin to access this page
require_once __DIR__ . '/config/auth_check.php';
require_role('admin');

=======
>>>>>>> 7fbec23cb932c4286c6ffe02a6e4b98f48321fe6
// Database connection
require_once 'db-config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_event':
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $date = mysqli_real_escape_string($conn, $_POST['date']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $location = mysqli_real_escape_string($conn, $_POST['location']);

                $sql = "INSERT INTO events (title, event_date, description, location) VALUES ('$title', '$date', '$description', '$location')";
                if (mysqli_query($conn, $sql)) {
                    $success = "Event added successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
                break;

            case 'delete_event':
                $id = (int)$_POST['event_id'];
                $sql = "DELETE FROM events WHERE id = $id";
                mysqli_query($conn, $sql);
                break;
<<<<<<< HEAD

            case 'add_user':
                $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
                $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
                $email = strtolower(trim(mysqli_real_escape_string($conn, $_POST['email'])));
                $password = $_POST['password'];
                $role = mysqli_real_escape_string($conn, $_POST['role']);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid email address.";
                } elseif (strlen($password) < 8) {
                    $error = "Password must be at least 8 characters.";
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified) VALUES ('$firstName', '$lastName', '$email', '$hash', '$role', 1)";
                    if (mysqli_query($conn, $sql)) {
                        $success = "User added successfully!";
                    } else {
                        $error = "Error: " . mysqli_error($conn);
                    }
                }
                break;

            case 'update_user':
                $id = (int)$_POST['user_id'];
                $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
                $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
                $email = strtolower(trim(mysqli_real_escape_string($conn, $_POST['email'])));
                $role = mysqli_real_escape_string($conn, $_POST['role']);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid email address.";
                } else {
                    $sql = "UPDATE users SET first_name='$firstName', last_name='$lastName', email='$email', role='$role' WHERE id=$id";
                    if (mysqli_query($conn, $sql)) {
                        $success = "User updated successfully!";
                    } else {
                        $error = "Error: " . mysqli_error($conn);
                    }
                }
                break;

            case 'delete_user':
                $id = (int)$_POST['user_id'];
                $sql = "DELETE FROM users WHERE id = $id";
                mysqli_query($conn, $sql);
                $success = "User deleted successfully!";
                break;

            case 'reset_password':
                $id = (int)$_POST['user_id'];
                $password = $_POST['new_password'];

                if (strlen($password) < 8) {
                    $error = "Password must be at least 8 characters.";
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $sql = "UPDATE users SET password_hash='$hash' WHERE id=$id";
                    if (mysqli_query($conn, $sql)) {
                        $success = "Password reset successfully!";
                    } else {
                        $error = "Error: " . mysqli_error($conn);
                    }
                }
                break;
=======
>>>>>>> 7fbec23cb932c4286c6ffe02a6e4b98f48321fe6
        }
    }
}

// Fetch all events
$events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC");
<<<<<<< HEAD

// Fetch all users
$users = mysqli_query($conn, "SELECT id, first_name, last_name, email, role, is_verified, created_at FROM users ORDER BY created_at DESC");
=======
>>>>>>> 7fbec23cb932c4286c6ffe02a6e4b98f48321fe6
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - McMaster Turkish Society</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #e30a17;
            font-size: 28px;
        }

        .dashboard-nav {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .nav-btn {
            padding: 8px 16px;
            background: #e30a17;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-btn:hover {
            background: #c00913;
        }

        .nav-btn.secondary {
            background: #666;
        }

        .nav-btn.secondary:hover {
            background: #555;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 12px 24px;
            background: #e30a17;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }

        .btn:hover {
            background: #c00913;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .events-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .events-table th,
        .events-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .events-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .events-table tr:hover {
            background: #f8f9fa;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .delete-btn:hover {
            background: #c82333;
        }
<<<<<<< HEAD

        .edit-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            margin-right: 5px;
        }

        .edit-btn:hover {
            background: #0056b3;
        }

        .reset-btn {
            background: #ffc107;
            color: #333;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            margin-right: 5px;
        }

        .reset-btn:hover {
            background: #e0a800;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-admin {
            background: #dc3545;
            color: white;
        }

        .badge-executive {
            background: #007bff;
            color: white;
        }

        .badge-member {
            background: #28a745;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
        }

        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #999;
        }

        .close:hover {
            color: #333;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
=======
>>>>>>> 7fbec23cb932c4286c6ffe02a6e4b98f48321fe6
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Admin Dashboard</h1>
            <div class="dashboard-nav">
<<<<<<< HEAD
                <a href="/~ulkere/M-TSA/index.html" class="nav-btn secondary">← Back to Website</a>
                <a href="#users" class="nav-btn">Manage Users</a>
=======
                <a href="index.html" class="nav-btn secondary">← Back to Website</a>
>>>>>>> 7fbec23cb932c4286c6ffe02a6e4b98f48321fe6
                <a href="#events" class="nav-btn">Manage Events</a>
                <a href="#gallery" class="nav-btn">Manage Gallery</a>
            </div>
        </header>

        <div class="section">
            <h2>Add New Event</h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="add_event">

                <div class="form-group">
                    <label for="title">Event Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="date">Event Date</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <button type="submit" class="btn">Add Event</button>
            </form>
        </div>

        <div class="section" id="events">
            <h2>Current Events</h2>

            <?php if (mysqli_num_rows($events) > 0): ?>
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = mysqli_fetch_assoc($events)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_event">
                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No events found. Add your first event above!</p>
            <?php endif; ?>
        </div>
<<<<<<< HEAD

        <!-- User Management Section -->
        <div class="section" id="users">
            <h2>User Management</h2>

            <button class="btn" onclick="document.getElementById('addUserModal').style.display='block'" style="margin-bottom: 20px;">+ Add New User</button>

            <?php if (mysqli_num_rows($users) > 0): ?>
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Verified</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo $user['is_verified'] ? '✓ Yes' : '✗ No'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="edit-btn" onclick='openEditModal(<?php echo json_encode($user); ?>)'>Edit</button>
                                    <button class="reset-btn" onclick="openResetModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name']); ?>')">Reset Password</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addUserModal').style.display='none'">&times;</span>
            <h2>Add New User</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_user">

                <div class="form-row">
                    <div class="form-group">
                        <label for="add_first_name">First Name</label>
                        <input type="text" id="add_first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="add_last_name">Last Name</label>
                        <input type="text" id="add_last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="add_email">Email</label>
                    <input type="email" id="add_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="add_password">Password</label>
                    <input type="password" id="add_password" name="password" required minlength="8">
                    <small>Minimum 8 characters</small>
                </div>

                <div class="form-group">
                    <label for="add_role">Role</label>
                    <select id="add_role" name="role" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="member">Member</option>
                        <option value="executive">Executive</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn">Add User</button>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('editUserModal').style.display='none'">&times;</span>
            <h2>Edit User</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_first_name">First Name</label>
                        <input type="text" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_last_name">Last Name</label>
                        <input type="text" id="edit_last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <select id="edit_role" name="role" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="member">Member</option>
                        <option value="executive">Executive</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn">Update User</button>
            </form>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('resetPasswordModal').style.display='none'">&times;</span>
            <h2>Reset Password</h2>
            <p id="reset_user_name"></p>
            <form method="POST" action="">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="reset_user_id">

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8">
                    <small>Minimum 8 characters</small>
                </div>

                <button type="submit" class="btn">Reset Password</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_first_name').value = user.first_name;
            document.getElementById('edit_last_name').value = user.last_name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('editUserModal').style.display = 'block';
        }

        function openResetModal(userId, userName) {
            document.getElementById('reset_user_id').value = userId;
            document.getElementById('reset_user_name').textContent = 'Reset password for: ' + userName;
            document.getElementById('resetPasswordModal').style.display = 'block';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
=======
    </div>
>>>>>>> 7fbec23cb932c4286c6ffe02a6e4b98f48321fe6
</body>
</html>
