<?php

namespace App\Controller;

use App\Entity\Listing;
use App\Form\ListingType;
use App\Repository\ListingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/listing')]
class ListingController extends AbstractController
{
    #[Route('/', name: 'app_listing_index', methods: ['GET'])]
    public function index(ListingRepository $listingRepository): Response
    {
        return $this->render('listing/index.html.twig', [
            'listings' => $listingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_listing_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ListingRepository $listingRepository): Response
    {
        $listing = new Listing();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listingRepository->add($listing, true);

            return $this->redirectToRoute('app_listing_show', ['id' => $listing->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('listing/new.html.twig', [
            'listing' => $listing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_listing_show', methods: ['GET'])]
    public function show(Listing $listing): Response
    {
        $countTasks = count($listing->getTasks());

        return $this->render('listing/show.html.twig', [
            'listing' => $listing,
            'countTasks' => $countTasks
        ]);
    }

    #[Route('/{id}/edit', name: 'app_listing_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Listing $listing, ListingRepository $listingRepository): Response
    {
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listingRepository->add($listing, true);

            return $this->redirectToRoute('app_listing_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('listing/edit.html.twig', [
            'listing' => $listing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_listing_delete', methods: ['POST'])]
    public function delete(Request $request, Listing $listing, ListingRepository $listingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$listing->getId(), $request->request->get('_token'))) {
            $listingRepository->remove($listing, true);
        }

        return $this->redirectToRoute('app_listing_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-done-tasks/{id}', name: 'app_delete_all_done_tasks', methods: ['GET', 'POST'])]
    public function deleteAllDoneTaks(ManagerRegistry $doctrine, Request $request, ListingRepository $listingRepository): Response
    {

        $getListingId = $request->attributes->get('id', null);
        
        if(!$getListingId) {
            throw new \Exception('The id is missing.');
        }

        $listing = $listingRepository->find($getListingId);

        if(!$getListingId) {
            throw new \Exception('List not found.');
        }
        
        $entityManager = $doctrine->getManager();

        foreach($listing->getTasks() as $task) {
            if(!$task->isState()) {
                continue;
            }

            $listing->removeTask($task);
            $entityManager->persist($listing);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_listing_show', ['id' => $listing->getId()], Response::HTTP_SEE_OTHER);
    }
}
