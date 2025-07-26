<?php
session_start();

$error = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $conn = new mysqli('localhost', 'root', '', 'sharenotes');
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['name']; // ✅ used in header to show logged in user
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - ShareNotes</title>
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
      <h1 class="text-2xl font-bold mt-4 text-gray-800">Login to <span class="text-red-600">ShareNotes</span></h1>
    </div>

    <?php if ($success): ?>
      <div class="mb-4 text-green-600 font-semibold text-center">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="mb-4 text-red-600 font-semibold text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4" action="">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input id="email" name="email" type="email" placeholder="you@example.com" required
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-400" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input id="password" name="password" type="password" placeholder="••••••••" required
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-400" />
      </div>

      <button type="submit" class="w-full bg-emerald-500 text-white font-semibold py-2 rounded-md hover:bg-emerald-600 transition-shadow shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-300">
        Login
      </button>
    </form>

    <div class="mt-6 flex flex-col space-y-2 text-center text-sm text-gray-500">
      <p>
        Don’t have an account?
        <a href="register.php" class="text-emerald-600 hover:underline">Register</a>
      </p>
      <p>
        <a href="forgot-password.php" class="text-blue-600 hover:underline">Forgot password?</a>
      </p>
    </div>
  </div>

</body>
</html>
