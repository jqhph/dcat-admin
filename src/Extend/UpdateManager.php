<?php

namespace Dcat\Admin\Extend;

use Dcat\Admin\Exception\AdminException;
use Illuminate\Support\Facades\Artisan;

/**
 * Class UpdateManager.
 *
 * @see https://github.com/octobercms/october/blob/develop/modules/system/classes/UpdateManager.php
 */
class UpdateManager
{
    use Note;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var VersionManager
     */
    protected $versionManager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->versionManager = $manager->versionManager();
    }

    public function install($extension)
    {
        return $this->update($extension);
    }

    public function uninstall($extension)
    {
        return $this->rollback($extension);
    }

    public function rollback($name, ?string $stopOnVersion = null)
    {
        if (
            ! ($extension = $this->manager->get($name))
            && $this->versionManager->purge($name)
        ) {
            $this->note('<info>Purged from database:</info> '.$name);

            return $this;
        }

        if ($stopOnVersion === null) {
            $extension->uninstall();
        }

        if ($stopOnVersion && ! $this->versionManager->hasDatabaseVersion($extension, $stopOnVersion)) {
            throw new AdminException('Extension version not found');
        }

        if ($this->versionManager->remove($extension, $stopOnVersion, true)) {
            $this->note('<info>Rolled back:</info> '.$name);

            if ($currentVersion = $this->versionManager->getCurrentVersion($extension)) {
                $this->note('<info>Current Version:</info> '.$currentVersion.' ('.$this->versionManager->getCurrentVersionNote($extension).')');
            }

            return $this;
        }

        $this->note('<error>Unable to find:</error> '.$name);

        return $this;
    }

    public function update($name, ?string $stopOnVersion = null)
    {
        $name = $this->manager->getName($name);

        if (! ($extension = $this->manager->get($name))) {
            $this->note('<error>Unable to find:</error> '.$name);

            return;
        }

        $this->note($name);

        $this->versionUpdate($extension, $stopOnVersion);

        $this->publish($name);

        return $this;
    }

    /**
     * 发布扩展资源.
     *
     * @param string $name
     */
    public function publish($name)
    {
        $name = $this->manager->getName($name);

        $this->manager->get($name)->publishable();

        Artisan::call('vendor:publish', ['--force' => true, '--tag' => $name]);
    }

    protected function versionUpdate($extension, $stopOnVersion)
    {
        $this->versionManager->notes = [];
        $this->versionManager->output = $this->output;

        if ($this->versionManager->update($extension, $stopOnVersion) !== false) {
            foreach ($this->versionManager->notes as $note) {
                $this->note($note);
            }
        }
    }
}
