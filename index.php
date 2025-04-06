<?php
session_start();

$messages_file = 'messages.json';

$users = [
    'admin' => 'password123',
    'user1' => 'securepass',
    'user2' => 'user2pass'
];

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($users[$username]) && $users[$username] === $password) {
        $_SESSION['user'] = $username;
        $_SESSION['loggedin'] = true;
        setcookie("user_session", session_id(), time() + 3600, "/");
        header("Location: index.php");
        exit;
    } else {
        $error_message = "Invalid username or password";
    }
}

// Check login
$loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie("user_session", "", time() - 3600, "/");
    header("Location: index.php");
    exit;
}

// Load messages
$messages = [];
if (file_exists($messages_file)) {
    $messages_json = file_get_contents($messages_file);
    $messages = json_decode($messages_json, true) ?: [];
}

// Handle message POST
if ($loggedin && isset($_POST['message']) && !empty($_POST['message'])) {
    $newMessage = [
        'id' => uniqid(),
        'user' => $_SESSION['user'],
        'content' => $_POST['message'],
        'time' => date('Y-m-d H:i:s')
    ];
    $messages[] = $newMessage;
    file_put_contents($messages_file, json_encode($messages));
    header("Location: index.php");
    exit;
}



// Handle message DELETE
if ($loggedin && isset($_POST['delete']) && !empty($_POST['delete'])) {
    $messageId = $_POST['delete'];
    $messages = array_filter($messages, function ($msg) use ($messageId) {
        return $msg['id'] !== $messageId;
    });
    $messages = array_values($messages); // Reset array keys
    file_put_contents($messages_file, json_encode($messages));
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Hijacking Demo</title>
  
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .login-form, .message-form, .messages { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .message { padding: 10px; margin-bottom: 10px; background-color: #f9f9f9; border-radius: 5px; }
        .message-header { display: flex; justify-content: space-between; font-size: 0.8em; color: #666; }
        .message-content { margin-top: 5px; }
        .message-preview {margin-top: 10px;}
        .view-message {color: #007bff;text-decoration: none;cursor: pointer;}
        .view-message:hover {text-decoration: underline;}
        input[type="text"], input[type="password"], textarea { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .status { margin-bottom: 20px; padding: 10px; background-color: #e9f7ef; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .restricted { background-color: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }

        .delete-btn {
        padding: 2px 8px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 0.8em;
        margin-left: 10px;
    }
    
    .delete-btn:hover {
        background-color: #c82333;
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Session Hijacking Demonstration</h1>
        
        <div class="status">
            <?php if ($loggedin): ?>
                <p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong> 
                   [<a href="?logout=1">Logout</a>]</p>
            <?php else: ?>
                <p>Not logged in. <strong>Please login to access all features.</strong></p>
            <?php endif; ?>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="error">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!$loggedin): ?>
            <div class="login-form">
                <h2>Login</h2>
                <form method="post">
                    <div>
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div>
                        <small>Try using: user1/securepass or user2/user2pass</small>
                    </div>
                    <button type="submit">Login</button>
                </form>
            </div>
            
            <div class="restricted">
                <h3>⚠️ Restricted Area</h3>
                <p>Please login to view the message board. The message board contains sensitive information only accessible to authenticated users.</p>
            </div>
        <?php else: ?>
            <div class="message-form">
                <h2>Post a Message</h2>
                <form method="post">
                    <div>
                        <textarea name="message" rows="3" placeholder="Enter your message..." required></textarea>
                    </div>
                    <button type="submit">Post Message</button>
                </form>
            </div>
            
            // ...existing code...
            <div class="messages">
                <h2>Message Board</h2>
                <?php if (empty($messages)): ?>
                    <p>No messages yet.</p>
                <?php else: ?>
                    <?php foreach($messages as $msg): ?>
                        <div class="message">
                            <div class="message-header">
                                <span><strong><?php echo htmlspecialchars($msg['user']); ?></strong> has sent a message</span>
                                <span>
                                    <?php echo htmlspecialchars($msg['time']); ?>
                                    <?php if ($loggedin && $_SESSION['user'] === $msg['user']): ?>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="delete" value="<?php echo htmlspecialchars($msg['id']); ?>">
                                            <button type="submit" class="delete-btn">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="message-preview">
                                <a href="#" class="view-message" data-message-id="<?php echo htmlspecialchars($msg['id']); ?>">Click to view message</a>
                            </div>
                            <div id="message-content-<?php echo $msg['id']; ?>" class="message-content" style="display: none;">
                                <?php echo $msg['content']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
      
    </div>
</body>


<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-message').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const messageId = this.getAttribute('data-message-id');
            const contentDiv = document.getElementById('message-content-' + messageId);
            
            if (contentDiv.style.display === 'none') {
                contentDiv.style.display = 'block';
                this.textContent = 'Hide message';
            } else {
                contentDiv.style.display = 'none';
                this.textContent = 'Click to view message';
            }
        });
    });
});
</script>
</html>