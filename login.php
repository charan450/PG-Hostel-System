<?php
require 'db.php';

$data     = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($username) || empty($password)) {
  echo json_encode([
    'success' => false,
    'message' => 'Please enter username and password'
  ]);
  exit;
}

$stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
  echo json_encode([
    'success'  => true,
    'message'  => 'Login successful',
    'username' => $user['username']
  ]);
} else {
  echo json_encode([
    'success' => false,
    'message' => 'Wrong username or password'
  ]);
}

$stmt->close();
$conn->close();
?>