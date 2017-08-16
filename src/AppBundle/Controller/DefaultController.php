<?php

namespace AppBundle\Controller;

use AKK\ModelBundle\Repository\PassTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/login", name="login_page", methods={"GET"})
     * @param Router $router
     * @return Response
     */
    public function loginPageAction(Router $router)
    {
        return $this->render('default/login_page.html.twig', [
            'action' => $router->generate('login')
        ]);
    }

    /**
     * @Route("/reg", name="reg_page", methods={"GET"})
     * @param Router $router
     * @return Response
     */
    public function regPageAction(Router $router)
    {
        return $this->render('default/reg_page.html.twig', [
            'action' => $router->generate('reg')
        ]);
    }

    /**
     * @Route("/buy-pass-page", name="buy_pass_page")
     * @param Router $router
     * @param PassTypeRepository $passTypeRepository
     * @return Response
     */
    public function buyPassPageAction(Router $router, PassTypeRepository $passTypeRepository)
    {
        return $this->render('default/buy_pass_page.html.twig', [
            'action' => $router->generate('buy_pass'),
            'passTypes' => $passTypeRepository->findAll()
        ]);
    }
}
