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

class ProjectFormController extends Controller
{
    /**
     * @Route("/project/form", name="projectForm")
     */
    public function indexAction(Request $request)
    {
        $form = $this->makeForm($request);
        
        if ($project = $this->handleForm($form, $request)){
            $this->addFlash("notice", "Saved project : " . $project->getId());
            return $this->redirectToRoute('homepage');
        }

        return $this->render('writer/projectForm.html.twig', array(
            'formProject' => $form->createView()
        ));
    }

    protected function makeForm($request){
        $formSceneBuilder = $this->createFormBuilder();

        $formSceneBuilder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, array('required' => false))
            //->add('variables', TextareaType::class, array('required' => false))
            ->add('save', SubmitType::class);

        return $formSceneBuilder->getForm();
    }

    protected function handleForm($form, $request){
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $form->isValid()) {
            return null;
        }

        $entityFactory = $this->get('entity_factory');

        $title = $form["title"]->getData();
        $description = $form["description"]->getData();
        //$variables = $form["variables"]->getData();
        
        $project = $entityFactory->makeProject($title);
        $project->setDescription($description);
        //$project->setVariables($variables);
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $project;
    }

}
