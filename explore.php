<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "sharenotes");

if ($mysqli->connect_errno) {
    die("Failed to connect to database: " . $mysqli->connect_error);
}

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM notes WHERE is_public = 1";

if (!empty($search)) {
    $search = $mysqli->real_escape_string($search);
    $query .= " AND (title LIKE '%$search%' OR branch LIKE '%$search%' OR uploaded_by LIKE '%$search%')";
}

$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Explore Notes - ShareNotes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- HEADER -->
  <header class="flex justify-between items-center px-6 py-4 border-b border-gray-300 bg-white">
    <div class="flex items-center space-x-3">
      <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center border border-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 48 48" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-orange-600">
          <path stroke-linecap="round" stroke-linejoin="round" d="M32 14l-6 6-1.5-1.5M16 34h12a4 4 0 004-4v-3a4 4 0 00-4-4H20m-4 11v-7m0 0c0-1.104.448-2.104 1.172-2.828M12 20h4a4 4 0 004-4v-3a4 4 0 00-4-4h-2" />
        </svg>
      </div>
      <h1 class="text-xl font-semibold select-none">
        Share<span class="text-red-600">Notes</span>
      </h1>
    </div>

    <nav class="flex items-center space-x-6 text-gray-700 font-medium">
      <a href="index.php" class="hover:text-gray-900 transition">Home</a>
      <a href="upload.php" class="hover:text-gray-900 transition">Upload</a>
      <a href="mynotes.php" class="hover:text-gray-900 transition">My Notes</a>
      <a href="explore.php" class="text-blue-600 font-bold">Explore</a>
      <form method="post" action="logout.php">
        <button type="submit" class="ml-4 bg-emerald-400 hover:bg-emerald-500 text-white font-semibold px-5 py-2 rounded-full transition-shadow shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-300">
          Logout
        </button>
      </form>
    </nav>
  </header>

  <!-- MAIN CONTENT -->
  <main class="flex-grow px-6 py-10 max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">🌐 Explore Public Notes</h2>

    <!-- Search Form -->
    <form method="get" class="mb-6">
      <input
        type="text"
        name="search"
        value="<?php echo htmlspecialchars($search); ?>"
        placeholder="Search by subject, branch, or keywords..."
        class="w-full sm:w-1/2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
      />
    </form>

    <!-- Notes Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($note = $result->fetch_assoc()): ?>
          <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
            <div class="flex justify-between items-center mb-2">
              <h3 class="text-lg font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($note['title']); ?></h3>
              <span class="text-sm text-gray-500"><?php echo strtoupper(htmlspecialchars($note['file_type'])); ?></span>
            </div>
            <p class="text-sm text-gray-600 mb-2">Branch: <?php echo htmlspecialchars($note['branch']); ?></p>
            <p class="text-xs text-gray-400 mb-3">Uploaded by: <?php echo htmlspecialchars($note['uploaded_by']); ?></p>

            <?php if (!empty($note['filename'])): ?>
              <a
                href="uploads/<?php echo urlencode($note['filename']); ?>"
                download
                class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full text-sm transition"
              >Download</a>
            <?php else: ?>
              <p class="text-red-500 text-sm">No file available</p>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-gray-600 text-sm col-span-full">No notes found.</p>
      <?php endif; ?>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="text-center text-gray-500 text-sm py-4 border-t border-gray-300 font-light select-none">
    &copy; <?php echo date("Y"); ?> ShareNotes. All rights reserved.
  </footer>

</body>
</html>
