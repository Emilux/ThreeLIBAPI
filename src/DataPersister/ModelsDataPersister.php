<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Models;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class ModelsDataPersister implements ContextAwareDataPersisterInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;
    private $_slugger;
    private $_request;
    private $_security;

    public function __construct(
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        RequestStack $request,
        Security $security
    ) {
        $this->_entityManager = $entityManager;
        $this->_slugger = $slugger;
        $this->_request = $request->getCurrentRequest();
        $this->_security = $security;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Models;
    }

    /**
     * @param Models $data
     */
    public function persist($data, array $context = [])
    {
        $data->setSlug(
            $this
                ->_slugger
                ->slug(strtolower($data->getTitle())). '-' .uniqid()
        );

        // Set the author if it's a new article
        if ($this->_request->getMethod() === 'POST') {
            $data->setUser($this->_security->getUser());
        }

        $this->_entityManager->persist($data);
        $this->_entityManager->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        $this->_entityManager->remove($data);
        $this->_entityManager->flush();
    }
}