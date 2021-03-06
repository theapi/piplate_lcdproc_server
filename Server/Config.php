<?php
namespace Theapi\Lcdproc\Server;

use Theapi\Lcdproc\Server\Render;
use Theapi\Lcdproc\Server\ServerScreens;

/**
 * Confiuration
 */

/* This file is part of phpLCDd, the php lcdproc server.
 *
 * This file is released under the GNU General Public License.
 * Refer to the COPYING file distributed with this package.
 *
 */

class Config
{

    const AUTOROTATE_OFF = 0;
    const AUTOROTATE_ON = 1;


    protected $serverScreen = ServerScreens::SERVERSCREEN_ON;
    protected $helloMsg =  array('Welcome to', 'PHP LCDproc');

    protected $backlight = Render::BACKLIGHT_OPEN;
    protected $heartbeat = Render::HEARTBEAT_OPEN;

    protected $duration = 32;
    protected $titleSpeed = Render::TITLESPEED_MAX;
    protected $autoRotate = self::AUTOROTATE_ON;

    public function __construct($config = array())
    {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }
}
