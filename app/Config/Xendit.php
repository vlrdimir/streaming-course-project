<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Xendit extends BaseConfig
{
    public string $baseUrl = 'https://api.xendit.co/';
    public string $secretKey = '';
    public string $callbackToken = '';
    public string $successRedirectUrl = '';
    public string $failureRedirectUrl = '';
    public int $invoiceDuration = 86400;
    public string $currency = 'IDR';
    public int $timeout = 30;
    public int $connectTimeout = 10;

    public function __construct()
    {
        parent::__construct();

        $this->baseUrl = $this->stringEnv('xendit.baseUrl', $this->baseUrl);
        $this->secretKey = $this->stringEnv('xendit.secretKey', $this->secretKey);
        $this->callbackToken = $this->stringEnv('xendit.callbackToken', $this->callbackToken);
        $this->successRedirectUrl = $this->stringEnv('xendit.successRedirectUrl', $this->successRedirectUrl);
        $this->failureRedirectUrl = $this->stringEnv('xendit.failureRedirectUrl', $this->failureRedirectUrl);
        $this->currency = strtoupper($this->stringEnv('xendit.currency', $this->currency));
        $this->invoiceDuration = $this->intEnv('xendit.invoiceDuration', $this->invoiceDuration);
        $this->timeout = $this->intEnv('xendit.timeout', $this->timeout);
        $this->connectTimeout = $this->intEnv('xendit.connectTimeout', $this->connectTimeout);
    }

    private function stringEnv(string $key, string $default): string
    {
        $value = env($key);

        if ($value === null || $value === false) {
            return $default;
        }

        return trim((string) $value);
    }

    private function intEnv(string $key, int $default): int
    {
        $value = env($key);

        if ($value === null || $value === false || $value === '') {
            return $default;
        }

        return max(1, (int) $value);
    }
}
