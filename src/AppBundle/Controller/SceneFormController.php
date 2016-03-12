<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        $entityFactory = $this->get('entity_factory');

        $formSceneBuilder = $this->createFormBuilder();

		$formSceneBuilder->add('project', EntityType::class, array(
    		'class' => 'AppBundle:Writer\Project',
    		'choice_label' => function ($project) {
		        return $project->getTitle();
		    }
		));

		$formSceneBuilder
			->add('title', TextType::class)
            ->add('text', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Scene'));

        $form = $formSceneBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form["project"]->getData();
            $title = $form["title"]->getData();
            $text = $form["text"]->getData();

            $scene = $entityFactory->makeScene($title, $project);
            $entityFactory->makeMediaText($text, $scene);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash("notice", "Saved scene : " . $scene->getId());
            return $this->redirectToRoute('previewProject', array('sceneId'=> $scene->getId()));
        }

        return $this->render('writer/sceneForm.html.twig', array(
            'formScene' => $form->createView()
        ));
    }

}
