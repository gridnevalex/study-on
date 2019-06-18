<?php

namespace App\Tests\Mock;

use App\Service\BillingClient;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BillingClientMock extends BillingClient
{
    public function sendLoginRequest($username, $password)
    {
        $trueUserName = "simpleUser@gmail.com";
        $trueUserPassword = "passwordForSimpleUser";

        $trueAdminName = "adminUser@gmail.com";
        $trueAdminPassword = "passwordForAdminUser";

        if ($username == $trueUserName && $password == $trueUserPassword) {
            return ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjA3NzQ4OTgsImV4cCI6MTU2MDc3ODQ5OCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiYWxleEBtYWlsLnJ1In0.jBqzJ_BWOs7_nXFncCtM_PNiLVIgoZqaXtsa0bmUV5sHfUbZczuZxLsW6jsuSK4soLAP3qmlUoQOLJvD9xrgMXTxTtGWsCwQ7a8qcBoQVUqlWQtwhDBDWTKkt48GmodXLUe7iIs08st31E3C6ly6DjZZvLFvfBuh3G7kcHWGsKpPZLiuX8BCiMZbgXFzeBEEObXNaFZn6DTx6NYBTt5kB6CpTbraogO30T2sxkXt2n8g-7RhQjb2dAdFg5FQx6k75W07lXqFkHleVCSXgRAgEJ_8eA1WfkuNWf2NGJLUsfAZTjPNGFuIjWl3bFhimkG8CeihqyNhjKrxUPfgmnMRJMTGE37_WPq4emAkSmb0SoxvKsqi9VTxzpyrOv6bN6BIuk6pCpwPRA6nHMXC2TL0AyMPh0ZeOWnjX9hhUdaS0G_asXDLXw7lVWoRH3BTmDv8fYq8obVwG4M6ojvdtuqiexmbT0JSodP241QhvUNIIQS2DyQ3m2GL2nHLuFs2FDRjiE8b4vWeCpzNCXnjiluB_OWU3ar7BCdMV5M49sBWX51WAHX8x7QyqC5zuoVBM8Rd1k-nMe_5v2BMpx-BRYhoos5Kh9jyCDLL8OcQCNXG2TLJBuGYPBH2_On1iGWXcRQZWhOCA7SntovUyYf7Z3IgUXZ4R4Hd-XMdxq5tCxKCntk', 'roles' => ["ROLE_USER"], 'refresh_token' => '6c511dd5e0e2ad0ae0dbd82aa1a89160385a611f622cd1d6e9908713e0f24f38d0c3189f5c1e8b419412391a6e8680c7035033d3c0f2ed7f6b192b5fdfa265be'];
        } elseif ($username == $trueAdminName && $password == $trueAdminPassword) {
            return ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjA3NzQ5NzIsImV4cCI6MTU2MDc3ODU3Miwicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluVXNlckBnbWFpbC5jb20ifQ.SPHtkXbVDUcG2odAXLeEY9wnEeVT2oHGc9WhIgaASFDyjhuj3vzqzGLsWKON-NZnCDliBImEaTvsXaUUjSYNNgi8aOG-8dE8xSiXQjsgcMKOLeVeps3p-1OqQ_544D4PSiKFtJKYd9iVyaZHvIr4iZ4-xm5kt-lE32kS8b6lgG7gld3UyDrjUKACa2byef0xbRIB1XF1AL5ZBSxP3-BcZxY-4YSfAZiy1dI-7lLlPB1KSlpAMVHJ9fKJHBSnJ0wQpBxbAEcfLZpsjSYXpW5YvJlexTjS88zdGUQv5huXcBRzSw5WBMXRGjl0GVYvvGzeMcNSVed1duQ_X36kqra1LeQOGmD2fERPlo1IDzfs99_YthG-yahDDlWXFVMGgliXcPweyDky3xIC8Bub3o3XgMFMBmVrEsd1M2W2hflqCShwynFboqdFAL15w1W_3e5dpPoBEgBTY1wIMh7gB2mDtwGLswNk7rDpkzOaBES1R2qACPyg0v_OtC-9nQQCPBSV1wB8MeZwr7X1g6wzks4V8t5PXkDR1opaCHi0ENk3WcJ1CmRF9B00u7SZ-dQhd0Mg-X3hY6x7uR-cQEM_bSmddAu6R8yeiorWOOU7GXqS_PX79ijaqJrnRChZuVoLmARyDGEpmhN4MLK7llTC_kluLQmY44oje_6vZx87d60EWNw', 'roles' => ["ROLE_SUPER_ADMIN"], 'refresh_token' => 'ed2e20e571a9284abb0c6cc8d00a2abc01bd2dcc9f702024733aa941b6be31c91fbdcb9e348b372be32f64c90c28cd52b175a8d44aa13fbff9ae70a26485ce10'];
        } elseif ($username == "throwException@mail.ru") {
            throw new HttpException(500);
        } else {
            return ['code' => 401, 'message' => 'Bad credentials, please verify your username and password'];
        }
    }

    public function sendRegisterRequest($email, $password)
    {
        $trueUserEmail= "simpleUser@gmail.com";
        $trueUserPassword = "passwordForSimpleUser";

        if ($email == $trueUserEmail) {
            return ['code' => 400, 'message' => ["Email already exists"]];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['code' => 400, 'message' => ['Invalid email']];
        } elseif ($email == "throwException@mail.ru") {
            throw new HttpException(500);
        } else {
            return ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjA3NzQ4OTgsImV4cCI6MTU2MDc3ODQ5OCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiYWxleEBtYWlsLnJ1In0.jBqzJ_BWOs7_nXFncCtM_PNiLVIgoZqaXtsa0bmUV5sHfUbZczuZxLsW6jsuSK4soLAP3qmlUoQOLJvD9xrgMXTxTtGWsCwQ7a8qcBoQVUqlWQtwhDBDWTKkt48GmodXLUe7iIs08st31E3C6ly6DjZZvLFvfBuh3G7kcHWGsKpPZLiuX8BCiMZbgXFzeBEEObXNaFZn6DTx6NYBTt5kB6CpTbraogO30T2sxkXt2n8g-7RhQjb2dAdFg5FQx6k75W07lXqFkHleVCSXgRAgEJ_8eA1WfkuNWf2NGJLUsfAZTjPNGFuIjWl3bFhimkG8CeihqyNhjKrxUPfgmnMRJMTGE37_WPq4emAkSmb0SoxvKsqi9VTxzpyrOv6bN6BIuk6pCpwPRA6nHMXC2TL0AyMPh0ZeOWnjX9hhUdaS0G_asXDLXw7lVWoRH3BTmDv8fYq8obVwG4M6ojvdtuqiexmbT0JSodP241QhvUNIIQS2DyQ3m2GL2nHLuFs2FDRjiE8b4vWeCpzNCXnjiluB_OWU3ar7BCdMV5M49sBWX51WAHX8x7QyqC5zuoVBM8Rd1k-nMe_5v2BMpx-BRYhoos5Kh9jyCDLL8OcQCNXG2TLJBuGYPBH2_On1iGWXcRQZWhOCA7SntovUyYf7Z3IgUXZ4R4Hd-XMdxq5tCxKCntk', 'roles' => ["ROLE_USER"], 'refresh_token' => 'refreshToken'];
        }
    }

    public function sendRefreshRequest($refreshToken)
    {
        return ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjA3NzQ4OTgsImV4cCI6MTU2MDc3ODQ5OCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiYWxleEBtYWlsLnJ1In0.jBqzJ_BWOs7_nXFncCtM_PNiLVIgoZqaXtsa0bmUV5sHfUbZczuZxLsW6jsuSK4soLAP3qmlUoQOLJvD9xrgMXTxTtGWsCwQ7a8qcBoQVUqlWQtwhDBDWTKkt48GmodXLUe7iIs08st31E3C6ly6DjZZvLFvfBuh3G7kcHWGsKpPZLiuX8BCiMZbgXFzeBEEObXNaFZn6DTx6NYBTt5kB6CpTbraogO30T2sxkXt2n8g-7RhQjb2dAdFg5FQx6k75W07lXqFkHleVCSXgRAgEJ_8eA1WfkuNWf2NGJLUsfAZTjPNGFuIjWl3bFhimkG8CeihqyNhjKrxUPfgmnMRJMTGE37_WPq4emAkSmb0SoxvKsqi9VTxzpyrOv6bN6BIuk6pCpwPRA6nHMXC2TL0AyMPh0ZeOWnjX9hhUdaS0G_asXDLXw7lVWoRH3BTmDv8fYq8obVwG4M6ojvdtuqiexmbT0JSodP241QhvUNIIQS2DyQ3m2GL2nHLuFs2FDRjiE8b4vWeCpzNCXnjiluB_OWU3ar7BCdMV5M49sBWX51WAHX8x7QyqC5zuoVBM8Rd1k-nMe_5v2BMpx-BRYhoos5Kh9jyCDLL8OcQCNXG2TLJBuGYPBH2_On1iGWXcRQZWhOCA7SntovUyYf7Z3IgUXZ4R4Hd-XMdxq5tCxKCntk'];
    }

    public function getCurentUserBalance($token)
    {
        if (isset($token)) {
            return 1000;
        }
    }

    public function getCourses()
    {
        return json_decode('[{"code":"mern-stack-front-to-back-full-stack-react-redux-node-js","type":"rent","price":25.55},{"code":"build-a-blockchain-and-a-cryptocurrency-from-scratch","type":"buy","price":20.25},{"code":"java-programming-masterclass-for-software-developers","type":"free"},{"code":"new-course","type":"free"}]', true);
    }

    public function getCourseByCode($slug)
    {
        if ($slug == 'mern-stack-front-to-back-full-stack-react-redux-node-js') {
            return json_decode('[{"code":"mern-stack-front-to-back-full-stack-react-redux-node-js","type":"rent","price":25.55}]', true);
        } elseif ($slug == 'build-a-blockchain-and-a-cryptocurrency-from-scratch') {
            return json_decode('[{"code":"build-a-blockchain-and-a-cryptocurrency-from-scratch","type":"buy","price":20.25}]', true);
        } elseif ($slug == 'java-programming-masterclass-for-software-developers') {
            return json_decode('[{"code":"java-programming-masterclass-for-software-developers","type":"free"}]', true);
        }
    }

    public function buyCourse($slug, $token)
    {
        if (isset($token)) {
            if ($slug == 'mern-stack-front-to-back-full-stack-react-redux-node-js') {
                return json_decode('{"success":true,"course_type":"rent","exrires_at":"2019-06-24T12:56:54+00:00"}', true);
            } elseif ($slug == 'build-a-blockchain-and-a-cryptocurrency-from-scratch') {
                return json_decode('{"success":true,"course_type":"buy","exrires_at":"2019-06-24T12:55:45+00:00"}', true);
            } elseif ($slug == 'java-programming-masterclass-for-software-developers') {
                return json_decode('{"success":true,"course_type":"free","exrires_at":"2019-06-24T12:57:28+00:00"}', true);
            }
        }
    }

    public function getPaymentTransactions($token)
    {
        if (isset($token)) {
            return json_decode('[{"id":38,"created_at":"2019-06-17T16:39:29+00:00","type":"payment","course_code":"mern-stack-front-to-back-full-stack-react-redux-node-js","amount":25.55,"expires_at":"2019-06-24T16:39:29+00:00"},{"id":39,"created_at":"2019-06-17T16:39:50+00:00","type":"payment","course_code":"build-a-blockchain-and-a-cryptocurrency-from-scratch","amount":20.25,"expires_at":"2019-06-24T16:39:50+00:00"}]', true);
        }
    }

    public function getAllTransactions($token)
    {
        if (isset($token)) {
            return json_decode('[{"id":34,"created_at":"2019-06-17T12:34:58+00:00","type":"deposit","amount":1000.0,"expires_at":"2019-06-24T12:34:58+00:00"},{"id":38,"created_at":"2019-06-17T16:39:29+00:00","type":"payment","course_code":"mern-stack-front-to-back-full-stack-react-redux-node-js","amount":25.55,"expires_at":"2019-06-24T16:39:29+00:00"},{"id":39,"created_at":"2019-06-17T16:39:50+00:00","type":"payment","course_code":"build-a-blockchain-and-a-cryptocurrency-from-scratch","amount":20.25,"expires_at":"2019-06-24T16:39:50+00:00"}]', true);
        }
    }

    public function getTransactionByCode($slug, $token)
    {
        if (isset($token)) {
            if ($slug == 'mern-stack-front-to-back-full-stack-react-redux-node-js') {
                return json_decode('[{"id":38,"created_at":"2019-06-17T16:39:29+00:00","type":"payment","course_code":"mern-stack-front-to-back-full-stack-react-redux-node-js","amount":25.55,"expires_at":"2019-06-24T16:39:29+00:00"}]', true);
            } elseif ($slug == 'build-a-blockchain-and-a-cryptocurrency-from-scratch') {
                return json_decode('[{"id":39,"created_at":"2019-06-17T16:39:50+00:00","type":"payment","course_code":"build-a-blockchain-and-a-cryptocurrency-from-scratch","amount":20.25,"expires_at":"2019-06-24T16:39:50+00:00"}]', true);
            } elseif ($slug == 'java-programming-masterclass-for-software-developers') {
                return json_decode('[{"id":40,"created_at":"2019-06-17T16:53:50+00:00","type":"payment","course_code":"java-programming-masterclass-for-software-developers","amount":0.0,"expires_at":"2019-06-24T16:53:50+00:00"}]', true);
            }
        }
    }
}
