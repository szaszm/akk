<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/16/17
 * Time: 11:17 PM
 */

namespace AppBundle\Controller;


use AKK\ModelBundle\Data\Pass;
use AKK\ModelBundle\Data\User;
use AKK\ModelBundle\Repository\PassRepository;
use AKK\ModelBundle\Repository\PassTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class PassController extends Controller
{
    /**
     * @Route("/buy-pass", name="buy_pass")
     * @param Request $request
     * @param PassTypeRepository $passTypeRepository
     * @param PassRepository $passRepository
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function buyPassAction(Request $request, PassTypeRepository $passTypeRepository, PassRepository $passRepository, Session $session)
    {
        if(!$session->has('user')) {
            throw $this->createAccessDeniedException();
        }
        /** @var User $user */
        $user = $session->get('user');


        $price = $request->query->get('price');
        $name = $request->query->get('name');

        $passType = $passTypeRepository->findOne(['name' => $name]);
        if($passType === null) {
            throw new \Exception("Unknown pass type '$name'");
        }

        if($request->query->has('validity_start')) {
            $validityStartDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $request->query->get('validity_start'));
        } else {
            $validityStartDate = new \DateTimeImmutable();
        }

        $pass = new Pass();
        $pass->user = $user;
        $pass->type = $passType;
        $pass->obtainDate = new \DateTimeImmutable();
        $pass->validityStartDate = $validityStartDate;

        $passRepository->persist($pass);

        if($pass->id !== null) {
            $session->getFlashBag()->set('success', 'Sikeres ' . lcfirst($passType->displayName) . ' vásárlás. Ár: ' . $price . ' HUF');
        } else {
            throw new \Exception('Sikertelen bérletvásárlás.');
        }

        return $this->render(':default:index.html.twig');
        //return $this->redirectToRoute('homepage');
    }

}