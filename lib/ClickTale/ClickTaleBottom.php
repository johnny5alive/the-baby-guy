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
    if(function_exists("ClickTale_DebugBuffers")) {
        ClickTale_DebugBuffers();
    }
    // Most of the time, output buffers are flushed automatically, uncomment one of the
    // following lines if you experience problems.
	// First uncomment the "WHILE" line and if errors then comment it back and uncomment the "TRY" line
    //while (@ob_end_flush());
    //try {while (ob_get_level() > 0) ob_end_flush();} catch( Exception $e ) {}
?>
