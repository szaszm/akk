<?php

namespace AppBundle\Controller;

use AKK\ModelBundle\Data\Pass;
use AKK\ModelBundle\Data\User;
use AKK\ModelBundle\Repository\Detail\RepositoryImpl;
use AKK\ModelBundle\Repository\PassRepository;
use AKK\ModelBundle\Repository\PassTypeRepository;
use AKK\ModelBundle\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param PassTypeRepository $passTypeRepository
     * @return Response
     */
    public function indexAction(PassTypeRepository $passTypeRepository)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'thing' => $passTypeRepository->findAll()
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Router $router
     * @return Response
     */
    public function loginAction(Request $request, UserRepository $userRepository, Router $router)
    {
        if(!$request->request->has('username') || !$request->request->has('password')) {
            throw $this->createAccessDeniedException('Invalid username or password.');
        }

        $user = $userRepository->findOne([
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password')
        ]);

        if($user === null) {
            throw $this->createAccessDeniedException('Invalid username or password.');
        }

        $this->get('session')->set('user', $user);

        $this->get('session')->getFlashBag()->set('success', 'Successfully logged in.');

        return new RedirectResponse($router->generate('homepage'));
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
     * @Route("/reg", name="reg", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Router $router
     * @return RedirectResponse
     * @throws \Exception
     */
    public function regAction(Request $request, UserRepository $userRepository, Router $router)
    {
        $requiredFields = ['username', 'password', 'first_name', 'last_name', 'email'];
        foreach ($requiredFields as $requiredField) {
            if(!$request->request->has($requiredField)) {
                throw new \Exception("Field '$requiredField' is required.");
            }
        }
        $fieldVals = $request->request->all();

        $conflictingUsers = $userRepository->find([['username' => $fieldVals['username'], 'email' => $fieldVals['email']]]);
        if(count($conflictingUsers) > 0) {
            throw new \Exception("User name or email is already used.");
        }

        $user = new User();
        $user->id = null;
        $user->username = $fieldVals['username'];
        $user->password = $fieldVals['password'];
        $user->email = $fieldVals['email'];
        $user->firstName = $fieldVals['first_name'];
        $user->lastName = $fieldVals['last_name'];
        $userRepository->persist($user);

        $this->get('session')->set('user', $user);
        $this->get('session')->getFlashBag()->set('success', 'Successfully registered.');

        return new RedirectResponse($router->generate('homepage'));
    }

    public function buyPassAction(Request $request, PassTypeRepository $passTypeRepository, PassRepository $passRepository)
    {
        $session = $this->get('session');
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
            $this->get('session')->getFlashBag()->set('success', 'Sikeres ' . lcfirst($passType->displayName) . ' vásárlás.');
        } else {
            throw new \Exception('Sikertelen bérletvásárlás.');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/profile/{userId}", name="profile", methods={"GET"})
     * @param int $userId
     * @param UserRepository $userRepository
     * @return Response
     */
    public function profileAction($userId, UserRepository $userRepository)
    {
        $user = $userRepository->getById($userId);

        return $this->render('default/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @param \Exception $exception
     * @return Response
     */
    public function exceptionAction(\Exception $exception)
    {
        return new Response($exception->getMessage());
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
