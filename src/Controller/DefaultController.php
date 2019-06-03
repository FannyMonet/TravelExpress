<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function index()
    {
    	if($this->isGranted('IS_AUTHENTICATED_FULLY'))
        	return $this->render('base.html.twig');

        return $this->redirectToRoute('app_login');
    }
  }