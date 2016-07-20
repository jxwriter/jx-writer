<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaFormController extends BaseController
{
    /**
     * @Route("/media/form/{sceneId}", name="mediaForm", defaults={"sceneId" = 0})
     */
    public function indexAction(Request $request, $sceneId)
    {
        $form = $this->makeForm($request, $sceneId);
        
        if ($media = $this->handleForm($form, $request)){
            $this->addFlash("notice", "Saved media : " . $media->getId());
            return $this->redirectToRoute('previewProject', array('sceneId'=> $media->getScene()->getId()));
        }

        return $this->render('writer/mediaForm.html.twig', array(
            'formMedia' => $form->createView()
        ));
    }

    protected function makeForm($request, $sceneId){

        $entityToEdit = $this->get('entity_factory')->makeEmptyMedia();
        $defaultScene = null;

        if ($request->query->get("editId")){
            $repo = $this->getDoctrine()->getRepository('AppBundle:Writer\Media');
            $entityToEdit = $repo->find($request->query->get("editId"));
            $defaultScene = $entityToEdit->getScene();
        } 

        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
        $currentProject = $this->entityFromSession($request, 'currentProject');
        $sceneRepo->currentProject = $currentProject;

        if (! $defaultScene && $sceneId) {
            $defaultScene = $sceneRepo->find($sceneId);
        }

        $formBuilder = $this->createFormBuilder($entityToEdit);

        $formBuilder->add('scene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'data' => $defaultScene,
            'query_builder' => function ($er) {
                return $er->getSceneListQueryBuilder();
            },

            'choice_label' => function ($scene) {
                return $scene->getId() . " - " . $scene->getTitle();
            },
        ));

        $formBuilder
            ->add('id', HiddenType::class, array('mapped' => false, 'data' => $entityToEdit->getId()))
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, array('required' => false))
            ->add('format', TextType::class, array('required' => true, 'data'=>'text'))
            ->add('content', TextareaType::class, array('required' => true))
            ->add('conditions', TextareaType::class, array('required' => false))
            ->add('position', NumberType::class, array('required' => true, 'data' => 0))
            ->add('data', TextareaType::class, array('required' => false))
            ->add('save', SubmitType::class);

        return $formBuilder->getForm();
    }

    protected function handleForm($form, $request){
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $form->isValid()) {
            return null;
        }

        $entityFactory = $this->get('entity_factory');
        $id = $form["id"]->getData();
        $scene = $form["scene"]->getData();
        $title = $form["title"]->getData();
        $description = $form["description"]->getData();
        $format = $form["format"]->getData();
        $content = $form["content"]->getData();
        $conditions = $form["conditions"]->getData();
        $position = $form["position"]->getData();
        
        $media = $entityFactory->loadOrEmptyMedia($id);
        $media->setContent($content);
        $media->setFormat($format);
        $media->setScene($scene);
        $media->setTitle($title);
        $media->setDescription($description);
        $media->setConditions($conditions);
        $media->setPosition($position);
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $media;
    }

}
