<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Produit;
use App\Form\FormationType;



final class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_connexion');
    }

    #[Route('/admin/dashboard', name: 'app_dashboard_admin')]
    public function goToAdmin(ManagerRegistry $doctrine, Session $session, Request $request){
        $employe = $session -> get('employe');
        if($employe){
            $allInscriptions = $doctrine -> getRepository(Inscription::class) -> findAll();
            $formFormation = $this -> createForm(FormationType::class);
            $formFormation -> handleRequest($request);
            if($formFormation -> isSubmitted() && $formFormation -> isValid()){
                $formationData = $formFormation -> getData();
                $doctrine -> getManager() -> persist($formationData);
                $doctrine -> getManager() -> flush();
                return $this -> render('employe/dashboard_Admin.html.twig', ['formFormation' => $formFormation -> createView(), 'allInscriptions' => $allInscriptions]);
            }else{
                return $this -> render('employe/dashboard_Admin.html.twig', ['formFormation' => $formFormation -> createView(), 'allInscriptions' => $allInscriptions]);
            }
        }else{
            return $this -> redirectToRoute('app_connexion');
        }


        return $this -> render('employe/dashboard_Admin.html.twig');
    }

    #[Route('/employe/dashboard', name: 'app_dashboard_employe')]
    public function goToDashboardEmploye(ManagerRegistry $doctrine, Session $session, Request $request){
        $employe = $session -> get('employe');

        if($session -> get('success') != null){
            $success = $session -> get('success');
        }else{
            $success = null;
            $session -> set('success', $success); 
        }
        if($employe){
            $formations = $doctrine -> getRepository(Formation::class) -> findAll();
            //$inscription = $doctrine -> getRepository(Inscription::class) -> findBy(['employe' => $employe, 'formations' => $formations]);
            return $this -> render('employe/dashboard_Employe.html.twig',['formations' => $formations, 'success' => $success]);

        }else{
            return $this -> redirectToRoute('app_connexion');
        }
    }
}
