<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BillingClient
{
    private $billingHost;

    public function __construct($billingHost)
    {
        $this->billingHost = $billingHost;
    }

    public function sendLoginRequest($username, $password)
    {
        $payload = json_encode(['username' => $username, 'password' => $password]);
         
        $ch = curl_init($this->billingHost . '/api/v1/auth');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)));

        $result = curl_exec($ch);
        
        if (curl_exec($ch) === false) {
            throw new HttpException(500);
        } else {
            return $result;
        }
        
        curl_close($ch);
    }

    public function sendRegisterRequest($email, $password)
    {
        $payload = json_encode(['email' => $email, 'password' => $password]);
         
        $ch = curl_init($this->billingHost . '/api/v1/register');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)));

        $result = curl_exec($ch);
        
        if (curl_exec($ch) === false) {
            throw new HttpException(500);
        } else {
            return $result;
        }
        
        curl_close($ch);
    }
}
