<?php

namespace App\Webhook;

use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\InvalidConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class GithubValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            return false;
        }

        $signingSecret = $config->signingSecret;

        if (empty($signingSecret)) {
            throw InvalidConfig::signingSecretNotSet();
        }

        return $this->checkValid($signature, $request->getContent(), $signingSecret);
    }

    public function checkValid($signature, $content, $signingSecret): bool
    {
        $computedSignature = hash_hmac('sha256', $content, $signingSecret);
        return hash_equals('sha256=' . $computedSignature, $signature);
    }
}
