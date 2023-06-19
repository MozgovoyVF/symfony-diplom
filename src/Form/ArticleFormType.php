<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;

class ArticleFormType extends AbstractType
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /** 
     * @param UserRepository $userRepository 
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /** 
     * @param FormBuilderInterface $builder 
     * @param array $options 
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $imageConstrains = [
            new Image([
                'maxSize' => '2M',
                'maxSizeMessage' => 'Размер изображение не должен превышать 2 Мб',
                'minWidth' => 480,
                'minWidthMessage' => 'Минимальная ширина 480 px',
                'minHeight' => 300,
                'minHeight' => 'Минимальная высота 300 px',
                'mimeTypes' => [
                    "image/jpeg",
                    "image/jpg",
                    "image/png",
                ],
                'mimeTypesMessage' => 'Допустимые форматы изображений : jpeg, jpg, png',
                'allowPortrait' => false,
                'allowPortraitMessage' => 'Изображение должно быть горизонтальным'
            ]),
        ];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Название статьи',
                'constraints' => [
                    new Regex(
                        [
                            'pattern' => '/[a-zA-Zа-яА-Я]+/ui',
                            'message' => 'Поле должно состоять только из букв'
                        ]
                    ),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание статьи',
                'attr' => array('rows' => '3'),
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Содержимое статьи',
                'attr' => array('rows' => '10'),
            ])
            ->add('keywords', TextType::class, [
                'label' => 'Ключевые слова статьи',
            ])
            ->add('publishedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFirstName();
                },
                'choices' => $this->userRepository->findAllSortedByName(),
                'label' => 'Автор статьи',
                'placeholder' => 'Выберите автора статьи'
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstrains
            ]);

        $builder
            ->get('keywords')
            ->addModelTransformer(new CallbackTransformer(
                function ($keywordsAsArray) {
                    return implode(', ', $keywordsAsArray);
                },
                function ($keywordsAsString) {
                    return explode(', ', $keywordsAsString);
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
