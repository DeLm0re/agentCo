<?php

namespace App\Form\Type;

use App\Entity\Agent;
use App\Entity\Transaction;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransactionType.
 */
class TransactionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('principalAgent', EntityType::class, [
                'class' => Agent::class,
                'choice_label' => function (Agent $agent) {
                    return ucfirst($agent->getName()).' '.mb_strtoupper($agent->getLastname());
                },
            ])
            ->add('associateAgent', EntityType::class, [
                'class' => Agent::class,
                'choice_label' => function (Agent $agent) {
                    return ucfirst($agent->getName()).' '.mb_strtoupper($agent->getLastname());
                },
            ])
            ->add('amount', NumberType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
