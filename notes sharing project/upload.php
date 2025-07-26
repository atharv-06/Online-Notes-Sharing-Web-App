<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("⚠️ Please <a href='login.php'>login</a> first.");
}

include 'connection.php'; // Ensure this file has your DB connection

$message = '';
$messageClass = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $userId = $_SESSION['user_id'];

    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['pdf']['tmp_name'];
        $fileName = $_FILES['pdf']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = uniqid('note_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $stmt = $conn->prepare("INSERT INTO notes (user_id, title, branch, file_type, filename) VALUES (?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("issss", $userId, $title, $subject, $fileExtension, $newFileName);
                    $stmt->execute();
                    $message = '✅ Note uploaded successfully! Redirecting...';
                    $messageClass = 'bg-green-100 text-green-700';
                    $redirect = true;

                } else {
                    $message = '❌ Database error: ' . $conn->error;
                    $messageClass = 'bg-red-100 text-red-700';
                }
            } else {
                $message = '❌ Failed to save uploaded file.';
                $messageClass = 'bg-red-100 text-red-700';
            }
        } else {
            $message = '❌ Only PDF files are allowed.';
            $messageClass = 'bg-red-100 text-red-700';
        }
    } else {
        $message = '❌ No file uploaded.';
        $messageClass = 'bg-red-100 text-red-700';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload Notes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-gray-100 to-gray-200 min-h-screen flex items-center justify-center px-4 py-10">

  <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-2xl font-semibold text-center mb-4 text-gray-800">📤 Upload Your Note</h2>

    <?php if (!empty($message)): ?>
      <div class="<?= $messageClass ?> px-4 py-3 rounded mb-4 text-sm" id="uploadMessage">
        <?= htmlspecialchars($message) ?>
      </div>
      <script>
        document.getElementById('uploadMessage')?.scrollIntoView({ behavior: 'smooth' });
      </script>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" name="title" required placeholder="e.g. Engineering Math Notes" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Branch / Subject</label>
        <input type="text" name="subject" required placeholder="e.g. Computer Science" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Upload PDF</label>
        <input type="file" name="pdf" accept=".pdf" required class="w-full text-sm file:border file:border-gray-300 file:rounded file:px-3 file:py-2 file:bg-blue-50 file:text-blue-600 file:cursor-pointer hover:file:bg-blue-100" />
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition-shadow shadow-md focus:outline-none focus:ring-2 focus:ring-blue-300">
        Upload Note
      </button>
    </form>
  </div>
  <?php if (!empty($redirect)): ?>
<script>
  setTimeout(() => {
    window.location.href = "notes.php"; // Change if needed
  }, 2000); // Redirect after 2 seconds
</script>
<?php endif; ?>

</body>
</html>
