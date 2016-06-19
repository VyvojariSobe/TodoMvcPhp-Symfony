<?php

namespace AppBundle\Form;

use AppBundle\Entity\ToDo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToDoType extends AbstractType
{
    /**
     * @var null|ToDo
     */
    private $data = null;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                if ($event->getData() instanceof ToDo) {
                    $this->data = $event->getData();
                }

                $event->getForm()->add(
                    'value',
                    TextType::class,
                    [
                        'label' => false,
                        'attr'  => $this->data
                            ? ['class' => 'edit']
                            : [
                                'class'       => 'new-todo',
                                'placeholder' => 'What needs to be done?',
                                'autofocus'   => 'autofocus',
                            ],
                    ]
                );
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\ToDo',
            ]
        );
    }
}
