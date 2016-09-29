<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 29/09/2016
 * Time: 9:34 AM
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class ItemType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            -> add('title', TextType::class)
            -> add('description', TextType::class)
            -> add('code', TextType::class)
            -> add('collection', TextType::class)
            -> add('imageUrl', UrlType::class)
            -> add('submit', SubmitType::class)
            ;
    }
}


