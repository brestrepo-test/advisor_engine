<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $symbol = (!empty($options['data']->getSymbol())) ? $options['data']->getSymbol()->getSymbol() : '';

        $builder
            ->add(
                'stock',
                null,
                [
                    'required' => true,
                    'mapped' => false,
                    'data' => $symbol,
                ]
            )
            ->add('date')
            ->add('operation', ChoiceType::class, array(
                'choices'  => array(
                    'Buy' => 'Buy',
                    'Sell' => 'Sell',
                ),
            ))
            ->add('amount');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Transaction',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_transaction';
    }
}
