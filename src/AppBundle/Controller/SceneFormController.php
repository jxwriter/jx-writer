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
     * @Route("/scene/form/{sceneId}", name="sceneForm")
     */
    public function indexAction(Request $request, $sceneId)
    {

		$scene = new Scene();

        $formSceneBuilder = $this->createFormBuilder($scene);

		$formSceneBuilder->add('project', EntityType::class, array(
    		'class' => 'AppBundle:Writer\Project',
    		'choice_label' => function ($project) {
		        return $project->getTitle();
		    }
		));

		$formSceneBuilder
			->add('title', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Scene'));

    
        return $this->render('writer/sceneForm.html.twig', array(
            'formScene' => $formSceneBuilder->getForm()->createView()
        ));
    }
}
