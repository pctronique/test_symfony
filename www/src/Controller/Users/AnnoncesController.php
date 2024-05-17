<?php

namespace App\Controller\Users;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AnnoncesRepository;
use App\Service\ManagePicturesService;
use App\Entity\Annonces;
use App\Form\AnnoncesType;

/**
 * @Route("/users/annonces", name="users_annonces_")
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $annoncesRepo): Response
    {
        return $this->render('users/annonces/index.html.twig', [
            'controller_name' => 'AnnoncesController',
            'annonces' => $annoncesRepo->findAll()
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer(Annonces $annonces): Response
    {
        $annonces->setActive(!$annonces->isActive());

        $em = $this->getDoctrine()->getManager();
        $em->remove($annonces);
        $em->flush();

        $this->addFlash('message', 'Annonce supprimée avec succès');
        return $this->redirectToRoute('users_annonces_home');
    }

    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Annonces $annonces): Response
    {
        $annonces->setActive(!$annonces->isActive());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonces);
        $em->flush();

        return new Response("true");
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajout(Request $request, ManagePicturesService $picturesService): Response
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            // On récupère les images transmises
            $images = $form->get('images')->getData();
                
            // On ajoute les images
            $picturesService->add($images, $annonce);

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users_annonces_home');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierAnnonce(Annonces $annonces, Request $request): Response
    {

        $form = $this->createForm(AnnoncesType::class, $annonces);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($annonces);
            $em->flush();

            return $this->redirectToRoute('users_annonces_home');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
