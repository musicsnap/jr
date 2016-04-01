<?php

class Captcha {

	private static function words($len) {
		$charecters = "123456789abcdefghijklmnpqrstuvwxyz";
		$words = "";
		$max = strlen($charecters) - 1;
		for ($i = 0; $i < $len; $i++)
		{
			$words .= $charecters[rand(0, $max)];
		}

		return $words;
	}

	/**
	 * 生成验证码字符串，写入SESSION，将字符串图片返回给浏览器
	 *
	 * @param     $len
	 * @param int $width
	 * @param int $height
	 * @param int $font_size
	 */
	public static function generate($len, $width = 108, $height = 30, $font_size = 18) {
		$sizes = array('18' => array('width' => 25, 'height' => 25));
		$words = self::words($len);
		session_start();
		$session_key = 'captcha';

		$_SESSION[$session_key] = strtolower($words);

		$image = ImageManager::createWhiteImage($width, $height);

		$font_config = array('spacing' => -17, 'font' => 't1.ttf');
		$font_path = dirname(__FILE__) . '/font/' . $font_config['font'];

		$color = imagecolorallocate($image, mt_rand(0, 100), mt_rand(20, 120), mt_rand(50, 150));
		$rand = 0;
		$w = $sizes[$font_size]['width'] * $len;
		$h = $sizes[$font_size]['height'];
		$x = round(($width - $w) / 2);
		$y = round(($height + $h) / 2) - 6;

		$coors = imagettftext($image, $font_size, $rand, $x, $y, $color, $font_path, $words);
		if ($coors)
		{
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Pragma: no-cache");
			header("Cache-control: private");
			header('Content-Type: image/png');
            ob_clean();
			imagepng($image);
			imagedestroy($image);
		}
		exit;
	}

	public static function simple($len, $width = 48, $height = 22) {
		$words = self::words($len);
		session_start();
		$session_key = 'captcha';
		$_SESSION[$session_key] = strtolower($words);

		$width = ($len * 10 + 10) > $width ? $len * 10 + 10 : $width;
		$canvas = imagecreatetruecolor($width, $height);
		$r = Array(225, 255, 255, 223);
		$g = Array(225, 236, 237, 255);
		$b = Array(225, 236, 166, 125);
		$key = mt_rand(0, 3);

		$back = imagecolorallocate($canvas, $r[$key], $g[$key], $b[$key]);
		$border = imagecolorallocate($canvas, 100, 100, 100);

		imagefilledrectangle($canvas, 0, 0, $width - 1, $height - 1, $back);
		imagerectangle($canvas, 0, 0, $width - 1, $height - 1, $border);

		$string = imagecolorallocate($canvas, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));

		for ($i = 0; $i < 10; $i++)
			imagearc($canvas, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 200), mt_rand(20, 200), 55, 44, $string);
		for ($i = 0; $i < 25; $i++)
			imagesetpixel($canvas, mt_rand(0, $width), mt_rand(0, $height), $string);
		for ($i = 0; $i < $len; $i++)
			imagestring($canvas, 5, $i * 10 + 5, mt_rand(1, 8), $words{$i}, $string);
		if ($canvas)
		{
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Pragma: no-cache");
			header("Cache-control: private");
			header('Content-Type: image/png');
			imagepng($canvas);
			imagedestroy($canvas);
		}
		exit;
	}



    public function googleCaptcha($len, $width = 80, $height = 25, $font_size = 18) {
        $sizes = array('18' => array('width' => 25, 'height' => 25));
        $words = self::words($len);

        session_start();
        $session_key = 'captcha';

        $_SESSION[$session_key] = strtolower($words);


        $im = imagecreatetruecolor($width,$height);
        $text_c = ImageColorAllocate($im, mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
        $tmpC0=mt_rand(100,255);
        $tmpC1=mt_rand(100,255);
        $tmpC2=mt_rand(100,255);
        $buttum_c = ImageColorAllocate($im,$tmpC0,$tmpC1,$tmpC2);
        imagefill($im, 16, 13, $buttum_c);

        $font = 't1.ttf';
        $font_path = dirname(__FILE__) . '/font/' . $font;

        for ($i=0;$i<strlen($words);$i++)
        {
            $tmp =substr($words,$i,1);
            $array = array(-1,1);
            $p = array_rand($array);
            $an = $array[$p]*mt_rand(1,10);//角度
            $size = 14;
            imagettftext($im, $size, $an, 15+$i*$size, 20, $text_c, $font_path, $tmp);
        }


        $distortion_im = imagecreatetruecolor ($width, $height);

        imagefill($distortion_im, 16, 13, $buttum_c);
        for ( $i=0; $i<$width; $i++) {
            for ( $j=0; $j<$height; $j++) {
                $rgb = imagecolorat($im, $i , $j);
                if( (int)($i+20+sin($j/$height*2*M_PI)*10) <= imagesx($distortion_im)&& (int)($i+20+sin($j/$height*2*M_PI)*10) >=0 ) {
                    imagesetpixel ($distortion_im, (int)($i+10+sin($j/$height*2*M_PI-M_PI*0.1)*4) , $j , $rgb);
                }
            }
        }
        //加入干扰象素;
        $count = 160;//干扰像素的数量
        for($i=0; $i<$count; $i++){
            $randcolor = ImageColorallocate($distortion_im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($distortion_im, mt_rand()%$width , mt_rand()%$height , $randcolor);
        }

        $rand = mt_rand(5,30);
        $rand1 = mt_rand(15,25);
        $rand2 = mt_rand(5,10);
        for ($yy=$rand; $yy<=+$rand+2; $yy++){
            for ($px=-80;$px<=80;$px=$px+0.1)
            {
                $x=$px/$rand1;
                if ($x!=0)
                {
                    $y=sin($x);
                }
                $py=$y*$rand2;

                imagesetpixel($distortion_im, $px+80, $py+$yy, $text_c);
            }
        }
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
        header("Cache-control: private");
        header('Content-Type: image/png');
        ob_clean();
        imagepng($distortion_im);
        imagedestroy($distortion_im);
        exit;

    }
	/**
	 * 验证是否是合法的验证码
	 *
	 * @param     $captcha
	 * @param int $size
	 *
	 * @return int
	 */
	public static function isCaptcha($captcha, $size = 4) {
		return (bool)preg_match('/^[123456789abcdefghijklmnpqrstuvwxyz]{' . $size . '}$/ui', $captcha);
	}
}
