<?php

namespace App\Controller;

use App\Form\DocType;
use App\Entity\Documents;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocController extends AbstractController
{
    /**
     * @Route("/doc/new", name="doc-create")
     * @Route("/doc/{id}/edit", name="doc-edit")
     */
    public function form(ManagerRegistry $doctrine, Documents $document = null, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $manager = $doctrine->getManager();

        if (!$document) {
            $document = new Documents();
        }

        $form = $this->createForm(DocType::class, $document);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filename = $document->getDocName();

            \unlink('../public/documents/' . $filename);
            $document->setdocName(null);
            $manager->flush();
            $manager->persist($document);
            $manager->flush();
            return $this->redirectToRoute('doc');
        }

        return $this->render('doc/create.html.twig', [
            'formDoc' => $form->createView(),
            'active_tab' => "doc",
            'documents' => $document,
            'editMode' => $document->getId() !== null,
        ]);
    }


    /**
     * @Route("/doc", name="doc")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repo = $doctrine->getRepository(Documents::class);

        $documents = $repo->findAll();

        return $this->render('doc/doc.html.twig', [
            'documents' => $documents,
            'active_tab' => "doc"
        ]);
    }

    /**
     * @Route("/download/{file}", name="download")
     */
    public function download(ManagerRegistry $doctrine,  $file)
    {
        $repo = $doctrine->getRepository(Documents::class);
        switch ($file) {
            case "affichette":
                $documents = $repo->find(1);
                $filePath = '../public/documents/' . $documents->getDocName();
                break;
            case "programme":
                $documents = $repo->find(2);
                $filePath = '../public/documents/' . $documents->getDocName();
                break;
        }
        $response = new BinaryFileResponse($filePath);
        return $response;
    }
}
