<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

//Контроллер отвечает только за работу с Request/Response +- по паттерну Grasp (Controller)
//Всю стороннюю логику по возможности выносим в Service Layer
class MainController extends AbstractController
{
    // todo прикрутить проброс параметра
    #[Route('/', name: 'app_home')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('episodes_index');
    }
}
