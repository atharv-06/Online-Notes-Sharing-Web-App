<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'connection.php';

if (isset($_GET['id'])) {
  $noteId = intval($_GET['id']);
  $userId = $_SESSION['user_id'];

  // Fetch the note to get the filename
  $stmt = $conn->prepare("SELECT filename FROM notes WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $noteId, $userId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $note = $result->fetch_assoc();
    $filename = $note['filename'];
    $filePath = __DIR__ . '/uploads/' . $filename;

    // Delete file if it exists
    if (file_exists($filePath)) {
      unlink($filePath);
    }

    // Delete the note record from database
    $deleteStmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $noteId, $userId);
    $deleteStmt->execute();
    $deleteStmt->close();

    $_SESSION['success'] = 'Note deleted successfully.';
  } else {
    $_SESSION['error'] = 'Note not found or you do not have permission to delete it.';
  }

  $stmt->close();
}

$conn->close();
header("Location: notes.php");
exit;
?>
