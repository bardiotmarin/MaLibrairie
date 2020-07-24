<?php


namespace App\Controller;

use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{

    /**
     * @Route("/authors", name="authors_list")
     */
    // je demande à Symfony de m'instancier la classe AuthorRepository
    // avec le mécanisme d'Autowire (je passe en paramètre de la méthode
    // la classe voulue suivie d'une variable dans laquelle je veux que Symfony m'instancie ma classe
    // l'authorRepository est la classe qui permet de faire des requêtes SELECT
    // dans la table authors
    public function AuthorsList(AuthorRepository $authorRepository)
    {

        // j'utilise l'authorRepository et la méthode findAll() pour récupérer tous les éléments
        // de ma table authors

        $authors = $authorRepository->findAll();
        return $this->render('authors.html.twig', [
            //         je declare ma variable author pour la linker avec mon array bdd
            'authors' => $authors
        ]);
    }



    //je declare ma route , avec un acces aux id(index) de ma bdd
    /**
     * @Route("/author/{id}", name="authors_view")
     */
    //    je declare ma function avec la methodes utilitaires repository
    public function AuthorsView(AuthorRepository $authorRepository,$id)
    {
    //        je declare ma variable , pour la lie avec mes donnees de bdd je cheche les index par id
        $author =$authorRepository->find($id);
        return $this->render('author.html.twig',[
            "author"=> $author
        ]);


    }

}