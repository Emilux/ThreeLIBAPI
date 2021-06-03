<?php


namespace App\Listerner;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListener
{
    public function onKernelRequest(RequestEvent $event){
        $request = $event->getRequest();
        $request->attributes->set('refresh_token', $request->cookies->get('REFRESH_TOKEN'));
    }
}