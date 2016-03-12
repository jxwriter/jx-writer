<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Writer\Media;
use AppBundle\Entity\Writer\Scene;
use AppBundle\Entity\Writer\SceneConnection;
use AppBundle\Entity\Writer\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ConnectionFormController extends Controller
{
    /**
     * @Route("/connection/form", name="connectionForm")
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

        $formSceneBuilder->add('parentScene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'choice_label' => function ($project) {
                return $project->getTitle();
            }
        ));

        $formSceneBuilder->add('childScene', EntityType::class, array(
            'class' => 'AppBundle:Writer\Scene',
            'choice_label' => function ($project) {
                return $project->getTitle();
            }
        ));

		$formSceneBuilder
			->add('label', TextType::class, array('required' => false))
            ->add('pattern', TextType::class, array('required' => false))
            ->add('position', NumberType::class, array('data' => 0))
            ->add('save', SubmitType::class, array('label' => 'Create Connection'));

        $form = $formSceneBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form["project"]->getData();
            $label = $form["label"]->getData();
            $pattern = $form["pattern"]->getData();

            $parentScene = $form["parentScene"]->getData();
            $childScene = $form["childScene"]->getData();

            $entityFactory->makeConnection($parentScene, $childScene, $label, $pattern);
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash("notice", "Saved connection : " . $parentScene->getId());
            return $this->redirectToRoute('previewProject', array('sceneId'=> $parentScene->getId()));
        }

        return $this->render('writer/connectionForm.html.twig', array(
            'formConnection' => $form->createView()
        ));
    }
}
