<?php
namespace CustomUser\Factory;

use CustomUser\Controller\CustomUserController;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomUserControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $objectManager      = $realServiceLocator->get(EntityManager::class);
        $userService        = $realServiceLocator->get('zfcuser_user_service');
        $forms = [
            'password'  => $realServiceLocator->get('zfcuser_change_password_form'),
            'email'     => $realServiceLocator->get('zfcuser_change_email_form'),
        ];

        return new CustomUserController($objectManager, $userService, $forms);
    }
}