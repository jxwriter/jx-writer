<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaFormController extends Controller
{
    /**
     * @Route("/media/form/{sceneId}", name="mediaForm", defaults={"sceneId" = 0})
     */
    public function indexAction(Request $request)
    {
        $form = $this->makeForm();
        
        if ($media = $this->handleForm($form, $request)){
            $this->addFlash("notice", "Saved media : " . $media->getId());
            return $this->redirectToRoute('previewProject', array('sceneId'=> $media->getScene()->getId()));
        }

        return $this->render('writer/mediaForm.html.twig', array(
            'formMedia' => $form->createView()
        ));
    }

    protected function makeForm(){
        $formBuilder = $this->createFormBuilder();

        $formBuilder->add('project', EntityType::class, array(
            'class' => 'AppBundle:Writer\Project',
            'choice_label' => function ($project) {
                return $project->getId() . " - " . $project->getTitle();
            }
        ));

        $formBuilder->add('scene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'choice_label' => function ($scene) {
                return $scene->getId() . " - " . $scene->getTitle();
            },
        ));

        $formBuilder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, array('required' => false))
            ->add('format', TextType::class, array('required' => true, 'data'=>'text'))
            ->add('content', TextareaType::class, array('required' => true))
            ->add('conditions', TextareaType::class, array('required' => false))
            ->add('position', NumberType::class, array('required' => true, 'data' => 0))
            ->add('save', SubmitType::class);

        return $formBuilder->getForm();
    }

    protected function handleForm($form, $request){
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $form->isValid()) {
            return null;
        }

        $entityFactory = $this->get('entity_factory');

        $project = $form["project"]->getData();
        $scene = $form["scene"]->getData();
        $title = $form["title"]->getData();
        $description = $form["description"]->getData();
        $format = $form["format"]->getData();
        $content = $form["content"]->getData();
        $conditions = $form["conditions"]->getData();
        $position = $form["position"]->getData();
        
        $media = $entityFactory->makeMedia($content, $scene, $format);
        $media->setTitle($title);
        $media->setDescription($description);
        $media->setConditions($conditions);
        $media->setPosition($position);
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $media;
    }

}
