<?php
  session_start();
  $isLoggedIn = isset($_SESSION['user']);
  $username = $isLoggedIn ? htmlspecialchars($_SESSION['user']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ShareNotes</title>
  <meta name="description" content="Upload, explore, and manage study notes." />
  <link rel="icon" href="/favicon.ico" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-gray-50 to-gray-200 min-h-screen flex flex-col font-sans">

  <!-- HEADER -->
  <header class="flex flex-col sm:flex-row sm:justify-between sm:items-center px-4 sm:px-6 py-4 border-b border-gray-300 bg-white shadow-sm sticky top-0 z-50">
    <!-- Clickable Logo -->
    <a href="index.php" class="flex items-center space-x-3 mb-3 sm:mb-0 hover:opacity-90 transition">
      <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center border border-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 48 48" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-orange-600" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M32 14l-6 6-1.5-1.5M16 34h12a4 4 0 004-4v-3a4 4 0 00-4-4H20m-4 11v-7m0 0c0-1.104.448-2.104 1.172-2.828M12 20h4a4 4 0 004-4v-3a4 4 0 00-4-4h-2" />
        </svg>
      </div>
      <h1 class="text-xl font-semibold select-none">
        Share<span class="text-red-600">Notes</span>
      </h1>
    </a>

    <!-- Navigation -->
    <nav class="flex flex-wrap justify-center sm:justify-end items-center gap-3 text-gray-700 font-medium text-sm">
      <a href="explore.php" class="hover:text-gray-900 transition">Explore</a>
      <a href="notes.php" class="hover:text-gray-900 transition">My Notes</a>
      <a href="upload.php" class="hover:text-gray-900 transition">Upload</a>

      <?php if ($isLoggedIn): ?>
        <span class="text-green-600 font-semibold block sm:inline">👋 <?php echo $username; ?></span>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full shadow transition text-sm">Logout</a>
      <?php else: ?>
        <a href="login.php" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-full shadow transition text-sm">Login</a>
        <a href="register.php" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-full shadow transition text-sm">Register</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- MAIN CONTENT -->
  <main class="flex-grow flex flex-col justify-center items-center text-center px-4 py-12 sm:px-6 sm:py-16 max-w-3xl mx-auto">
    <h2 class="text-2xl sm:text-4xl font-extrabold mb-4 select-none flex items-center gap-2">
      <span aria-hidden="true">📚</span> Welcome to ShareNotes
    </h2>

    <p class="text-gray-600 max-w-md sm:max-w-lg mb-8 leading-relaxed text-base sm:text-lg">
      Share and explore study notes with students across all branches and semesters.
      Upload your PDFs, browse public notes, or access your uploaded materials anytime.
    </p>

    <div class="flex flex-col sm:flex-row flex-wrap justify-center items-center gap-4 w-full px-2">
      <a href="upload.php" class="bg-black text-white w-full sm:w-auto text-center px-5 py-3 rounded-md flex items-center justify-center gap-2 font-semibold hover:bg-gray-900 transition shadow">
        <span>📤</span> Upload Notes
      </a>
      <a href="notes.php" class="bg-yellow-100 text-yellow-800 w-full sm:w-auto text-center px-5 py-3 rounded-md flex items-center justify-center gap-2 font-semibold hover:bg-yellow-200 transition shadow">
        <span>📁</span> My Notes
      </a>
      <a href="explore.php" class="bg-blue-100 text-blue-800 w-full sm:w-auto text-center px-5 py-3 rounded-md flex items-center justify-center gap-2 font-semibold hover:bg-blue-200 transition shadow">
        <span>🌐</span> Explore Notes
      </a>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="text-center text-gray-500 text-xs sm:text-sm py-4 border-t border-gray-300 font-light select-none">
    &copy; <?php echo date("Y"); ?> ShareNotes. All rights reserved.
  </footer>

</body>
</html>
