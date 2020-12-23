<?php

namespace Dcat\Admin\Extend;

use Carbon\Carbon;
use Dcat\Admin\Models\Extension;
use Dcat\Admin\Models\ExtensionHistory;
use Dcat\Admin\Support\DatabaseUpdater;
use Illuminate\Support\Arr;

/**
 * Class VersionManager.
 *
 * @see https://github.com/octobercms/october/blob/develop/modules/system/classes/VersionManager.php
 */
class VersionManager
{
    use Note;

    const NO_VERSION_VALUE = 0;

    const HISTORY_TYPE_COMMENT = 1;
    const HISTORY_TYPE_SCRIPT = 2;

    protected $fileVersions;
    protected $databaseVersions;
    protected $databaseHistory;
    protected $updater;
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->updater = new DatabaseUpdater();
    }

    public function update($extension, $stopOnVersion = null)
    {
        $name = $this->manager->getName($extension);

        if (! $this->hasVersionFile($name)) {
            return false;
        }

        $currentVersion = $this->getLatestFileVersion($name);
        $databaseVersion = $this->getDatabaseVersion($name);

        if ($currentVersion === $databaseVersion) {
            $this->note('- <info>Nothing to update.</info>');

            return;
        }

        $this->manager->get($extension)->update($databaseVersion, $stopOnVersion ?: $currentVersion);

        $newUpdates = $this->getNewFileVersions($name, $databaseVersion);

        foreach ($newUpdates as $version => $details) {
            $this->applyExtensionUpdate($name, $version, $details);

            if ($stopOnVersion === $version) {
                return true;
            }
        }

        return true;
    }

    public function listNewVersions($extension)
    {
        $name = $this->manager->getName($extension);

        if (! $this->hasVersionFile($name)) {
            return [];
        }

        return $this->getNewFileVersions($name, $this->getDatabaseVersion($name));
    }

    protected function applyExtensionUpdate($name, $version, $details)
    {
        [$comments, $scripts] = $this->extractScriptsAndComments($details);

        foreach ($scripts as $script) {
            if ($this->hasDatabaseHistory($name, $version, $script)) {
                continue;
            }

            $this->applyDatabaseScript($name, $version, $script);
        }

        if (! $this->hasDatabaseHistory($name, $version)) {
            foreach ($comments as $comment) {
                $this->applyDatabaseComment($name, $version, $comment);

                $this->note(sprintf('- <info>v%s: </info> %s', $version, $comment));
            }
        }

        $this->setDatabaseVersion($name, $version);
    }

    public function remove($extension, $stopOnVersion = null, $stopCurrentVersion = false)
    {
        $name = $this->manager->getName($extension);

        if (! $this->hasVersionFile($name)) {
            return false;
        }

        $extensionHistory = $this->getDatabaseHistory($name);
        $extensionHistory = array_reverse($extensionHistory);

        $stopOnNextVersion = false;
        $newExtensionVersion = null;

        try {
            foreach ($extensionHistory as $history) {
                if ($stopCurrentVersion && $stopOnVersion === $history->version) {
                    $newExtensionVersion = $history->version;

                    break;
                }

                if ($stopOnNextVersion && $history->version !== $stopOnVersion) {
                    $newExtensionVersion = $history->version;

                    break;
                }

                if ($history->type == static::HISTORY_TYPE_COMMENT) {
                    $this->removeDatabaseComment($name, $history->version);
                } elseif ($history->type == static::HISTORY_TYPE_SCRIPT) {
                    $this->removeDatabaseScript($name, $history->version, $history->detail);
                }

                if ($stopOnVersion === $history->version) {
                    $stopOnNextVersion = true;
                }
            }
        } catch (\Throwable $exception) {
            $lastHistory = $this->getLastHistory($name);
            if ($lastHistory) {
                $this->setDatabaseVersion($name, $lastHistory->version);
            }
            throw $exception;
        }

        $this->setDatabaseVersion($name, $newExtensionVersion);

        if (isset($this->fileVersions[$name])) {
            unset($this->fileVersions[$name]);
        }

        if (isset($this->databaseVersions[$name])) {
            unset($this->databaseVersions[$name]);
        }

        if (isset($this->databaseHistory[$name])) {
            unset($this->databaseHistory[$name]);
        }

        return true;
    }

    public function purge($name)
    {
        $name = $this->manager->getName($name);

        $versions = Extension::query()->where('name', $name);

        if ($countVersions = $versions->count()) {
            $versions->delete();
        }

        $history = ExtensionHistory::query()->where('name', $name);

        if ($countHistory = $history->count()) {
            $history->delete();
        }

        return $countHistory + $countVersions;
    }

    protected function getLatestFileVersion($name)
    {
        $versionInfo = $this->getFileVersions($name);
        if (! $versionInfo) {
            return static::NO_VERSION_VALUE;
        }

        return trim(key(array_slice($versionInfo, -1, 1)));
    }

    public function getNewFileVersions($name, $version = null)
    {
        $name = $this->manager->getName($name);

        if ($version === null) {
            $version = static::NO_VERSION_VALUE;
        }

        $versions = $this->getFileVersions($name);

        $position = array_search($version, array_keys($versions));

        return array_slice($versions, ++$position);
    }

    public function getFileVersions($name)
    {
        $name = $this->manager->getName($name);

        if ($this->fileVersions !== null && array_key_exists($name, $this->fileVersions)) {
            return $this->fileVersions[$name];
        }

        $versionInfo = (array) $this->parseVersionFile($this->getVersionFile($name));

        if ($versionInfo) {
            uksort($versionInfo, function ($a, $b) {
                return version_compare($a, $b);
            });
        }

        return $this->fileVersions[$name] = $versionInfo;
    }

    protected function parseVersionFile($file)
    {
        if ($file && is_file($file)) {
            return include $file;
        }
    }

    protected function getVersionFile($name)
    {
        return $this->manager->path($name, 'version.php');
    }

    protected function hasVersionFile($name)
    {
        $versionFile = $this->getVersionFile($name);

        return $versionFile && is_file($versionFile);
    }

    protected function getDatabaseVersion($name)
    {
        if ($this->databaseVersions === null) {
            $this->databaseVersions = Extension::query()->pluck('version', 'name');
        }

        if (! isset($this->databaseVersions[$name])) {
            $this->databaseVersions[$name] =
                Extension::query()
                ->where('name', $name)
                ->value('version');
        }

        return $this->databaseVersions[$name] ?? static::NO_VERSION_VALUE;
    }

    protected function setDatabaseVersion($name, $version = null)
    {
        $currentVersion = $this->getDatabaseVersion($name);

        if ($version && ! $currentVersion) {
            Extension::query()->create([
                'name'    => $name,
                'version' => $version,
            ]);
        } elseif ($version && $currentVersion) {
            Extension::query()->where('name', $name)->update([
                'version'    => $version,
                'updated_at' => new Carbon,
            ]);
        } elseif ($currentVersion) {
            Extension::query()->where('name', $name)->delete();
        }

        $this->databaseVersions[$name] = $version;
    }

    protected function applyDatabaseComment($name, $version, $comment)
    {
        ExtensionHistory::query()->create([
            'name'    => $name,
            'type'    => static::HISTORY_TYPE_COMMENT,
            'version' => $version,
            'detail'  => $comment,
        ]);
    }

    protected function removeDatabaseComment($name, $version)
    {
        ExtensionHistory::query()
            ->where('name', $name)
            ->where('type', static::HISTORY_TYPE_COMMENT)
            ->where('version', $version)
            ->delete();
    }

    protected function applyDatabaseScript($name, $version, $script)
    {
        $updateFile = $this->manager->path($name, 'updates/'.$script);

        if (! is_file($updateFile)) {
            $this->note(sprintf('- <error>v%s:  Migration file "%s" not found</error>', $version, $script));

            return;
        }

        $this->updater->setUp($this->resolveUpdater($name, $updateFile), function () use ($name, $version, $script) {
            ExtensionHistory::query()->create([
                'name'    => $name,
                'type'    => static::HISTORY_TYPE_SCRIPT,
                'version' => $version,
                'detail'  => $script,
            ]);
        });

        $this->note(sprintf('- <info>v%s:  Migrated</info> %s', $version, $script));
    }

    protected function resolveUpdater($name, $updateFile)
    {
        $updater = $this->updater->resolve($updateFile);

        if (method_exists($updater, 'setExtension')) {
            $updater->setExtension($this->manager->get($name));
        }

        return $updater;
    }

    protected function removeDatabaseScript($name, $version, $script)
    {
        $updateFile = $this->manager->path($name, 'updates/'.$script);

        $this->updater->packDown($this->resolveUpdater($name, $updateFile), function () use ($name, $version, $script) {
            ExtensionHistory::query()
                ->where('name', $name)
                ->where('type', static::HISTORY_TYPE_SCRIPT)
                ->where('version', $version)
                ->where('detail', $script)
                ->delete();
        });
    }

    protected function getDatabaseHistory($name)
    {
        if ($this->databaseHistory !== null && array_key_exists($name, $this->databaseHistory)) {
            return $this->databaseHistory[$name];
        }

        $historyInfo = ExtensionHistory::query()
            ->where('name', $name)
            ->orderBy('id')
            ->get()
            ->all();

        return $this->databaseHistory[$name] = $historyInfo;
    }

    protected function getLastHistory($name)
    {
        return ExtensionHistory::query()
            ->where('name', $name)
            ->orderByDesc('id')
            ->first();
    }

    protected function hasDatabaseHistory($name, $version, $script = null)
    {
        $historyInfo = $this->getDatabaseHistory($name);
        if (! $historyInfo) {
            return false;
        }

        foreach ($historyInfo as $history) {
            if ($history->version != $version) {
                continue;
            }

            if ($history->type == static::HISTORY_TYPE_COMMENT && ! $script) {
                return true;
            }

            if ($history->type == static::HISTORY_TYPE_SCRIPT && $history->detail == $script) {
                return true;
            }
        }

        return false;
    }

    protected function extractScriptsAndComments($details): array
    {
        $details = (array) $details;

        $fileNamePattern = "/^[a-z0-9\_\-\.\/\\\]+\.php$/i";

        $comments = array_values(array_filter($details, function ($detail) use ($fileNamePattern) {
            return ! preg_match($fileNamePattern, $detail);
        }));

        $scripts = array_values(array_filter($details, function ($detail) use ($fileNamePattern) {
            return preg_match($fileNamePattern, $detail);
        }));

        return [$comments, $scripts];
    }

    public function getCurrentVersion($extension): string
    {
        return $this->getDatabaseVersion($this->manager->getName($extension));
    }

    public function hasDatabaseVersion($extension, string $version): bool
    {
        $name = $this->manager->getName($extension);

        $histories = $this->getDatabaseHistory($name);

        foreach ($histories as $history) {
            if ($history->version === $version) {
                return true;
            }
        }

        return false;
    }

    public function getCurrentVersionNote($extension): string
    {
        $name = $this->manager->getName($extension);

        $histories = $this->getDatabaseHistory($name);

        $lastHistory = Arr::last(Arr::where($histories, function ($history) {
            return $history->type === static::HISTORY_TYPE_COMMENT;
        }));

        return $lastHistory ? $lastHistory->detail : '';
    }
}
