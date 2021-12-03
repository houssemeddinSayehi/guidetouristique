<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\Transport1Type;
use App\Repository\transportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transportfrond")
 */
class TransportfrondController extends AbstractController
{
    /**
     * @Route("/", name="transportfrond_index", methods={"GET"})
     */
    public function index(transportRepository $transportRepository): Response
    {
        return $this->render('transportfrond/index.html.twig', [
            'transports' => $transportRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="transportfrond_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transport = new Transport();
        $form = $this->createForm(Transport1Type::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transport);
            $entityManager->flush();

            return $this->redirectToRoute('transportfrond_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transportfrond/new.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transportfrond_show", methods={"GET"})
     */
    public function show(Transport $transport): Response
    {
        return $this->render('transportfrond/show.html.twig', [
            'transport' => $transport,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transportfrond_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Transport1Type::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('transportfrond_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transportfrond/edit.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transportfrond_delete", methods={"POST"})
     */
    public function delete(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transportfrond_index', [], Response::HTTP_SEE_OTHER);
    }
}
