<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);
$rent = (int)($data['rent'] ?? 0);

if ($id < 101 || $id > 305) {
  echo json_encode(['success' => false, 'message' => 'Invalid room ID']);
  exit;
}

// Overall room status — occupied అయిన members ఉంటే occupied, లేకపోతే vacant
$members = $data['members'] ?? [];

$m1_name    = trim($members[1]['name']    ?? '');
$m1_phone   = trim($members[1]['phone']   ?? '');
$m1_movein  = $members[1]['movein']       ?: null;
$m1_payment = $members[1]['payment']      ?: null;
$m1_status  = trim($members[1]['status']  ?? 'vacant');

$m2_name    = trim($members[2]['name']    ?? '');
$m2_phone   = trim($members[2]['phone']   ?? '');
$m2_movein  = $members[2]['movein']       ?: null;
$m2_payment = $members[2]['payment']      ?: null;
$m2_status  = trim($members[2]['status']  ?? 'vacant');

$m3_name    = trim($members[3]['name']    ?? '');
$m3_phone   = trim($members[3]['phone']   ?? '');
$m3_movein  = $members[3]['movein']       ?: null;
$m3_payment = $members[3]['payment']      ?: null;
$m3_status  = trim($members[3]['status']  ?? 'vacant');

$m4_name    = trim($members[4]['name']    ?? '');
$m4_phone   = trim($members[4]['phone']   ?? '');
$m4_movein  = $members[4]['movein']       ?: null;
$m4_payment = $members[4]['payment']      ?: null;
$m4_status  = trim($members[4]['status']  ?? 'vacant');

// Room overall status calculate చేయండి
$allStatuses = [$m1_status, $m2_status, $m3_status, $m4_status];
$hasOccupied = in_array('occupied', $allStatuses);
$hasUnpaid   = in_array('unpaid',   $allStatuses);
$hasOverdue  = in_array('overdue',  $allStatuses);

if ($hasOverdue)       $roomStatus = 'overdue';
elseif ($hasUnpaid)    $roomStatus = 'unpaid';
elseif ($hasOccupied)  $roomStatus = 'occupied';
else                   $roomStatus = 'vacant';

$stmt = $conn->prepare('
  UPDATE rooms SET
    rent=?,
    status=?,
    member1_name=?, member1_phone=?, member1_movein=?, member1_payment=?, member1_status=?,
    member2_name=?, member2_phone=?, member2_movein=?, member2_payment=?, member2_status=?,
    member3_name=?, member3_phone=?, member3_movein=?, member3_payment=?, member3_status=?,
    member4_name=?, member4_phone=?, member4_movein=?, member4_payment=?, member4_status=?
  WHERE id=?
');

$stmt->bind_param(
  'isssssssssssssssssssssi',
  $rent, $roomStatus,
  $m1_name, $m1_phone, $m1_movein, $m1_payment, $m1_status,
  $m2_name, $m2_phone, $m2_movein, $m2_payment, $m2_status,
  $m3_name, $m3_phone, $m3_movein, $m3_payment, $m3_status,
  $m4_name, $m4_phone, $m4_movein, $m4_payment, $m4_status,
  $id
);

$stmt->execute();
echo json_encode(['success' => true, 'message' => 'Room saved successfully']);
$stmt->close();
$conn->close();
?>