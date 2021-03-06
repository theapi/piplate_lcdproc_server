<?php
namespace Theapi\Lcdproc\Server\Commands;


use Theapi\Lcdproc\Server\Server;
use Theapi\Lcdproc\Server\Render;
use Theapi\Lcdproc\Server\Exception\ClientException;

class ClientCommands
{

    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
        $this->container = $this->client->container;
    }

    /**
     * Debugging only..  prints out a list of arguments it receives
     */
    public function test($args)
    {
        $this->client->sendString(print_r($args, true));

        return 0;
    }

    /**
     * The client must say "hello" before doing anything else.
     *
     * Usage: hello
     */
    public function hello($args)
    {
        $this->client->setStateActive();

        $str = 'connect LCDproc 0.5dev protocol 0.3 lcd';
        $str .= ' wid ' . $this->client->container->drivers->displayProps->width;
        $str .= ' hgt ' . $this->client->container->drivers->displayProps->height;
        $str .= ' cellwid ' . $this->client->container->drivers->displayProps->cellWidth;
        $str .= ' cellhgt ' . $this->client->container->drivers->displayProps->cellHeight;
        $str .= "\n";
        $this->client->sendString($str);
    }

    /**
     * The client should say "bye" before disconnecting
     *
     * The function does not respond to the client: it simply cuts connection.
     */
    public function bye($args)
    {
        if (isset($this->client)) {
            $this->client->setStateGone();
        }

        return 0;
    }

    /**
     * Sets info about the client, such as its name
     *
     * Usage: client_set -name <id>
     */
    public function set($args)
    {
        if (!$this->client->isActive()) {
            return 1;
        }

        // NB: our arg count is one less than source as the function name has been extracted.
        if (count($args) != 2) {
            $this->client->container->log(LOG_DEBUG, print_r($args, true));
            throw new ClientException('Usage: client_set -name <name>');
        }

        $key = trim($args[0], ' -');
        $value = trim($args[1]);

        if (!empty($key) && !empty($value)) {
            if ($key != 'name') {
                throw new ClientException("invalid parameter ($key)");
            }

            $this->name = $value;
            $this->client->sendString("success\n");
        }

        return 0;
    }

    /**
     * Tells the server the client would like to accept keypresses
     * of a particular type
     */
    public function addKey($args)
    {
        if (!$this->client->isActive()) {
            return 1;
        }

        if (count($args) < 1) {
            throw new ClientException('Usage: client_add_key [-exclusively|-shared] {<key>}+');
        }

        // TODO input key stuff


        $this->client->sendString("success\n");

        return 0;
    }

    /**
     * Tells the server the client would NOT like to accept keypresses
     * of a particular type
     */
    public function delKey($args)
    {
        if (!$this->client->isActive()) {
            return 1;
        }

        if (count($args) < 1) {
            throw new ClientException('Usage: client_del_key {<key>}+');
        }

        // TODO input key stuff


        $this->client->sendString("success\n");

        return 0;
    }

    /**
     * Toggles the backlight, if enabled.
     *
     * And set backlight colours if possible :-)
     * The original reason for this porting to a language I know.
     * Still I've learned alot to get here...
     */
    public function backlight($args)
    {
        if (!$this->client->isActive()) {
            return 1;
        }

        if (count($args) != 1) {
            throw new ClientException('Usage: backlight {on|off|toggle|blink|flash}');
        }

        $arg = trim($args[0]);
        switch ($arg) {
            case 'on':
                $this->client->backlight = Render::BACKLIGHT_ON;
                break;
            case 'off':
                $this->client->backlight = Render::BACKLIGHT_OFF;
                break;
            case 'toggle':
                if ($this->client->backlight == Render::BACKLIGHT_ON) {
                    $this->client->backlight = Render::BACKLIGHT_OFF;
                } else {
                    $this->client->backlight = Render::BACKLIGHT_ON;
                }
                break;
            case 'blink':
                $this->client->backlight = Render::BACKLIGHT_BLINK;
                break;
            case 'flash':
                $this->client->backlight = Render::BACKLIGHT_FLASH;
                break;
            default:
                // maybe its a colour, let the driver decide
                $this->client->backlight = $arg;
                break;
        }


        $this->client->sendString("success\n");

        return 0;
    }

    /**
     * Sends back information about the loaded drivers.
     */
    public function info($args)
    {
        if (!$this->client->isActive()) {
            return 1;
        }

        $info = $this->client->container->drivers->getInfo();
        $this->client->sendString("$info\n");

        return 0;
    }
}
