<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function index()
    {
    	//if($this->isGranted('IS_AUTHENTICATED_FULLY'))
        	return $this->redirectToRoute('travel_index');

        return $this->redirectToRoute('app_login');
    }
  }