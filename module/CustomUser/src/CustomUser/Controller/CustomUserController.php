<?php
namespace CustomUser\Controller;

use Application\Service\StringService;
use CustomUser\Entity\ConformationToken;
use CustomUser\Entity\User;
use CustomUser\Form\ChangeDataForm;
use Doctrine\ORM\EntityManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Http\Response;
use ZfcUser\Controller\UserController;
use ZfcUser\Form\ChangeEmail;
use ZfcUser\Form\ChangePassword;
use ZfcUser\Service\User as UserService;

class CustomUserController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var UserService $userService
     */
    protected $userService;

    /**
     * @var FormInterface
     */
    protected $changePasswordForm;

    /**
     * @var FormInterface
     */
    protected $changeEmailForm;

    public function __construct(EntityManager $objectManager, UserService $userService, array $forms)
    {
        $this->setObjectManager($objectManager)
            ->setUserService($userService)
            ->setChangePasswordForm($forms['password'])
            ->setChangeEmailForm($forms['email']);
    }

    public function indexAction()
    {
        $viewModel = new ViewModel([
            'password_form' => $this->getChangePasswordForm(),
            'email_form'    => $this->getChangeEmailForm(),
            'data_form'     => new ChangeDataForm(null, $this->zfcUserAuthentication()->getIdentity()),
        ]);
        $viewModel->setTemplate('zfc-user/user/index');
        return $viewModel;
    }

    public function activateAction()
    {
        $token = $this->params()->fromQuery('t', false);
        if ($token === false) {
            return $this->notFoundAction();
        }

        /** @var ConformationToken $token_object */
        $token_object = $this->getObjectManager()->getRepository(ConformationToken::class)->findOneBy([
            'token' => StringService::clearString($token, ConformationToken::TOKEN_LENGTH),
        ]);
        if (!$token_object) {
            return $this->notFoundAction();
        }

        if ($token_object->getUser() === null || $token_object->getValidTill() < new \DateTime('now')) {
            $this->getObjectManager()->remove($token_object);
            $this->getObjectManager()->flush();
            return $this->notFoundAction();
        }

        $user = $token_object->getUser();
        $user->setState(true);
        $this->getObjectManager()->persist($user);
        $this->getObjectManager()->remove($token_object);
        $this->getObjectManager()->flush();

        return new ViewModel([
            'success' => true,
        ]);
    }

    public function changeEmailAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Response $response */
        $response = $this->getResponse();

        if (!$request->isPost()) {
            return $this->notFoundAction();
        }

        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(UserController::ROUTE_LOGIN);
        }

        $data = $request->getPost()->toArray();
        /** @var ChangeEmail $form */
        $form = $this->getChangeEmailForm();
        $form->setData($data);

        if (!$form->isValid()) {
            return $response->setContent(Json::encode([
                'success' => false,
                'messages' => $form->getMessages() ?: []
            ]));
        }

        if ($this->getUserService()->changeEmail($form->getData()) !== true) {
            $this->flashMessenger()->setNamespace('change-email')->addMessage(false);
            return $response->setContent(Json::encode([
                'success' => false
            ]));
        }

        $this->flashMessenger()->setNamespace('change-email')->addMessage(true);
        return $response->setContent(Json::encode([
            'success'   => true
        ]));
    }

    public function changePasswordAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Response $response */
        $response = $this->getResponse();

        if (!$request->isPost()) {
            return $this->notFoundAction();
        }

        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(UserController::ROUTE_LOGIN);
        }

        $data = $request->getPost()->toArray();
        /** @var ChangePassword $form */
        $form = $this->getChangePasswordForm();
        $form->setData($data);

        if (!$form->isValid()) {
            return $response->setContent(Json::encode([
                'success' => false,
                'messages' => $form->getMessages() ?: []
            ]));
        }

        if ($this->getUserService()->changePassword($form->getData()) !== true) {
            $this->flashMessenger()->setNamespace('change-password')->addMessage(false);
            return $response->setContent(Json::encode([
                'success' => false
            ]));
        }

        $this->flashMessenger()->setNamespace('change-password')->addMessage(true);
        return $response->setContent(Json::encode([
            'success'   => true
        ]));
    }

    public function changeDataAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Response $response */
        $response = $this->getResponse();

        if (!$request->isPost()) {
            return $this->notFoundAction();
        }

        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(UserController::ROUTE_LOGIN);
        }

        $data = $request->getPost()->toArray();
        /** @var ChangePassword $form */
        $form = new ChangeDataForm();
        $form->setData($data);

        if (!$form->isValid()) {
            return $response->setContent(Json::encode([
                'success' => false,
                'messages' => $form->getMessages() ?: []
            ]));
        }

        $validatedData = $form->getData();
        /** @var User $user */
        $user = $this->zfcUserAuthentication()->getIdentity();
        $bcrypt = new Bcrypt();
        if (!$user || !($user instanceof User) || !$user->getState() || !$bcrypt->verify((string)$validatedData['credential'], $user->getPassword())) {
            $this->flashMessenger()->setNamespace('change-data')->addMessage(false);
            return $response->setContent(Json::encode([
                'success' => false
            ]));
        }

        $user->setFirstName($validatedData['first_name'])
            ->setLastName($validatedData['last_name']);
        $this->objectManager->persist($user);
        $this->objectManager->flush();

        $this->flashMessenger()->setNamespace('change-data')->addMessage(true);
        return $response->setContent(Json::encode([
            'success'   => true
        ]));
    }

    /**
     * @return EntityManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param EntityManager $objectManager
     * @return CustomUserController
     */
    public function setObjectManager($objectManager)
    {
        $this->objectManager = $objectManager;
        return $this;
    }

    /**
     * @return UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     * @param UserService $userService
     * @return CustomUserController
     */
    public function setUserService($userService)
    {
        $this->userService = $userService;
        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getChangePasswordForm()
    {
        return $this->changePasswordForm;
    }

    /**
     * @param FormInterface $changePasswordForm
     * @return CustomUserController
     */
    public function setChangePasswordForm($changePasswordForm)
    {
        $this->changePasswordForm = $changePasswordForm;
        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getChangeEmailForm()
    {
        return $this->changeEmailForm;
    }

    /**
     * @param FormInterface $changeEmailForm
     * @return CustomUserController
     */
    public function setChangeEmailForm($changeEmailForm)
    {
        $this->changeEmailForm = $changeEmailForm;
        return $this;
    }
}
