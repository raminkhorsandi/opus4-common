<?php
/**
 * This file is part of OPUS. The software OPUS has been originally developed
 * at the University of Stuttgart with funding from the German Research Net,
 * the Federal Department of Higher Education and Research and the Ministry
 * of Science, Research and the Arts of the State of Baden-Wuerttemberg.
 *
 * OPUS 4 is a complete rewrite of the original OPUS software and was developed
 * by the Stuttgart University Library, the Library Service Center
 * Baden-Wuerttemberg, the Cooperative Library Network Berlin-Brandenburg,
 * the Saarland University and State Library, the Saxon State Library -
 * Dresden State and University Library, the Bielefeld University Library and
 * the University Library of Hamburg University of Technology with funding from
 * the German Research Foundation and the European Regional Development Fund.
 *
 * LICENCE
 * OPUS is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or any later version.
 * OPUS is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License
 * along with OPUS; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * @category    Framework
 * @package     Opus_Model
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2018-2019, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

namespace Opus\Model;

use Opus\Model\Plugin\PluginInterface;

/**
 * Trait for adding plugin support to a class.
 *
 * This trait is used to add plugins to model classes. Plugins for instance update the cache after store operations or
 * trigger a reindexing. Plugins also create URNs or DOIs. The plugins implement technical functions like cache updates,
 * but also business logic, like adding a URN, when a document is published.
 *
 * A new plugin object is created for every model object. Every Opus_Document object for instance has multiple plugin
 * objects.
 *
 * Each model class defines default plugins that are always used. It should be possible to add additional plugins. The
 * mechanism should in both cases be the same. All the plugins are 'added' at some point. Some plugins are just the
 * default for a particular class.
 *
 * If a plugin is not optional it should not be a plugin. TODO correct?
 *
 * TODO allow registering (default) plugins that are then used for all new objects (static)
 * TODO support explizit ordering of plugins
 * TODO Reintegrate code in appropriate class if this Trait is not needed in more than one class. (Currently mostly used to understand code better.)
 *
 * It is necessary to manage the default plugins for the class. Some are registered as part of the class definition,
 * others are added at runtime like the optional cache or indexing plugin. The registered plugins are the loaded for
 * every new object. Plugins can be remove from an object or added. This affects only the concrete object.
 *
 * It must be possible to register new plugins in a decentralized fashion in order to be open to extension, but closed
 * for modification. It should not be necessary to edit the list of class level default plugins.
 *
 */
trait PluginsTrait
{

    /**
     * Array mapping plugin class names to model plugins.
     *
     * Copy-Paste from Qucosa-Code base.
     *
     * NOTE: Modified name from $_plugins so that plugins management is only internal to PluginsTrait. Outside, the
     * default plugins are still defined in $_plugins. That name might be changed in the future to $defaultPlugins.
     *
     * @var array
     */
    protected $_plugins = [];

    private static $defaultPlugins = [];

    private $plugins = [];

    /**
     *
     * @param $plugin
     */
    public static function addDefaultPlugin($plugin)
    {
        self::$defaultPlugins[$plugin] = $plugin;
    }

    public static function removeDefaultPlugin($plugin)
    {
        if (isset(self::$defaultPlugins[$plugin])) {
            unset(self::$defaultPlugins[$plugin]);
        }
    }

    public static function getDefaultPlugins()
    {
        return self::$defaultPlugins;
    }

    public static function isDefaultPlugin($plugin)
    {
        return array_key_exists($plugin, self::$defaultPlugins);
    }

    /**
     * Instanciate and install plugins for this model.
     *
     * Copy-Paste from Qucosa-Code base.
     *
     * @return void
     */
    protected function _loadPlugins()
    {
        $plugins = $this->getDefaultPlugins();

        foreach ($plugins as $pluginClass => $plugin) {
            if (true === is_string($plugin)) {
                $pluginClass = $plugin;
                $plugin = null;
            }

            if (null === $plugin) {
                $this->registerPlugin($pluginClass);
            } else {
                $this->registerPlugin($plugin);
            }
        }
    }

    /**
     * Registers a plugin with the model object.
     *
     * Copy-Paste from Qucosa-Code base.
     *
     * @param PluginInterface|string $plugin Plugin to register for this very model.
     * @return void
     */
    public function registerPlugin($plugin)
    {
        $pluginClass = null;

        if (true === is_string($plugin)) {
            $pluginClass = $plugin;
            $plugin = new $pluginClass;
        } else {
            $pluginClass = get_class($plugin);
        }

        $this->plugins[$pluginClass] = $plugin;
    }

    /**
     * Unregister a pre- or post processing plugin.
     *
     * Copy-Paste from Qucosa-Code base.
     *
     * @param string|object $plugin Instance or class name to unregister plugin.
     * @throw Opus_Model_Exception Thrown if specified plugin does not exist.
     * @return void
     */
    public function unregisterPlugin($plugin)
    {
        $key = '';
        if (true === is_string($plugin)) {
            $key = $plugin;
        }
        if (true === is_object($plugin)) {
            $key = get_class($plugin);
        }
        if (false === isset($this->plugins[$key])) {
            // don't throw exception, just write a warning
            $this->getLogger()->warn('Cannot unregister specified plugin: ' . $key);
        } else {
            unset($this->plugins[$key]);
        }
    }

    /**
     * Return true if the given plugin was already registered; otherwise false.
     * @param string $plugin class name of the plugin
     */
    public function hasPlugin($plugin)
    {
        return array_key_exists($plugin, $this->plugins);
    }

    /**
     * Returns array with classes for default plugins defined in model class.
     *
     * TODO Is this a good way to define the defaults or should the function be overwritten.
     * TODO modify name of function
     *
     * @return mixed
     */
    public function getDefaultPlugins2()
    {
        return $this->_plugins;
    }

    /**
     * Calls a specified plugin method in all available plugins.
     *
     * Copy-Paste from Qucosa-Code base.
     *
     * @param string $methodname Name of plugin method to call
     * @param mixed  $parameter  Value that gets passed instead of the model instance.
     */
    protected function _callPluginMethod($methodname, $parameter = null)
    {
        try {
            if (null === $parameter) {
                $param = $this;
            } else {
                $param = $parameter;
            }

            foreach ($this->plugins as $name => $plugin) {
                $plugin->$methodname($param);
            }
        } catch (\Exception $ex) {
            throw new Exception('Plugin ' . $name . ' failed in ' . $methodname . ' with ' . $ex->getMessage());
        }
    }

    /**
     * Function must be provided to obtain logger from trait using class.
     * @return mixed
     */
    abstract public function getLogger();
}
