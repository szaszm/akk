<?php

namespace AppBundle\Controller;

use AKK\ModelBundle\Repository\Detail\RepositoryImpl;
use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, Connection $connection)
    {
        // replace this example code with whatever you need
        $repo = new RepositoryImpl($connection);
        $repo->select('sqlite_master');
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'thing' => $connection->fetchAll('SELECT name FROM sqlite_master')
        ]);
    }
}
