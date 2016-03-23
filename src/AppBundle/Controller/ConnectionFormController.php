<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ConnectionFormController extends Controller
{
    /**
     * @Route("/connection/form/{parentSceneId}", name="connectionForm", defaults={"parentSceneId" = 0})
     */
    public function indexAction(Request $request, $parentSceneId)
    {
     
        $form = $this->makeSceneForm($parentSceneId);
        
        if ($connection = $this->handleForm($form, $request)){
            $this->addFlash("notice", "Saved connection : " . $connection->getId());
            return $this->redirectToRoute('previewProject', array('sceneId'=> $connection->getParentScene()->getId()));
        }

        return $this->render('writer/connectionForm.html.twig', array(
            'formConnection' => $form->createView()
        ));
    }

    protected function makeSceneForm($parentSceneId){

        $defaultParentScene = null;
        if ($parentSceneId) {
            $sceneRepo = $this->getDoctrine()->getRepository('AppBundle:Writer\Scene');
            $defaultParentScene = $sceneRepo->find($parentSceneId);
        }

        $formSceneBuilder = $this->createFormBuilder();

        $formSceneBuilder->add('project', EntityType::class, array(
            'class' => 'AppBundle:Writer\Project',
            'choice_label' => function ($project) {
                return $project->getId() . " - " . $project->getTitle();
            }
        ));

        $formSceneBuilder->add('parentScene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'data' => $defaultParentScene,
            'choice_label' => function ($scene) {
                return $scene->getId() . " - " . $scene->getTitle();
            },
        ));

        $formSceneBuilder->add('childScene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'choice_label' => function ($scene) {
                return $scene->getId() . " - " . $scene->getTitle();
            }
        ));

        $formSceneBuilder
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
        $project = $form["project"]->getData();
        $label = $form["label"]->getData();
        $pattern = $form["pattern"]->getData();
        $position = $form["position"]->getData();
        $conditions = $form["conditions"]->getData();

        $parentScene = $form["parentScene"]->getData();
        $childScene = $form["childScene"]->getData();

        $connection = $entityFactory->makeConnection($parentScene, $childScene, $label, $pattern);
        $connection->setConditions($conditions);
        $connection->setPosition($position);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $connection;

    }
}
