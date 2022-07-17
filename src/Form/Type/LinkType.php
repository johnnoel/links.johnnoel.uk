<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\LinkModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    /**
     * @param array<string,mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('url', UrlType::class, [
            'required' => true,
            'trim' => true,
        ])->add('isPublic', CheckboxType::class, [
            'required' => false,
        ])->add('categories', TextType::class, [
            'required' => false,
            'trim' => true,
        ])->add('tags', TextType::class, [
            'required' => false,
            'trim' => true,
        ]);

        $csvTransformer = new CallbackTransformer(
            fn (?array $v): string => implode(', ', $v ?? []),
            fn (?string $v): array => array_map('trim', explode(',', strval($v))),
        );
        $builder->get('categories')->addModelTransformer($csvTransformer);
        $builder->get('tags')->addModelTransformer($csvTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LinkModel::class,
        ]);
    }
}
