<?php
// Load app configuration and database connection.
require __DIR__ . '/config.php';

// If already logged in, go directly to dashboard.
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Available animal avatars for learners to choose from
$avatars = [
    'lion' => '🦁',
    'bear' => '🐻',
    'panda' => '🐼',
    'rabbit' => '🐰',
    'fox' => '🦊',
    'monkey' => '🐵',
    'tiger' => '🐯',
    'penguin' => '🐧',
    'owl' => '🦉',
    'elephant' => '🐘',
    'giraffe' => '🦒',
    'zebra' => '🦓',
];

// Handle account creation (simplified - just nickname + avatar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_account') {
        $nickname = trim($_POST['nickname'] ?? '');
        $avatar = trim($_POST['avatar'] ?? '');

        // Validate inputs
        if ($nickname === '') {
            $error = 'Please enter a nickname for your profile.';
        } elseif ($avatar === '' || !array_key_exists($avatar, $avatars)) {
            $error = 'Please select an animal avatar.';
        } else {
            // Create a unique learner account with nickname and avatar
            try {
                $stmt = getPDO()->prepare('
                    INSERT INTO users (name, email, password, role)
                    VALUES (:name, :email, :password, :role)
                ');

                // Generate a unique email based on nickname + timestamp
                $uniqueId = time() . rand(1000, 9999);
                $email = strtolower(str_replace(' ', '', $nickname)) . '_' . $uniqueId . '@readready.local';
                
                // Password is the avatar name (simple for children)
                $hash = password_hash($avatar, PASSWORD_DEFAULT);

                $stmt->execute([
                    ':name' => $nickname,
                    ':email' => $email,
                    ':password' => $hash,
                    ':role' => 'learner',
                ]);

                // Store avatar choice in a custom field or session
                $_SESSION['user'] = [
                    'id' => (int) getPDO()->lastInsertId(),
                    'name' => $nickname,
                    'email' => $email,
                    'role' => 'learner',
                    'avatar' => $avatar,
                ];

                redirect('dashboard.php');
            } catch (Exception $e) {
                $error = 'Error creating account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadReady | Welcome</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="landing-bg">
    <div class="landing-shell">
        <div class="landing-panel intro-panel">
            <div class="brand large">
                <span class="brand-mark">📘</span>
                <span>ReadReady</span>
            </div>

            <h1>Learn, Play, and Grow</h1>
            <p>
                A colorful and child-friendly educational platform that combines
                vocabulary learning, nursery development, quizzes, and progress tracking.
            </p>

            <div class="feature-list">
                <span>📚 Vocabulary</span>
                <span>🔤 Alphabet</span>
                <span>🔢 Math</span>
                <span>🏆 Badges</span>
            </div>
        </div>

        <div class="landing-panel form-panel">
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <h2>Create Your Profile</h2>
            <p style="color: #64748b; margin-bottom: 20px;">Choose a nickname and your favorite animal!</p>

            <form method="POST" class="create-account-form">
                <input type="hidden" name="action" value="create_account">

                <!-- Nickname Input -->
                <div class="form-group">
                    <label for="nickname">Your Nickname</label>
                    <input 
                        type="text" 
                        id="nickname" 
                        name="nickname" 
                        placeholder="e.g., Luna, Alex, Sam" 
                        maxlength="20"
                        required
                    >
                </div>

                <!-- Avatar Selection -->
                <div class="form-group">
                    <label>Pick Your Animal Avatar</label>
                    <div class="avatar-grid">
                        <?php foreach ($avatars as $key => $emoji): ?>
                            <label class="avatar-option">
                                <input 
                                    type="radio" 
                                    name="avatar" 
                                    value="<?php echo e($key); ?>" 
                                    required
                                >
                                <span class="avatar-display"><?php echo $emoji; ?></span>
                                <span class="avatar-name"><?php echo ucfirst($key); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="primary-btn">Start Learning!</button>
            </form>

            <div class="demo-box">
                <p><strong>🎓 Demo Account</strong></p>
                <p>Admin: admin@readready.test / admin123</p>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
