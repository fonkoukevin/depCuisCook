<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{

    public function __construct(private FormListenerFactory $listenerfactory)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'empty_data' => '',
                ]
            )
            ->add('slug',TextType::class,[
                'required' => false
//                'constraints' => new Sequentially(
//                    [
//                        new Length(min: 10),
//                        new Regex('/^[a-z0-9]+(?;-[a-z0-9]+)*$/',message: "Ceci n'est pas un slug valide")
//                    ]
//                )
            ])

            ->add('thumbnailFile',FileType::class,[
                'required' => false,
            ])

            ->add('category',CategoryAutocompleteField::class)
            ->add('content',
            TextareaType::class,[
                'empty_data' => '',
                ])
//            ->add('createdAt', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('updatedAt', null, [
//                'widget' => 'single_text',
//            ])
            ->add('duration')

            ->add('quantities', CollectionType::class, [
            'entry_type' => QuantityType::class,
                'allow_add' =>true,
                'allow_delete' =>true,
                'by_reference' => false,
                'entry_options'=>['label' => false],
                'attr'=>[
                    'data-controller' => 'form-collection',
                    'data-form-collection-add-label-value' => 'Ajouter un ingredient',
                    'data-form-collection-delete-label-value' => 'Supprimer un ingredient'
                ]
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerfactory->autoSlug('titre'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerfactory->timestamps())
        ;
    }

//    public function autoSlug(PreSubmitEvent $event):void
//    {
//
//        $data = $event->getData();
//        if (empty($data['slug'])) {
//            $slugger = new AsciiSlugger();
//            $data['slug'] = strtolower($slugger->slug($data['titre']));
//            $event->setData($data);
//        }
//    }

//    public  function attachTimesTamps(PostSubmitEvent $event):void{
////            dd($event->getData());
//
//        $data = $event->getData();
//        if(!($data instanceof Recipe)){
//            return;
//        }
//        $data->setUpdatedAt(new \DateTimeImmutable());
//
//        if(!$data->getId()){
//            $data->setCreatedAt(new \DateTimeImmutable());
//        }
//    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
