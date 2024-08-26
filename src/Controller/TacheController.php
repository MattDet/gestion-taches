<?php

// src/Controller/TacheController.php
namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class TacheController extends AbstractController
{
    #[Route('/taches', name: 'tache_index', methods: ['GET'])]
    public function index(TacheRepository $tacheRepository): Response
    {
        $taches = $tacheRepository->findAll();
        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
            'controller_name' => 'TacheController',
        ]);
    }

    #[Route('/taches/new', name: 'tache_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tache);
            $em->flush();

            return $this->redirectToRoute('tache_index');
        }

        return $this->render('tache/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/taches/{id}/edit', name: 'tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('tache_index');
        }

        return $this->render('tache/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/taches/{id}', name: 'tache_delete', methods: ['DELETE'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $request->request->get('_token');

        if ($csrfTokenManager->isTokenValid(new CsrfToken('delete' . $tache->getId(), $token))) {
            $em->remove($tache);
            $em->flush();
        }

        return $this->redirectToRoute('tache_index');
    }
}
