<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/16/17
 * Time: 9:01 PM
 */

namespace AppBundle\Controller;


use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Environment;

class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController implements ContainerAwareInterface
{
    /** @var  ContainerInterface */
    private $container;

    /**
     * ExceptionController constructor.
     * @param Environment $twig
     * @param $debug
     */
    public function __construct(Environment $twig, $debug)
    {
        parent::__construct($twig, $debug);
    }

    /**
     * @param Request $request
     * @param FlattenException $exception
     * @param DebugLoggerInterface|null $debugLogger
     * @return RedirectResponse
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $debugLogger = null)
    {
        $this->container->get('session')->getFlashBag()->set('error', $exception->getMessage());
        //return new Response($exception->getMessage());
        return new RedirectResponse($this->container->get('router')->generate('homepage'));
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        if ($container !== null) {
            $this->container = $container;
        }
    }
}