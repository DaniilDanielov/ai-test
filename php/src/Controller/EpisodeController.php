<?php
namespace App\Controller;

use App\Dto\CreateReviewDto;
use App\Form\ReviewType;
use App\Repository\Contracts\EpisodeRepositoryInterface;
use App\Services\ReviewProcessor;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Контроллер отвечает только за работу с Request/Response +- по паттерну Grasp (Controller)
//Всю стороннюю логику по возможности выносим в Service Layer
class EpisodeController extends AbstractController
{
    public function __construct(
        private readonly EpisodeRepositoryInterface $episodeRepository,
        private readonly ReviewProcessor $reviewProcessor,
    ) {
    }

    #[Route('/episodes', name: 'episodes_index')]
    public function index(
        Request $request
    ): Response {
        $page = $request->get('page', 1);
        $data = $this->episodeRepository->findAllEpisodesByPage($page);

        return $this->render('episode/index.html.twig', [
            'episodes' => $data['episodes'],
            'pagination' => $data['pagination']
        ]);
    }

    #[Route('/episode/{id}', name: 'episode_show', methods: ['GET'])]
    public function show(int $id): Response {
        $episodeData = $this->episodeRepository->getEpisodeData($id);

        if (! $episodeData) {
            throw $this->createNotFoundException('Эпизод не найден');
        }

        $route = 'create_review';
        $routeParams = [
            'id' => $episodeData->getEpisode()->getId(),
        ];

        $form = $this->createForm(type: ReviewType::class,options: [
            'action' => $this->generateUrl($route, $routeParams),
            'method' => 'POST',
        ]);

        return $this->render('episode/show.html.twig', [
            'episode' => $episodeData->getEpisode(),
            'averageRating' => $episodeData->getAverageRating(),
            'latestReviews' => $episodeData->getReviews(),
            'reviewForm' => $form->createView()
        ]);
    }

    #[Route('/episode/review/{id}', name: 'create_review', methods: ['POST'])]
    public function createReview(
        int $id,
        Request $request,
    ): Response {
        $requestData = $request->request->all();
        if (empty($requestData['review'])) {
            throw new InvalidArgumentException('Не переданы данные отзыва');
        }
        $createReviewDto = CreateReviewDto::createFromArray([
            'episodeId' => $id,
            'author'    => $requestData['review']['author'],
            'content'   => $requestData['review']['content'],
        ]);

        if ($this->reviewProcessor->createReview($createReviewDto)) {
            $this->addFlash('success', 'Отзыв успешно добавлен!');
        } else {
            $this->addFlash('error', 'Произошла ошибка при добавлении отзыва');
        }

        return $this->redirectToRoute('episode_show', ['id' => $id]);
    }


}
