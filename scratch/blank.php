<?php $im = imagecreate(1, 1); $bg = imagecolorallocate($im, 255, 255, 255); imagejpeg($im, 'temp_docx/word/media/image1.jpg'); imagedestroy($im);
