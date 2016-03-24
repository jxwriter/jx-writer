<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ConnectionFormController extends BaseController
{
    /**
     * @Route("/connection/form/{parentSceneId}", name="connectionForm", defaults={"parentSceneId" = 0})
     */
    public function indexAction(Request $request, $parentSceneId)
    {
     
        $form = $this->makeForm($request, $parentSceneId);
        
        if ($connection = $this->handleForm($form, $request)){
            $this->addFlash("notice", "Saved connection : " . $connection->getId());
            return $this->redirectToRoute('previewProject', array('sceneId'=> $connection->getParentScene()->getId()));
        }

        return $this->render('writer/connectionForm.html.twig', array(
            'formConnection' => $form->createView()
        ));
    }

    protected function makeForm($request, $parentSceneId){

        $entityToEdit = $this->get('entity_factory')->makeEmptyConnection();
        $defaultParentScene = null;

        if ($request->query->get("editId")){
            $repo = $this->getDoctrine()->getRepository('AppBundle:Writer\SceneConnection');
            $entityToEdit = $repo->find($request->query->get("editId"));

            $defaultParentScene = $entityToEdit->getParentScene();
        } 

        $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
        $currentProject = $this->entityFromSession($request, 'currentProject');
        $sceneRepo->currentProject = $currentProject;

        
        if (!$defaultParentScene && $parentSceneId) {
            $defaultParentScene = $sceneRepo->find($parentSceneId);
        }

        $formSceneBuilder = $this->createFormBuilder($entityToEdit);

        

        $formSceneBuilder->add('parentScene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'data' => $defaultParentScene,
            'query_builder' => function ($er) {
                return $er->getSceneListQueryBuilder();
            },
            'choice_label' => function ($scene) {
                return $scene->getId() . " - " . $scene->getTitle();
            },
        ));

        $formSceneBuilder->add('childScene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'query_builder' => function ($er) {
                return $er->getSceneListQueryBuilder();
            },
            'choice_label' => function ($scene) {
                return $scene->getId() . " - " . $scene->getTitle();
            }
        ));

        $formSceneBuilder
            ->add('id', HiddenType::class, array('mapped' => false, 'data' => $entityToEdit->getId()))
            ->add('label', TextType::class, array('required' => false))
            ->add('pattern', TextType::class, array('required' => false))
            ->add('position', NumberType::class, array('data' => 0))
            ->add('conditions', TextareaType::class, array('required' => false))
            ->add('save', SubmitType::class);

        return $formSceneBuilder->getForm();

    }

    protected function handleForm($form, $request){
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $form->isValid()) {
            return null;
        }

        $entityFactory = $this->get('entity_factory');
        $id = $form["id"]->getData();
        
        $label = $form["label"]->getData();
        $pattern = $form["pattern"]->getData();
        $position = $form["position"]->getData();
        $conditions = $form["conditions"]->getData();

        $parentScene = $form["parentScene"]->getData();
        $childScene = $form["childScene"]->getData();

        $connection = $entityFactory->loadOrEmptyConnection($id);

        $connection->setParentScene($parentScene);
        $connection->setChildScene($childScene);
        $connection->setLabel($label);
        $connection->setPattern($pattern);
        $connection->setConditions($conditions);
        $connection->setPosition($position);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $connection;

    }
}
