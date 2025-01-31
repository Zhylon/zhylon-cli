<?php

namespace App\Commands\Concerns;

trait InteractsWithVersions
{
    /**
     * The latest version resolver.
     *
     * @var callable|null
     */
    protected static $latestVersionResolver = null;

    /**
     * Warns the user about the latest version of Forge CLI.
     *
     * @return void
     */
    protected function ensureLatestVersion()
    {
        $current = 'v'.config('app.version');

        if (version_compare($remote = $this->getLatestVersion(), $current) > 0) {
            $this->warnStep(['You are using an outdated version %s of Zhylon CLI. Please update to %s.', $current, $remote]);
        }
    }

    /**
     * Returns the latest version.
     *
     * @return string
     */
    protected function getLatestVersion()
    {
        $resolver = static::$latestVersionResolver ?? function () {
            $package = json_decode(file_get_contents(
                'https://packagist.org/p2/zhylon/zhylon-cli.json'
            ), true);

            return collect($package['packages']['zhylon/zhylon-cli'])
                ->first()['version'];
        };

        if (is_null($this->config->get('latest_version_verified_at'))) {
            $this->config->set('latest_version_verified_at', now()->timestamp);
        }

        if (is_null($this->config->get('latest_version'))) {
            $this->config->set('latest_version', call_user_func($resolver));
        }

        if ($this->config->get('latest_version_verified_at') < now()->subDays(1)->timestamp) {
            $this->config->set('latest_version', call_user_func($resolver));
            $this->config->set('latest_version_verified_at', now()->timestamp);
        }

        return $this->config->get('latest_version');
    }

    /**
     * Sets the latest version resolver.
     *
     * @param  callable  $resolver
     * @return void
     */
    public static function resolveLatestVersionUsing($resolver)
    {
        static::$latestVersionResolver = $resolver;
    }
}
