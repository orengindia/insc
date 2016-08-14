<?php
/* CAPTCHA */
session_start();
 $ranStr = md5(microtime());
 $ranStr = substr($ranStr, 0, 6);
 $_SESSION['cap_code'] = $ranStr;
 $newImage = imagecreatefromjpeg("cap_bg.jpg");
 $txtColor = imagecolorallocate($newImage, 0, 0, 0);
imagestring($newImage, 10, 15, 10, $ranStr, $txtColor);
header("Content-type: image/jpeg");
imagejpeg($newImage);
?>