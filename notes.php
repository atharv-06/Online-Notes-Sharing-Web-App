<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'connection.php';

$userId = $_SESSION['user_id'];

$sql = "SELECT id, title, branch, file_type, filename FROM notes WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
  die("SQL prepare error: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Notes - ShareNotes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function confirmDelete(id) {
      if (confirm('Are you sure you want to delete this note?')) {
        window.location.href = 'delete_note.php?id=' + id;
      }
    }
  </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <header class="bg-white shadow p-4 flex items-center justify-between">
    <h1 class="text-xl font-bold text-emerald-600">📘 ShareNotes</h1>
    <nav class="space-x-4 text-sm">
      <a href="index.php" class="text-gray-700 hover:text-emerald-600">Home</a>
      <a href="upload.php" class="text-gray-700 hover:text-emerald-600">Upload</a>
      <a href="logout.php" class="text-red-500 hover:underline">Logout</a>
    </nav>
  </header>

  <main class="flex-grow px-4 py-8 max-w-5xl mx-auto w-full">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800 text-center sm:text-left">📂 My Uploaded Notes</h2>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded">
        <?= htmlspecialchars($_SESSION['success']) ?>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php elseif (isset($_SESSION['error'])): ?>
      <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">
        <?= htmlspecialchars($_SESSION['error']) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
      <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition duration-300 relative">
            <h3 class="text-lg font-bold text-gray-800 truncate"><?= htmlspecialchars($row['title']) ?></h3>
            <p class="text-sm text-gray-500 mb-2">Branch: <?= htmlspecialchars($row['branch']) ?></p>
            
            <a href="uploads/<?= htmlspecialchars($row['filename']) ?>" target="_blank"
               class="inline-block mt-2 px-4 py-1 text-sm bg-emerald-500 text-white rounded hover:bg-emerald-600 transition">
              View PDF
            </a>

            <a href="uploads/<?= htmlspecialchars($row['filename']) ?>" download="<?= htmlspecialchars($row['title']) ?>.pdf"
               class="inline-block mt-2 ml-2 px-4 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition">
              Download
            </a>

            <button onclick="confirmDelete(<?= $row['id'] ?>)"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm"
                    title="Delete Note">
              ✖
            </button>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-500 text-sm mt-10">You haven’t uploaded any notes yet.</p>
    <?php endif; ?>
  </main>

  <footer class="bg-white text-center py-4 text-sm text-gray-500 border-t mt-8">
    &copy; <?= date('Y') ?> ShareNotes. All rights reserved.
  </footer>

</body>
</html>