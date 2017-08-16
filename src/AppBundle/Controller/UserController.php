<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/16/17
 * Time: 11:13 PM
 */

namespace AppBundle\Controller;


use AKK\ModelBundle\Data\User;
use AKK\ModelBundle\Repository\PassRepository;
use AKK\ModelBundle\Repository\PassTypeRepository;
use AKK\ModelBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Session $session
     * @return RedirectResponse
     */
    public function loginAction(Request $request, UserRepository $userRepository, Session $session)
    {
        if(!$request->request->has('username') || !$request->request->has('password')) {
            $session->getFlashBag()->add('error', 'Hibás felhasználónév vagy jelszó.');
            return $this->redirectToRoute('login');
        }

        $user = $userRepository->findOne([
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password')
        ]);

        if($user === null) {
            $session->getFlashBag()->add('error', 'Hibás felhasználónév vagy jelszó.');
            return $this->redirectToRoute('login');
        }

        $session->set('user', $user);

        $session->getFlashBag()->set('success', 'Sikeres bejelentkezés.');

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/reg", name="reg", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Router $router
     * @param Session $session
     * @return RedirectResponse
     * @throws \Exception
     */
    public function regAction(Request $request, UserRepository $userRepository, Router $router, Session $session)
    {
        $requiredFields = ['username', 'password', 'first_name', 'last_name', 'email'];
        foreach ($requiredFields as $requiredField) {
            if(!$request->request->has($requiredField)) {
                throw new \Exception("A '$requiredField' mező kitöltése kötelező.");
            }
        }
        $fieldVals = $request->request->all();

        $byname = $userRepository->find(['username' => $fieldVals['username']]);
        if(count($byname) > 0) {
            throw new \Exception("Már foglalt a felhasználónév");
        }
        $byemail = $userRepository->find(['email' => $fieldVals['email']]);
        if(count($byemail) > 0) {
            throw new \Exception("Már foglalt a felhasználónév");
        }

        $user = new User();
        $user->id = null;
        $user->username = $fieldVals['username'];
        $user->password = $fieldVals['password'];
        $user->email = $fieldVals['email'];
        $user->firstName = $fieldVals['first_name'];
        $user->lastName = $fieldVals['last_name'];
        $userRepository->persist($user);

        $session->set('user', $user);
        $session->getFlashBag()->set('success', 'Sikeres regisztráció.');

        return new RedirectResponse($router->generate('homepage'));
    }

    /**
     * @Route("/profile/{userId}", name="profile", methods={"GET"})
     * @param int $userId
     * @param UserRepository $userRepository
     * @param PassRepository $passRepository
     * @return Response
     */
    public function profileAction($userId, UserRepository $userRepository, PassRepository $passRepository, PassTypeRepository $passTypeRepository)
    {
        $user = $userRepository->getById($userId);
        $passes = $passRepository->getByUser($user);
        foreach ($passes as $pass) {
            $passTypeRepository->load($pass->type);
        }

        return $this->render('default/profile.html.twig', [
            'user' => $user,
            'passes' => $passes
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @param Session $session
     * @return RedirectResponse
     */
    public function logoutAction(Session $session)
    {
        $session->remove('user');
        $session->getFlashBag()->set('success', 'Sikeres kijelentkezés.');
        return $this->redirectToRoute('homepage');
    }
}