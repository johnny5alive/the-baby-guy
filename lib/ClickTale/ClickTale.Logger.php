<?php
/**
 * ClickTale - PHP Integration Module
 *
 * LICENSE
 *
 * This source file is subject to the ClickTale(R) Integration Module License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.clicktale.com/Integration/0.2/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@clicktale.com so we can send you a copy immediately.
 *
 */
?>
<?php

require_once(ClickTale_Root."/ClickTale.Settings.php");

class ClickTale_Logger
{		
	static $ins = null;
	static public function Write($data)
	{
		if(ClickTale_Logger::$ins == null) {
			ClickTale_Logger::$ins = rand();
		}
		$ins = ClickTale_Logger::$ins;
		$date = date("H:i:s");
		if (!self::Enabled())
			return false;
	
		$handle = fopen(self::getFullPath(), "a");
		fwrite($handle, "[$date - $ins] $data".PHP_EOL);
		fclose($handle);
	}
	
	static public function WriteDebug($data)
	{
		if(ClickTale_Settings::Instance()->AllowDebug) {
			ClickTale_Logger::Write($data);
		}
	}
	
	// Reads the latest log.
	static public function Read()
	{
		if (!file_exists(self::getFullPath()) || !self::Enabled())
			return false;
	
		$handle = fopen(self::getFullPath(), "r");
		$contents = fread($handle, filesize(self::getFullPath()));
		fclose($handle);
		return $contents;
	}
	
	static public function Enabled()
	{
		@$logFileNameMask = ClickTale_Settings::Instance()->LogPathMask;
		if (empty($logFileNameMask) || strtolower($logFileNameMask) == "false")
			return false;
		else
			return true;
	}
	
	// Gets full path of the actual log file.
	// {0} is being replaced by current date.
	static public function getFullPath()
	{		
		if (!self::Enabled())
			return false;
		
		$logFileNameMask = ClickTale_Settings::Instance()->LogPathMask;
		return str_replace("{0}", date("Ymd"), $logFileNameMask);
	}
}



?>
