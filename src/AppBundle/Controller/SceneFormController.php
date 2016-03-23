<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SceneFormController extends Controller
{
    /**
     * @Route("/scene/form", name="sceneForm")
     */
    public function indexAction(Request $request)
    {
        $form = $this->makeSceneForm();
        
        if ($scene = $this->handleForm($form, $request)){
            $this->addFlash("notice", "Saved scene : " . $scene->getId());
            return $this->redirectToRoute('previewProject', array('sceneId'=> $scene->getId()));
        }

        return $this->render('writer/sceneForm.html.twig', array(
            'formScene' => $form->createView()
        ));
    }

    protected function makeSceneForm(){
        $formSceneBuilder = $this->createFormBuilder();

        $formSceneBuilder->add('project', EntityType::class, array(
            'class' => 'AppBundle:Writer\Project',
            'choice_label' => function ($project) {
                return $project->getTitle();
            }
        ));

        $formSceneBuilder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, array('required' => false))
            ->add('conditions', TextareaType::class, array('required' => false))
            ->add('actions', TextareaType::class, array('required' => false))
            ->add('data', TextType::class, array('required' => false))
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
        $title = $form["title"]->getData();
        $description = $form["description"]->getData();
        $conditions = $form["conditions"]->getData();
        $actions = $form["actions"]->getData();
        $data = $form["data"]->getData();
        
        $scene = $entityFactory->makeScene($title, $project);
        $scene->setDescription($description);
        $scene->setConditions($conditions);
        $scene->setActions($actions);
        $scene->setData($data);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $scene;
    }

}
