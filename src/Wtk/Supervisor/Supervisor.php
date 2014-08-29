<?php
namespace Wtk\Supervisor;

use RuntimeException;
use UnexpectedValueException;
use Indigo\Supervisor\Supervisor as SupervisorClient;
use MD\Foundation\Utils\ArrayUtils;

/**
 *
 * @author Wojtek Zalewski <wojtek@neverbland.com>
 */
class Supervisor
{
    /**
     * SDK
     *
     * @var SupervisorClient
     */
    protected $sdk;

    /**
     * @param  SupervisorClient     $sdk
     */
    public function __construct(SupervisorClient $sdk)
    {
        $this->sdk = $sdk;
    }

    /**
     *
     * @return SupervisorClient
     */
    public function getSdk()
    {
        return $this->sdk;
    }

    /**
     * Reload configuration
     *
     * @return array
     */
    public function reread()
    {
        return $this->getSdk()->reloadConfig();
    }

    /**
     * Returns info about all available process configurations.
     *
     * @see Supervisor API docs
     *
     * @return array
     */
    public function getAllConfigInfo()
    {
        return $this->getSdk()->getAllConfigInfo();
    }

    /**
     * @see Supervisor API docs
     *
     * @return array
     */
    public function getAllProcessInfo()
    {
        return $this->getSdk()->getAllProcessInfo();
    }

    /**
     * Pulls out from configuration known processes and its groups
     *
     * @return array
     */
    public function getConfiguredProcesses()
    {
        $known_process_groups = array();

        foreach ($this->getAllConfigInfo() as $configuration) {
            $known_process_groups[ArrayUtils::get($configuration, 'group')] =
                ArrayUtils::get($configuration, 'name');
        }

        return $known_process_groups;
    }
    /**
     * Returns list of available processes
     *
     * @return array
     */
    public function getAvailableProcesses()
    {
        $known_processes = array();

        foreach ($this->getAllProcessInfo() as $process) {
            $known_processes[ArrayUtils::get($process, 'group')] =
                ArrayUtils::get($process, 'name');
        }

        return $known_processes;
    }

    /**
     * Update the config for a running process from config file.
     *
     * @param string $name Name name of process group to add
     */
    public function addProcessGroup($name)
    {
        return $this->getSdk()->addProcessGroup($name);
    }

    /**
     * Start all processes in the group named 'name'
     *
     * @param string $groupName The group name
     * @param bool $wait Wait for process to be fully started
     * @return boolean Result always true unless error
     */
    public function startProcessGroup($name, $wait = true)
    {
        return $this->getSdk()->startProcessGroup($name);
    }

    public function startProcess($name, $wait = true)
    {
        return $this->getSdk()->startProcess($name, $wait);
    }

    /**
     * Remove a stopped process from the active configuration.
     *
     * @param string $name Name name of process group to remove
     */
    public function removeProcessGroup($name)
    {
        return $this->getSdk()->removeProcessGroup($name);
    }

    public function stopProcessGroup($name, $wait = true)
    {
        // Fuck me, supervisord returns empty response but it does
        // the job.
        // https://github.com/indigophp/supervisor/issues/12
        try {
            return $this->getSdk()->stopProcessGroup($name, $wait);
        } catch (UnexpectedValueException $e) {
            return true;
        }
    }

    public function update()
    {
        return $this->getSdk()->update();
    }
}









