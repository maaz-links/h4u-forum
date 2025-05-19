<?php
namespace App\Services;

use App\Models\UserConfig;
use Schema;

class ModConfigValues {

    public static function LoadConfigValues() {
        $modifiedConfigs = [];
        // Reload config from database
        if (Schema::hasTable('user_configs')) {
            $allConfigs = UserConfig::get();
            foreach ($allConfigs as $c) {
                config([$c->key => $c->value]);
            }
            $modifiedConfigs = $allConfigs;
        }
        return $modifiedConfigs;
    }

    public static function getModifiedConfig($modifiedConfigs, $key = '') {
        if (empty($key)) {
            return null;
        }

        // Check in modifiedConfigs first
        foreach ($modifiedConfigs as $config) {
            if ($config->key === $key) {
                return $config->value;
            }
        }

        // Fallback to the existing config
        return config($key);
    }

}
