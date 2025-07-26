<?php
session_start();

$errors = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors = 'Invalid email address.';
    } elseif ($password !== $confirm_password) {
        $errors = 'Passwords do not match.';
    } else {
        $conn = new mysqli('localhost', 'root', '', 'sharenotes');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors = 'Email is already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $insert = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if (!$insert) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $insert->bind_param("sss", $name, $email, $hashedPassword);

            if ($insert->execute()) {
                // Automatically log in the user
                $_SESSION['user_id'] = $insert->insert_id;
                $_SESSION['user'] = $name;

                header('Location: index.php');
                exit;
            } else {
                $errors = 'Error: ' . $conn->error;
            }
            $insert->close();
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - ShareNotes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-gray-50 to-gray-200 min-h-screen flex items-center justify-center px-4">

  <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-sm">
    <div class="flex flex-col items-center mb-6">
      <div class="w-14 h-14 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 48 48" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-orange-600">
          <path stroke-linecap="round" stroke-linejoin="round" d="M32 14l-6 6-1.5-1.5M16 34h12a4 4 0 004-4v-3a4 4 0 00-4-4H20m-4 11v-7m0 0c0-1.104.448-2.104 1.172-2.828M12 20h4a4 4 0 004-4v-3a4 4 0 00-4-4h-2" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold mt-4 text-gray-800">Register for <span class="text-red-600">ShareNotes</span></h1>
    </div>

    <?php if ($errors): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?= htmlspecialchars($errors) ?></div>
    <?php elseif ($success): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4" novalidate>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Your name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-400" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@example.com" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-400" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" placeholder="Create password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-400" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Repeat password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-400" required />
      </div>

      <button type="submit" class="w-full bg-emerald-500 text-white font-semibold py-2 rounded-md hover:bg-emerald-600 transition-shadow shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-300">
        Create Account
      </button>
    </form>

    <div class="mt-6 flex flex-col space-y-2 text-center text-sm text-gray-500">
      <p>
        Already have an account? <a href="login.php" class="text-emerald-600 hover:underline">Login</a>
      </p>
      <p>
        <a href="forgot-password.php" class="text-blue-600 hover:underline">Forgot password?</a>
      </p>
    </div>
  </div>

</body>
</html>
