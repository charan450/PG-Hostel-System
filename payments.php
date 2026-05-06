<?php
require 'db.php';

$action = $_GET['action'] ?? 'summary';

if ($action === 'summary') {

  $result   = $conn->query('SELECT * FROM rooms WHERE status != "vacant"');
  $expected = 0; $collected = 0; $pending = 0; $overdue = 0;

  while ($row = $result->fetch_assoc()) {
    $rent = (int)$row['rent'];
    $expected += $rent;
    if ($row['status'] === 'occupied') $collected += $rent;
    if ($row['status'] === 'unpaid')   $pending   += $rent;
    if ($row['status'] === 'overdue')  $overdue   += $rent;
  }

  echo json_encode([
    'success'   => true,
    'expected'  => $expected,
    'collected' => $collected,
    'pending'   => $pending,
    'overdue'   => $overdue,
  ]);

} elseif ($action === 'mark') {

  $data      = json_decode(file_get_contents('php://input'), true);
  $room_id   = (int)($data['room_id'] ?? 0);
  $newStatus = trim($data['status']   ?? 'unpaid');

  $allowed = ['occupied', 'unpaid', 'overdue'];
  if (!in_array($newStatus, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
  }

  $stmt = $conn->prepare('UPDATE rooms SET status=? WHERE id=?');
  $stmt->bind_param('si', $newStatus, $room_id);
  $stmt->execute();

  echo json_encode(['success' => true, 'message' => 'Payment status updated']);
  $stmt->close();
}

$conn->close();
?>