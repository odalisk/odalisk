<?php
// src/Odalisk/Form/ContactType.php
namespace Odalisk\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name');
        $builder->add('email', 'email');
        $builder->add('subject');
        $builder->add('body', 'textarea');
        $builder->add('currentPage', 'hidden');
    }

    public function getName()
    {
        return 'contact';
    }
}
