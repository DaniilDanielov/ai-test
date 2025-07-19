<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{

//    #[Route('/review/{episode}', name: 'create_episode', methods: ['POST'])]
//    public function new(Request $request): Response
//    {
//        $review = new Review();
//        $form = $this->createForm(ReviewType::class, $review);
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            // Сохранение отзыва в базу данных
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($review);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('review_success');
//        }
//
//        return $this->render('review/new.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
}
