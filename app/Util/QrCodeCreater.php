<?php
namespace App\Util;

use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

/**
 * Author: CHQ
 * Time: 2016/6/29 13:40
 * Usage: 二维码生成类
 * Update: 2016/7/4 14:00
 */
class QrCodeCreater
{
	/**
	 * @var array 默认配置项
	 * format：返回的文件格式，默认为一个 SVG格式的图片字符串
	 * encoding：创建二维码时可以使用的编码，默认为UTF-8
	 * size：二维码的像素尺寸
	 * color：二维码颜色，注意必须是RBG格式
	 * bgcolor：二维码背景色，注意必须是RBG格式
	 * margin：二维码边距
	 * tolerancelevel：二维码容错级别
	 * text：二维码文字内容
	 * savepath：二维码保存路径，默认为'/uploads/qrcode'，请不要更改！
	 * withlogo：是否带有logo，默认为false
	 * logo：logo图片地址，logo占二维码图像比例，是否使用绝对路径。注意：logo只支持PNG格式的图片！
	 */
	protected static $config = [
		'format' => 'svg',
		'encoding' => 'UTF-8',
		'size' => 200,
		'color' => ['r' => 0, 'g' => 0, 'b' => 0],
		'bgcolor' => ['r' => 255, 'g' => 255, 'b' => 255],
		'margin' => 0,
		'tolerancelevel' => 'M',
		'text' => 'some content',
		'issave' => false,
		'savepath' => '/uploads/qrcode',
		'withlogo' => false,
		'logo' => [
			'filename' => '',
			'percentage' => 0.0,
			'absolute' => true
		]
	];

	/**
	 * @var array 可以接受的参数值，请注意大小写！
	 *
	 */
	protected static $legalPrm = [
		'format' => ['svg', 'png', 'eps'],
		'encoding' => ['UTF-8', 'GBK', 'ASCII', 'UTF-16BE', 'EUC-KR',
			'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7',
			'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-11', 'ISO-8859-12', 'ISO-8859-13', 'ISO-8859-14',
			'ISO-8859-15', 'ISO-8859-16', 'SHIFT-JIS', 'WINDOWS-1250', 'WINDOWS-1251', 'WINDOWS-1252', 'WINDOWS-1256'
		],
		'tolerancelevel' => ['H', 'Q', 'M', 'L'],
		'issave' => [true, false],
		'withlogo' => [true, false],
	];


	/**
	 * 生成二维码
	 * 注意：根据输入的配置参数 $myconfig['issave'] 的值决定返回值（图片路径或者图片内容字符串）
	 * @param array $myconfig
	 * @return array
	 */
	public static function getQrCode($myconfig = array())
	{
		$config = self::checkConfig($myconfig);
		$generator = new BaconQrCodeGenerator();
		$tempObj = $generator->format($config['format'])
			->size($config['size'])
			->color($config['color']['r'], $config['color']['g'], $config['color']['b'])
			->backgroundColor($config['bgcolor']['r'], $config['bgcolor']['g'], $config['bgcolor']['b'])
			->margin($config['margin'])
			->errorCorrection($config['tolerancelevel'])
			->encoding($config['encoding']);

		if ($config['withlogo']) {
			// 注意：要带有logo，生成二维码时只能设置为png格式！
			if($config['format'] === 'png'){
				$tempObj->merge($config['logo']['filename'], $config['logo']['percentage'], $config['logo']['absolute']);
			}else{
				return ['success' => false, 'msg' => '要使用logo，生成二维码时只能设置为png格式！'];
			}
		}
		if ($config['issave']) {
			// 返回二维码图片路径
			$imgPath = storage_path() . $config['savepath'];
			// 检查存储目录是否存在
			$isAvailablePath = self::checkSavePath($imgPath);
			if (!$isAvailablePath['success']) {
				return ['success' => false, 'msg' => $isAvailablePath['msg']];
			}
			// 图片的文件名
			$imgName = md5(time()) . '.' . $config['format'];
			$tempPrm = $imgPath . DIRECTORY_SEPARATOR . $imgName;
			$tempObj->generate($config['text'], $tempPrm);
			$returnPath = $config['savepath'] . '/' . $imgName;
			return file_exists($tempPrm) ? ['success' => true, 'data' => $returnPath] : ['success' => false, 'msg' => '二维码文件保存失败！'];
		} else {
			// 返回二维码字符串
			$str = $tempObj->generate($config['text']);
			return ['success' => true, 'data' => $str];
		}
	}


	/**
	 * 检查二维码保存路径，若不存在就创建
	 * @param string $dir
	 * @return array
	 */
	protected static function checkSavePath($dir)
	{
		if (!is_string($dir) || empty($dir)) {
			return ['success' => false, 'msg' => '非法参数！'];
		}
		$exist = file_exists($dir);
		if (!$exist) {
			$createDir = mkdir($dir, 0777);
			if (!$createDir) {
				return ['success' => false, 'msg' => '创建文件夹' . $dir . '失败！'];
			}
		}
		return ['success' => true, 'path' => $dir];
	}

	/**
	 * 检查二维码配置项参数，对参数值进行过滤
	 * @param $prm
	 * @return array
	 */
	protected static function checkConfig($prm)
	{
		if (!is_array($prm) || empty($prm)) {
			return self::$config;
		}
/*
		$available = array_filter($prm, function ($value, $key) {
			if ($key === 'format') {
				return in_array($value, self::$legalPrm['format'], true);
			} elseif ($key === 'encoding') {
				return in_array($value, self::$legalPrm['encoding'], true);
			} elseif ($key === 'tolerancelevel') {
				return in_array($value, self::$legalPrm['tolerancelevel'], true);
			} elseif ($key === 'issave') {
				return in_array($value, self::$legalPrm['issave'], true);
			} elseif (($key === 'color') || ($key == 'bgcolor')) {
				return isset($value['r'], $value['g'], $value['b']) && is_int($value['r']) && is_int($value['g'] && is_int($value['b']));
			} elseif (($key === 'size') || ($key == 'margin')) {
				return is_int($value);
			} elseif ($key === 'text') {
				return is_string($value) && !empty($value);
			} elseif ($key === 'withlogo') {
				return in_array($value, self::$legalPrm['withlogo'], true);
			} elseif ($key === 'logo') {
				return isset($value['filename'], $value['percentage'], $value['absolute']) && is_string($value['filename']) && is_float($value['percentage']) && is_bool($value['absolute']);
			} else {
				return false;
			}
		}, ARRAY_FILTER_USE_BOTH);
*/
		$available = [];
		foreach($prm as $key => $value){
			if (($key === 'format') && in_array($value, self::$legalPrm['format'], true)){
				$available[$key] = $value;
			}
			if (($key === 'encoding') && in_array($value, self::$legalPrm['encoding'], true)){
				$available[$key] = $value;
			}
			if (($key === 'tolerancelevel') && (in_array($value, self::$legalPrm['tolerancelevel'], true))){
				$available[$key] = $value;
			}
			if(($key === 'issave') && (in_array($value, self::$legalPrm['issave'], true))){
				$available[$key] = $value;
			}
			if(($key === 'color') || ($key == 'bgcolor')){
				if(isset($value['r'], $value['g'], $value['b']) && is_int($value['r']) && is_int($value['g'] && is_int($value['b']))){
					$available[$key] = $value;
				}
			}
			if(($key === 'size') || ($key == 'margin')){
				if(is_int($value)){
					$available[$key] = $value;
				}
			}
			if($key === 'text'){
				if(is_string($value) && !empty($value)){
					$available[$key] = $value;
				}
			}
			if($key === 'withlogo'){
				if(in_array($value, self::$legalPrm['withlogo'], true)){
					$available[$key] = $value;
				}
			}
			if ($key === 'logo') {
				if(isset($value['filename'], $value['percentage'], $value['absolute']) && is_string($value['filename']) && is_float($value['percentage']) && is_bool($value['absolute'])){
					$available[$key] = $value;
				};
			}
		}
		return array_merge(self::$config, $available);
	}
}