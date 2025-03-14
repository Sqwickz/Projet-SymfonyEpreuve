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
use App\Entity\Employe;


final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(): Response
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }


    #[Route('/addInscription/{id}', name: 'app_addInscription')]
    public function addInscription(ManagerRegistry $doctrine, Session $session, Request $request, $id){
        $employe = $session -> get('employe');
        if($employe){
            $formation = $doctrine -> getRepository(Formation::class) -> findOneBy(['id' => $id]);
            if($formation){
                $inscriptions = $doctrine -> getRepository(Inscription::class) -> findOneBy(['employe' => $employe, 'formation' => $formation]);
                if(!$inscriptions){
                    $inscription = new Inscription();
                    $inscription -> setEmploye($doctrine -> getRepository(Employe::class) -> findOneBy(['id' => $employe -> getId()]));
                    $inscription -> setFormation($formation);
                    $inscription -> setStatut('En attente');
                    $doctrine -> getManager() -> persist($inscription);
                    $doctrine -> getManager() -> flush();
                    $session -> set('success', 'Inscription effectuée avec succès');
                    return $this -> redirectToRoute('app_dashboard_employe');
                }else{
                    $session -> set('success', 'Vous êtes déjà inscrit à cette formation');
                    return $this -> redirectToRoute('app_dashboard_employe');
                }
            }else{
                return $this -> redirectToRoute('app_dashboard_employe');
            }
        }else{
            return $this -> redirectToRoute('app_connexion');
        }
    }




    #[Route('/gestionInscription/{statut}/{inscriptionID}', name: 'app_gestionInscription')]
    public function gestionInscription(ManagerRegistry $doctrine, Session $session, Request $request, $statut, $inscriptionID){
        $employe = $session -> get('employe');
        if(!$employe){
            return $this -> redirectToRoute('app_connexion');
        }
        $inscription = $doctrine -> getRepository(Inscription::class) -> findOneBy(['id' => $inscriptionID]);
        if(!$inscription){
            return $this -> redirectToRoute('app_dashboard_admin');
        }
        if($statut == 0){
            $inscription -> setStatut('Refuser');
        }else{
            $inscription -> setStatut('Accepter');
        }
        $doctrine -> getManager() -> persist($inscription);
        $doctrine -> getManager() -> flush();
        return $this -> redirectToRoute('app_dashboard_admin');
    }
}
