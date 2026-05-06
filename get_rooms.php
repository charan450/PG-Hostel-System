<?php
require 'db.php';

$result = $conn->query('SELECT * FROM rooms ORDER BY id ASC');

$rooms = [];
while ($row = $result->fetch_assoc()) {
  $rooms[$row['id']] = [
    'id'     => (int)$row['id'],
    'rent'   => (int)$row['rent'],
    'status' => $row['status'],
    'members' => [
      1 => [
        'name'    => $row['member1_name']    ?? '',
        'phone'   => $row['member1_phone']   ?? '',
        'movein'  => $row['member1_movein']  ?? '',
        'payment' => $row['member1_payment'] ?? '',
        'status'  => $row['member1_status']  ?? 'vacant',
      ],
      2 => [
        'name'    => $row['member2_name']    ?? '',
        'phone'   => $row['member2_phone']   ?? '',
        'movein'  => $row['member2_movein']  ?? '',
        'payment' => $row['member2_payment'] ?? '',
        'status'  => $row['member2_status']  ?? 'vacant',
      ],
      3 => [
        'name'    => $row['member3_name']    ?? '',
        'phone'   => $row['member3_phone']   ?? '',
        'movein'  => $row['member3_movein']  ?? '',
        'payment' => $row['member3_payment'] ?? '',
        'status'  => $row['member3_status']  ?? 'vacant',
      ],
      4 => [
        'name'    => $row['member4_name']    ?? '',
        'phone'   => $row['member4_phone']   ?? '',
        'movein'  => $row['member4_movein']  ?? '',
        'payment' => $row['member4_payment'] ?? '',
        'status'  => $row['member4_status']  ?? 'vacant',
      ],
    ],
  ];
}

echo json_encode(['success' => true, 'rooms' => $rooms]);
$conn->close();
?>