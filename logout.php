<?php
session_start();
session_destroy();
header("Location: logincl.php");
exit();
