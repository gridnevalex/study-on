<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Security\StudyOnUser;
use App\Service\BillingClient;

class UserProvider implements UserProviderInterface
{
    private $billingClient;

    public function __construct(BillingClient $billingClient)
    {
        $this->billingClient = $billingClient;
    }
    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $user = new StudyOnUser();
        $user->setEmail($username);
        return $user;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof StudyOnUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $expTime = $this->billingClient->decodePayload($user->getApiToken())->exp;
        $currentDate = ((new \DateTime())->modify('+1 hour'))->getTimestamp();

        if ($currentDate > $expTime) {
            $response = $this->billingClient->sendRefreshRequest($user->getRefreshToken());
            $user->setApiToken($response['token']);
            //$user->setApiToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjEzODAwMDQsImV4cCI6MTU2MTM4MDAzNCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoic2ltcGxlVXNlckBnbWFpbC5jb20ifQ.XF2CurGJ6dq6T8pKFfz-WFMzWsDmoHSHMLK3QzDepvla29-dzXsy0U5J_yINdRItMDv5rsdVH4B0e1Qs19-MteWMJBCsUkj4Hh5Ca70sQl5EN1Eu6ceGGte6Jzw9OK7yCSu1I917qklxcTZ3ZmAL3a6-UQK5nDN5LKpHGJ82oZ4kZfJSM_fNjVpSpHgT7yRELXH6P9NDok2ITdwbOVE8bZqDYytFcE7_FBRsE5AckE1dWG7Zn-QKS5uEMekxwa50fZRWqnxDl8uJQBI3EC6r3xdZvFniKDJ4oD3oVCb1cTWvurrj86B786l2-uLk5TY-BjBK_YpcQhcj4Q6AYd0ksdKCJSrhZ5HtoHjG3crGombImZKjxI889cDDbM3xOYZp8PHYo7-uhYbsSZqGffSAye940qONwZXDlMoiPQ6yztpWRWKfHtEu_G8Wb50Behni_WU2zQcBiH19ZeaPLue80prVCMgRnbowsjk9hj45jouLtptSDmxhp6LKgF20bNmwBVHhKzKU-IDmf3QfQ_EoB5R-PrdCitUPNuAie_4SrMJvrFOfVLJWjLFGemp84X3d_vJALGgFHjElklUbT9zIDFqgRKGnQ_YsfbTCSqIijiE0LoDOR7v331O3GFk-y5CNJBBQcQCHHiCONwDTN4ofRaptryz8qtKnocITmfMjiv4');
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return StudyOnUser::class === $class;
    }
}
