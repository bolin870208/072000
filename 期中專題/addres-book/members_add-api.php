<?php
require __DIR__ . '/parts/admin-required.php';
require __DIR__ . '/db-connect.php';
header('Content-Type: application/json');

$output = [
  'success' => false,
  'bodayData' => $_POST, #除錯用
];

//TODO: 表單欄位檢查

$birthday = $_POST['birthday'];
$ts = strtotime($birthday); #轉換成timestamp
if ($ts === false) {
  $birthday = null; #如果不是日期結果空值
} else {
  $birthday = date('Y-m-d', $ts);
}

$password_hash = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;


// 允許上傳的圖片類型
$allowed_types = array('image/jpeg', 'image/png', 'image/gif');

// 上傳資料夾
$upload_dir = '../uploads/';

if (!empty($_FILES['photo']['name'])) {
  // 檢查檔案類型是否允許
  if (in_array($_FILES['photo']['type'], $allowed_types)) {
    $upload_file = $upload_dir . basename($_FILES['photo']['name']);

    // 嘗試將檔案從臨時位置移動到上傳資料夾
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file)) {
      $_POST['photo'] = basename($_FILES['photo']['name']); // 將檔案名稱存入 $_POST['photo']
    }
  }
}




//錯誤作法

// $sql = "INSERT INTO `address_book`( `name`, `email`, `mobile`, `birthday`, `address`, `created_at`) VALUES (
// '{$_POST['name']}','{$_POST['email']}','{$_POST['mobile']}','{$_POST['birthday']}','{$_POST['address']}',NOW()
// )";
// $stmt=$pdo->query($sql);


// $membership_level = isset($_POST['membership_level']) ? $_POST['membership_level'] : null;

// Check if preferred_products is set in $_POST, otherwise set a default value or handle accordingly
// $preferred_products = isset($_POST['preferred_products']) ? $_POST['preferred_products'] : null;

// Check if status is set in $_POST, otherwise set a default value or handle accordingly
// $status = isset($_POST['status']) ? $_POST['status'] : null;

// Check if photo is set in $_POST, otherwise set a default value or handle accordingly
$photo = isset($_POST['photo']) ? $_POST['photo'] : null;



// Proceed with SQL insert query using these values
$sql = "INSERT INTO `address_book` 
        (`name`, `email`, `password_hash`, `membership_level`, `gender`, `mobile`, `address`, `birthday`, `preferred_products`, `created_at`, `status`, `photo`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  $_POST['name'],
  $_POST['email'],
  $password_hash,
  $_POST['membership_level'],
  $_POST['gender'],
  $_POST['mobile'],
  $_POST['address'],
  $birthday,
  $_POST['preferred_products'],
  $_POST['status'],
  $photo,
]);


$output['success'] = !!$stmt->rowCount();
echo json_encode($output);
