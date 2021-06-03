<?php

namespace App\Listerner;

use ApiPlatform\Core\Api\IriConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;

class AuthenticationSuccessListerner{

    private $tokenTTL;

    private $secure = false;

    private $security;

    private $iriConverter;
    private $token;

    public function __construct($tokenTTL,IriConverterInterface $iriConverter, Security $security)
    {
        $this->security = $security;
        $this->tokenTTL = $tokenTTL;
        $this->iriConverter = $iriConverter;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event){
        $response = $event->getResponse();
        $data = $event->getData();

        $token = $data['token'];
        unset($data['token']);
        unset($data['refresh_token']);
        if (!empty($this->security->getUser()))
            $data['user_path'] = $this->iriConverter->getIriFromItem($this->security->getUser());
        $event->setData($data);

        $response->headers->setCookie(
            new Cookie('BEARER', $token,
                (new \DateTime())
                    ->add(new \DateInterval('PT'. $this->tokenTTL . 'S'))
                ,'/', null, $this->secure
            )
        );
    }

    private function getCurrentUser(){
        return $this->security->getUser();
    }

}