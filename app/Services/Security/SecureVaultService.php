<?php

namespace App\Services\Security;

use App\Models\ApiCredential;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class SecureVaultService
{
    /**
     * Store encrypted secret API key credential.
     */
    public function storeCredential(string $providerName, string $label, string $secretValue): ApiCredential
    {
        $maskedValue = $this->maskSecret($secretValue);
        $encryptedPayload = Crypt::encryptString($secretValue);

        return ApiCredential::create([
            'provider_name' => $providerName,
            'label' => $label,
            'masked_value' => $maskedValue,
            'encrypted_payload' => $encryptedPayload,
            'status' => 'active',
            'last_tested_at' => now(),
        ]);
    }

    /**
     * Retrieve decrypted raw secret string.
     */
    public function getDecryptedSecret(ApiCredential $credential): string
    {
        return Crypt::decryptString($credential->encrypted_payload);
    }

    /**
     * Mask sensitive API keys securely.
     */
    public function maskSecret(string $secret): string
    {
        $length = strlen($secret);
        if ($length <= 8) {
            return Str::mask($secret, '*', 2, $length - 4);
        }

        $prefix = substr($secret, 0, 4);
        $suffix = substr($secret, -4);
        return $prefix . str_repeat('•', max(6, $length - 8)) . $suffix;
    }

    /**
     * Replace existing secret without revealing old secret.
     */
    public function replaceCredential(ApiCredential $credential, string $newSecretValue, ?string $newLabel = null): ApiCredential
    {
        $credential->update([
            'label' => $newLabel ?? $credential->label,
            'masked_value' => $this->maskSecret($newSecretValue),
            'encrypted_payload' => Crypt::encryptString($newSecretValue),
            'status' => 'active',
            'last_tested_at' => now(),
        ]);

        return $credential;
    }
}
