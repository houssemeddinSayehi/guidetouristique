<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\TransportType;
use App\Repository\transportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
/**
 * @Route("/transport")
 */
class TransportController extends AbstractController
{
    /**
     * @Route("/", name="transport_index", methods={"GET"})
     */
    public function index(transportRepository $transportRepository): Response
    {
        return $this->render('transport/index.html.twig', [
            'transports' => $transportRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="transport_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transport = new Transport();
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['images']->getData();
            $file->move("img/", $file->getClientOriginalName());
            $transport->setImages("img/".$file->getClientOriginalName());
            $entityManager->persist($transport);
            $entityManager->flush();

            return $this->redirectToRoute('transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport/new.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="transport_show", methods={"GET"})
     */
    public function show(Transport $transport): Response
    {
        return $this->render('transport/show.html.twig', [
            'transport' => $transport,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="transport_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('transport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport/edit.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="transport_delete", methods={"POST"})
     */
    public function delete(Request $request, Transport $transport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transport_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/front", name="transport_indexfront", methods={"GET"})
     */
    public function indexfront(transportRepository $transportRepository): Response
    {
        return $this->render('transport/frontaffichetransport.html.twig', [
            'transports' => $transportRepository->findAll(),
        ]);
    }
    /**
     * @Route("/listTransport", name="Transport_imprime", methods={"GET"})
     */
    public function imprimer(transportRepository $transportRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $transport = $transportRepository ->findAll();


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Transport/pdf.html.twig', [
            'transports' => $transport,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("transport.pdf", [
            "Attachment" => true
        ]);
        return $this->render('Transport/pdf.html.twig', [
            'transports' => $transport,
        ]);

    }
}
