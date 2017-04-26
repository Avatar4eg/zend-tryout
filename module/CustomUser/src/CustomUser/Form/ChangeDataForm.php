<?php
namespace CustomUser\Form;

use CustomUser\Entity\User;
use Zend\Form\Form;
use Zend\InputFilter;
use Zend\Form\Element;

class ChangeDataForm extends Form
{
    public function __construct($name = null, User $user = null)
    {
        parent::__construct($name);
        $this->addElements($user);
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements(User $user = null)
    {
        $first_name = new Element\Text('first_name');
        $first_name->setLabel('First name');
        if ($user !== null) {
            $first_name->setValue($user->getFirstName());
        }

        $last_name = new Element\Text('last_name');
        $last_name->setLabel('Last name');
        if ($user !== null) {
            $last_name->setValue($user->getLastName());
        }

        $identity = new Element\Hidden('identity');

        $credential = new Element\Password('credential');
        $credential->setLabel('Password');
        $credential->setAttribute('type', 'password');


        $submit = new Element\Submit('submit');
        $submit->setValue('Submit');
        $submit->setAttribute('value', 'Submit');
        $submit->setAttribute('type', 'submit');

        $this->add($first_name)
            ->add($last_name)
            ->add($identity)
            ->add($credential)
            ->add($submit);
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $inputFilter->add([
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
        ]);
        $inputFilter->add([
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
        ]);

        return $inputFilter;
    }
}
