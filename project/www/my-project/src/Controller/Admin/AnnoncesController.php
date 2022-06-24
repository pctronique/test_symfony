<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AnnoncesRepository;
use App\Entity\Annonces;
use App\Form\AnnoncesType;

/**
 * @Route("/admin/annonces", name="admin_annonces_")
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $annoncesRepo): Response
    {
        return $this->render('admin/annonces/index.html.twig', [
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
        return $this->redirectToRoute('admin_annonces_home');
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
    public function ajoutAnnonce(Request $request): Response
    {
        $annonces = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonces);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $annonces->setUsers($this->getUser());
            $annonces->setActive(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonces);
            $em->flush();

            return $this->redirectToRoute('admin_annonces_home');
        }

        return $this->render('admin/annonces/ajout.html.twig', [
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

            return $this->redirectToRoute('admin_annonces_home');
        }

        return $this->render('admin/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
