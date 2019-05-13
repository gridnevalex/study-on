<?php

namespace App\Tests\Mock;

use App\Service\BillingClient;

class BillingClientMock extends BillingClient
{
    public function sendLoginRequest($username, $password)
    {
        $trueUserName = "simpleUser@gmail.com";
        $trueUserPassword = "passwordForSimpleUser";

        $trueAdminName = "adminUser@gmail.com";
        $trueAdminPassword = "passwordForAdminUser";

        if ($username == $trueUserName && $password == $trueUserPassword) {
            return json_encode(['token' => 'someToken', 'roles' => ["ROLE_USER"]]);
        } elseif ($username == $trueAdminName && $password == $trueAdminPassword) {
            return json_encode(['token' => 'someToken', 'roles' => ["ROLE_SUPER_ADMIN"]]);
        } else {
            return json_encode(['code' => 401, 'message' => 'Bad credentials, please verify your username and password']);
        }
    }

    public function sendRegisterRequest($email, $password)
    {
        $trueUserEmail= "simpleUser@gmail.com";
        $trueUserPassword = "passwordForSimpleUser";

        if ($email == $trueUserEmail) {
            return json_encode(['errors' => ["Email already exists"]]);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['errors' => ['Invalid email']]);
        } else {
            return json_encode(['token' => 'someToken', 'roles' => ["ROLE_USER"]]);
        }
    }
}
