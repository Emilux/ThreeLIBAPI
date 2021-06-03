<?php


namespace App\Listerner;


use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

class RefreshedTokenListener implements EventSubscriberInterface
{
    private $TTL;

    private $secure = false;

    public function __construct($TTL)
    {
        $this->TTL = $TTL;
    }

    public function setRefreshToken(AuthenticationSuccessEvent $event){
        $response = $event->getResponse();
        $data = $event->getData();
        $refreshToken = $data['refresh_token'];
        $event->setData($data);


        if ($refreshToken){
            $response->headers->setCookie(
                new Cookie('REFRESH_TOKEN', $refreshToken,
                    (new \DateTime())
                        ->add(new \DateInterval('PT'. $this->TTL . 'S'))
                    ,'/', null, $this->secure
                )
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => [
                ['setRefreshToken']
            ]
        ];
    }
}