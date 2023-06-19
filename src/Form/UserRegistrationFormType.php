<?php

namespace App\Form;

use App\Form\Model\UserRegistrationFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationFormType extends AbstractType
{
    /** 
     * @param FormBuilderInterface $builder 
     * @param array $options 
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstName')
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Введенные пароли не совпадают',
                'options' => ['attr' => ['class' => 'form-control']],
                'required' => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationFormModel::class,
        ]);
    }
}
