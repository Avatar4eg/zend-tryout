<?php
namespace CustomUser\Listener;

use Application\Service\MailService;
use Application\Service\StringService;
use CustomUser\Entity\ConformationToken;
use CustomUser\Entity\Role;
use CustomUser\Entity\User;
use Doctrine\ORM\EntityManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;
use Zend\Math\Rand;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomUserListener extends AbstractListenerAggregate
{
    /** @var ServiceLocatorInterface $sm */
    protected $sm;

    public function attach(EventManagerInterface $events)
    {
        $sharedManager = $events->getSharedManager();
        $this->listeners[] = $sharedManager->attach(\ZfcUser\Form\Register::class,'init', [$this, 'modifyRegisterForm']);
        $this->listeners[] = $sharedManager->attach(\ZfcUser\Form\RegisterFilter::class,'init', [$this, 'modifyRegisterFormFilter']);
        $this->listeners[] = $sharedManager->attach(\ZfcUser\Service\User::class, 'register.post', [$this, 'onRegisterPost']);
        $this->listeners[] = $sharedManager->attach(\ScnSocialAuth\Authentication\Adapter\HybridAuth::class, 'register.post', array($this, 'onRegisterPost'));
        $this->listeners[] = $sharedManager->attach(\ScnSocialAuth\Authentication\Adapter\HybridAuth::class, 'registerViaProvider', array($this, 'onRegisterViaProvider'));
    }

    public function modifyRegisterForm(Event $e)
    {
        /* @var $form \ZfcUser\Form\Register */
        $form = $e->getTarget();
        $form->add([
            'name' => 'first_name',
            'type' => 'text',
            'options' => [
                'label' => 'First name',
            ],
        ]);
        $form->add([
            'name' => 'last_name',
            'type' => 'text',
            'options' => [
                'label' => 'Last name',
            ],
        ]);
    }

    /**
     * @param Event $e
     */
    public function modifyRegisterFormFilter(Event $e)
    {
        /* @var $form \ZfcUser\Form\RegisterFilter */
        $filter = $e->getTarget();
        $filter->add([
                'name'          => 'first_name',
                'required'      => true,
                'filters'       => [
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StringTrim::class],
                ],
                'validators'    => [
                    [
                        'name'      => \Zend\Validator\StringLength::class,
                        'options'   => [
                            'min'   => 1,
                            'max'   => 64,
                        ],
                    ],
                ],
            ]
        );
        $filter->add([
                'name'          => 'last_name',
                'required'      => true,
                'filters'       => [
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StringTrim::class],
                ],
                'validators'    => [
                    [
                        'name'      => \Zend\Validator\StringLength::class,
                        'options'   => [
                            'min'   => 1,
                            'max'   => 64,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @param Event $e
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Zend\Mail\Exception\InvalidArgumentException
     * @throws \Zend\Mime\Exception\InvalidArgumentException
     */
    public function onRegisterPost(Event $e)
    {
        $config = $this->getServiceManager()->get('config');
        if (!$config) {
            return;
        }

        /** @var User $user */
        $user = $e->getParam('user');
        if (!$user) {
            return;
        }

        if (array_key_exists('zfcuser', $config) && array_key_exists('default_role', $config['zfcuser'])) {
            /** @var EntityManager $objectManager */
            $objectManager = $this->getServiceManager()->get(EntityManager::class);
            /** @var Role $user_role */
            $user_role = $objectManager->getRepository(Role::class)->findOneBy([
                'roleId' => $config['zfcuser']['default_role']
            ]);
            if ($user_role) {
                $user->addRole($user_role);
                $objectManager->persist($user);
                $objectManager->flush();
            }
        }

        if ($user->getState() === false) {
            if (!array_key_exists('smtp_mail', $config)){
                return;
            }
            $mail_service = new MailService($config['smtp_mail']);
            if (!$mail_service) {
                return;
            }

            $url = StringService::siteURL() . '/user/activate?t=' . $user->getConformationToken()->getToken();
            $string = 'Confirm registration: <a href="' . $url . '">' . $url . '</a>';
            $mail_service->sendMail($user->getEmail(), $string);
        }
    }

    public function onRegisterViaProvider(Event $e)
    {
        /** @var User $user */
        $user = $e->getParam('user');
        $provider = $e->getParam('provider');
        $userProfile = $e->getParam('userProfile');

        if($userProfile->firstName !== null) {
            $user->setFirstName($userProfile->firstName);
        } else {
            $user->setFirstName($userProfile->email);
        }
        if ($userProfile->lastName !== null) {
            $user->setLastName($userProfile->lastName);
        } else {
            $user->setFirstName('from ' . $provider);
        }

        $bcrypt = new Bcrypt();
        if ($user->getConformationToken() !== null) {
            $code = $user->getConformationToken()->getToken();
        } else {
            $code = Rand::getString(ConformationToken::TOKEN_LENGTH, '0123456789abcdefghijklmnopqrstuvwxyz');
        }
        $user->setPassword($bcrypt->create($code));
        $user->setState(true);
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceManager()
    {
        return $this->sm;
    }

    /**
     * @param ServiceLocatorInterface $sm
     * @return CustomUserListener
     */
    public function setServiceManager($sm)
    {
        $this->sm = $sm;
        return $this;
    }
}