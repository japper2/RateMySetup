<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: /github/RateMySetup/login");
exit;
