<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
session_destroy();
header('Location: login.php');
exit();
