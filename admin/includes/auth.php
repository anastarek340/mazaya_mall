<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if(!isset($_SESSION['admin_id'])){
    header('Location: login.php');
    exit;
}
