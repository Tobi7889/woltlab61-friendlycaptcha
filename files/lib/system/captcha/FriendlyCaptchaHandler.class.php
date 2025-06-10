<?php

/**
 * © 2024–2025 SpeedIT Solutions UG (haftungsbeschränkt), Isernhagen, Germany
 * 
 * This file is part of a licensed product. Redistribution, modification, or reuse
 * outside the scope of the applicable license agreement is strictly prohibited.
 * 
 * For more information, please refer to the full license terms:
 * https://www.speedit.org/
 */


namespace wcf\system\captcha;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use wcf\system\exception\UserInputException;
use wcf\system\io\HttpFactory;
use wcf\system\WCF;
use wcf\util\JSON;

/**
 * Captcha handler for FriendlyCaptcha.
 */
class FriendlyCaptchaHandler implements ICaptchaHandler
{
    /**
     * FriendlyCaptcha solution token
     */
    public string $solution = '';

    /**
     * @inheritDoc
     */
    public function getFormElement(): string
    {
        return WCF::getTPL()->fetch('shared_friendlycaptcha');
    }

    /**
     * @inheritDoc
     */
    public function isAvailable(): bool
    {
        // forceIsAvailable is a workaround for the captcha selection in the ACP
        return RecaptchaHandler::$forceIsAvailable
            || (FRIENDLYCAPTCHA_SITEKEY && FRIENDLYCAPTCHA_APIKEY);
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters(): void
    {
        if (isset($_POST['frc-captcha-solution'])) {
            $this->solution = $_POST['frc-captcha-solution'];
        } elseif (isset($_POST['parameters']['frc-captcha-solution'])) {
            $this->solution = $_POST['parameters']['frc-captcha-solution'];
        }
        if (FRIENDLYCAPTCHA_VERSION === 'v2') {
            if (isset($_POST['frc-captcha-response'])) {
                $this->solution = $_POST['frc-captcha-response'];
            } elseif (isset($_POST['parameters']['frc-captcha-response'])) {
                $this->solution = $_POST['parameters']['frc-captcha-response'];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        WCF::getSession()->unregister('friendlycaptchaDone');
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        if (WCF::getSession()->getVar('friendlycaptchaDone')) {
            return;
        }

        if (empty($this->solution)) {
            throw new UserInputException('friendlycaptchaString', 'false');
        }
        if (FRIENDLYCAPTCHA_VERSION === 'v1') {
            $verificationData = [
                'solution' => $this->solution,
                'secret'   => FRIENDLYCAPTCHA_APIKEY,
            ];
        } else {
            $verificationData = [
                'response' => $this->solution,
                'sitekey'  => FRIENDLYCAPTCHA_SITEKEY,
            ];
        }
        if (FRIENDLYCAPTCHA_VERSION === 'v1') {
            $endpoint = FRIENDLYCAPTCHA_ENDPOINT === 'eu'
                ? 'https://eu.friendlycaptcha.com'
                : 'https://api.friendlycaptcha.com';
            $versionPath = '/api/v1/siteverify';
            $headers = ['content-type' => 'application/x-www-form-urlencoded'];
            $body = http_build_query($verificationData);
        } else {
            $endpoint = FRIENDLYCAPTCHA_ENDPOINT === 'eu'
                ? 'https://eu.frcapi.com'
                : 'https://global.frcapi.com';
            $versionPath = '/api/v2/captcha/siteverify';
            $headers = [
                'content-type' => 'application/x-www-form-urlencoded',
                'X-API-KEY'    => FRIENDLYCAPTCHA_APIKEY,
            ];
            $body = http_build_query($verificationData);
        }

        $request = new Request(
            'POST',
            $endpoint . $versionPath,
            $headers,
            $body
        );

        try {
            $response = $this->getHttpClient()->send($request);
            $data = JSON::decode((string) $response->getBody());

            if (empty($data['success'])) {
                throw new UserInputException('friendlycaptchaString', 'false');
            }

            WCF::getSession()->register('friendlycaptchaDone', true);
        } catch (ClientExceptionInterface $e) {
            // log error, but accept captcha
            \wcf\functions\exception\logThrowable($e);
        }
    }

    private function getHttpClient(): ClientInterface
    {
        return HttpFactory::makeClientWithTimeout(5);
    }
}