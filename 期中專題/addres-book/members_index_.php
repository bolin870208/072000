<?php

session_start();
if (isset($_SESSION["admin"])) {
  include __DIR__ . '/members_list-admin.php';
} else {
  include __DIR__ . '/members_list-no-admin.php';
}
