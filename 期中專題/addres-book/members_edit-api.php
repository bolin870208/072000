<?php
require __DIR__ . '/parts/admin-required.php';
require __DIR__ . '/db-connect.php';
header('Content-Type: application/json');

$output = [
  'success' => false,
  'bodyData' => $_POST, // 除錯用
  'code' => 0,
];

// 檢查必填欄位
$ab_id = isset($_POST['ab_id']) ? intval($_POST['ab_id']) : 0;
if (empty($ab_id)) {
  $output['code'] = 400;
  echo json_encode($output);
  exit;
}

$name = $_POST['name'] ?? '';
if (mb_strlen($name) < 2) {
  $output['code'] = 405;
  echo json_encode($output);
  exit;
}

$birthday = $_POST['birthday'];
$ts = strtotime($birthday);
if ($ts === false) {
  $birthday = null;
} else {
  $birthday = date('Y-m-d', $ts);
}

// 允許上傳的圖片類型
$allowed_types = array('image/jpeg', 'image/png', 'image/gif');
$upload_dir = '../uploads/';

// 檢查是否有上傳圖片
if (!empty($_FILES['photo']['name'])) {
  if (in_array($_FILES['photo']['type'], $allowed_types)) {
    $upload_file = $upload_dir . basename($_FILES['photo']['name']);
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file)) {
      $_POST['photo'] = basename($_FILES['photo']['name']);
    } else {
      $output['code'] = 500; // 檔案上傳失敗
      echo json_encode($output);
      exit;
    }
  } else {
    $output['code'] = 415; // 不支持的媒體類型
    echo json_encode($output);
    exit;
  }
} else {
  $_POST['photo'] = ''; // 如果沒有上傳照片，設置為空字符串
}

// 更新資料庫
$sql = "UPDATE `address_book` SET `name`=?, `email`=?, `membership_level`=?, `gender`=?, `mobile`=?, `birthday`=?, `preferred_products`=?, `status`=?, `address`=?, `photo`=? WHERE `ab_id`=?";
$stmt = $pdo->prepare($sql);

$stmt->execute([
  $name,
  $_POST['email'],
  $_POST['membership_level'],
  $_POST['gender'],
  $_POST['mobile'],
  $birthday,
  $_POST['preferred_products'],
  $_POST['status'],
  $_POST['address'],
  $_POST['photo'],
  $ab_id
]);

$output['success'] = !!$stmt->rowCount(); // 將rowCount()的值轉換成布林值
echo json_encode($output);
