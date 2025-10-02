<?php

class WhiteListExtension extends Minz_Extension {
    public function init(): void {
        $this->registerTranslates();

        if (version_compare(FRESHRSS_VERSION, 1.28) >= 0) {
            $this->registerHook(Minz_HookType::CheckUrlBeforeAdd, [$this, 'checkWhiteList']);
        } else {
            $this->registerHook('check_url_before_add', [$this, 'checkWhiteList']);
        }
    }

    public function handleConfigureAction(): void {
        $this->registerTranslates();

        if (Minz_Request::isPost()) {
            $configuration = [
                'patterns' => Minz_Request::paramTextToArray('patterns', []),
            ];
            $this->setSystemConfiguration($configuration);
        }
    }

    public function checkWhiteList(string $url): string {
        $patterns = $this->getSystemConfigurationValue('patterns') ?? [];
        if (is_array($patterns)) {
            foreach ($patterns as $pattern) {
                if (1 === preg_match("/{$pattern}/i", $url)) {
                    return $url;
                }
            }
        }
        Minz_Log::warning(_t('ext.white_list.warning.not_white_listed', $url));
        throw new FreshRSS_FeedNotAdded_Exception($url);
    }

    public function getPatterns(): string {
        $patterns = $this->getSystemConfigurationValue('patterns') ?? [];
        if (is_array($patterns)) {
            return implode(PHP_EOL, $patterns);
        }
        return '';
    }
}
