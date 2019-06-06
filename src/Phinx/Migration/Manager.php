<?php

/**
 * Phinx
 *
 * (The MIT license)
 * Copyright (c) 2015 Rob Morgan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated * documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package    Phinx
 * @subpackage Phinx\Migration
 */

namespace Phinx\Migration;

use Phinx\Config\ConfigInterface;
use Phinx\Config\NamespaceAwareInterface;
use Phinx\Migration\Manager\Environment;
use Phinx\Seed\AbstractSeed;
use Phinx\Seed\SeedInterface;
use Phinx\Util\Util;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Manager extends ManagerBasic
{
    /**
     * @var array
     */
    protected $environments;

    /**
     * @var array
     */
    protected $migrations;

    /**
     * @var array
     */
    protected $seeds;

    /**
     * @var integer
     */
    const EXIT_STATUS_DOWN = 3;

    /**
     * @var integer
     */
    const EXIT_STATUS_MISSING = 2;

    /**
     * Class Constructor.
     *
     * @param \Phinx\Config\ConfigInterface $config Configuration Object
     * @param \Symfony\Component\Console\Input\InputInterface $input Console Input
     * @param \Symfony\Component\Console\Output\OutputInterface $output Console Output
     */
    public function __construct($config, $input = null, $output = null)
    {
        $this->setConfig($config);
        $this->setInput($input);
        $this->setOutput($output);
    }

    /**
     * Sets the environments.
     *
     * @param array $environments Environments
     * @return \Phinx\Migration\Manager
     */
    public function setEnvironments($environments = [])
    {
        $this->environments = $environments;

        return $this;
    }

    /**
     * Gets the manager class for the given environment.
     *
     * @param string $name Environment Name
     * @throws \InvalidArgumentException
     * @return \Phinx\Migration\Manager\Environment
     */
    public function getEnvironment($name)
    {
        if (isset($this->environments[$name])) {
            return $this->environments[$name];
        }

        // check the environment exists
        if (!$this->configHasEnvironment($name)) {
            throw new \InvalidArgumentException(sprintf(
                            'The environment "%s" does not exist',
                            $name
            ));
        }

        // create an environment instance and cache it
        $envOptions = $this->configGetEnvironment($name);
        $envOptions['version_order'] = $this->getVersionOrder();

        $environment = new Environment($name, $envOptions);
        $this->environments[$name] = $environment;
        $environment->setInput($this->getInput());
        $environment->setOutput($this->getOutput());

        return $environment;
    }

    /**
     * Sets the console input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input Input
     * @return \Phinx\Migration\Manager
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Gets the console input.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Sets the console output and verbose flag
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output Output
     * @return \Phinx\Migration\Manager
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        $this->verbose = ($output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG);
        return $this;
    }

    /**
     * Sets the config.
     *
     * @param  \Phinx\Config\ConfigInterface $config Configuration Object
     * @return \Phinx\Migration\Manager
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Gets the config.
     *
     * @return \Phinx\Config\ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Accessor
     */
    public function getVersionOrder()
    {
        return $this->getConfig()->getVersionOrder();
    }

    /**
     * Accessor
     */
    public function getSeedPaths()
    {
        return $this->getConfig()->getSeedPaths();
    }

    /**
     * Accessor
     */
    public function getMigrationPaths()
    {
        return $this->getConfig()->getSeedPaths();
    }

    /**
     * Accessor
     */
    public function configHasEnvironment($name)
    {
        return $this->getConfig()->hasEnvironment($name);
    }

    /**
     * Accessor
     */
    public function configGetEnvironment($name)
    {
        return $this->getConfig()->getEnvironment($name);
    }
}
