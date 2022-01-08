<?php

class WhiteListExtension extends Minz_Extension {
    public function init() {
        $this->registerTranslates();

        $this->registerHook('check_url_before_add', [$this, 'checkWhiteList']);
    }
    
    public function handleConfigureAction() {
        $this->registerTranslates();

        if (Minz_Request::isPost()) {
            $configuration = [
                'patterns' => Minz_Request::paramTextToArray('patterns', ''),
            ];
            $this->setSystemConfiguration($configuration);
        }
    }

    public function checkWhiteList($url) {
        foreach ($this->getSystemConfigurationValue('patterns') as $pattern) {
            if (1 === preg_match("/{$pattern}/i", $url)) {
                return $url;
            }
        }
        Minz_Log::warning(_t('ext.white_list.warning.not_white_listed', $url));
        throw new FreshRSS_FeedNotAdded_Exception($url);
    }

    public function getPatterns() {
        return implode(PHP_EOL, $this->getSystemConfigurationValue('patterns') ?? []);
    }
}
