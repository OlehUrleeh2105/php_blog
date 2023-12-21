<?php
  session_start();
  $_SESSION["user_id"] = -1;
  $_SESSION["role"]=-1;
  header("Location: index.php");
  exit();
