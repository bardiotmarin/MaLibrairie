<?php


namespace App\Controller;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{


    /**
     * @Route("/books", name="books_list")
     */
    // je demande à Symfony de m'instancier la classe AuthorRepository
    // avec le mécanisme d'Autowire (je passe en paramètre de la méthode
    // la classe voulue suivie d'une variable dans laquelle je veux que Symfony m'instancie ma classe
    // l'authorRepository est la classe qui permet de faire des requêtes SELECT
    // dans la table authors
    public function BooksList(BookRepository $bookRepository)
    {

        // j'utilise le bookRepository et la méthode findAll() pour récupérer tous les éléments
        // de ma table books
        $books = $bookRepository->findAll();

        return $this->render('books.html.twig', [
            'books' => $books
        ]);
    }
//je declare une route avec un selecteur sur l'id'

    /**
     * @Route("/books/{id}", name="books_solo")
     */
    //j'utilise la methode repository pour fair un select de mes id sur ma bdd
    public function BooksSolo(BookRepository $bookRepository, $id)
    {
        //        j'utilise la methode utilitaire find pour recuperer une selection rapport a mes id de ma table en bdd
        $book = $bookRepository->find($id);
        //        je fait le lien du rendu de mes  methodes (render) sur ma page book
        return $this->render('books_genre.html.twig', [
            //            j'initialise ma variable book
            "books" => $book
        ]);
    }

    /**
     * @Route("/books/genre/{id}", name="books_genre")
     */
    public function BooksByGenre(BookRepository $bookRepository, $id)
    {
        // J'utilise le bookRepository et sa méthode findBy
        // pour trouver un ou plusieurs livres en BDD
        // en fonction de la valeur d'une colonne
        $books = $bookRepository->findBy(['id' => $id]);

        return $this->render('books_genre.html.twig', [
            'books' => $books,
            'id' => $id
        ]);
    }

//    /**
//     * @Route("/books/genre/{genre}", name="books_genre")
//     */
//    public function BooksByGenre2(BookRepository $bookRepository, $genre)
//    {
//        // J'utilise le bookRepository et sa méthode findBy
//        // pour trouver un ou plusieurs livres en BDD
//        // en fonction de la valeur d'une colonne
//        $books = $bookRepository->findBy(['genre' => $genre]);
//
//        return $this->render('books_genre.html.twig', [
//            'books' => $books,
//            'genre' => $genre
//        ]);
//    }
    /**
     * @Route("/books/search/resume", name="books_search_resume")
     */
    public function BooksSearchByResume(
        BookRepository $bookRepository,
        Request $request
    )
    {
        // J'utilise la classe Request pour récupérer la valeur
        // du parametre d'url "search" (envoyé par le formulaire)
        $word = $request->query->get('search');

        // j'initilise une variable $books avec un tableau vide
        // pour ne pas avoir d'erreur si je n'ai pas de parametre d'url de recherche
        // et que donc ma méthode de répository n'est pas appelée
        $books = [];

        //  si j'ai des parametres d'url de recherche (donc que mon utilisateur
        // a fait une recherche
        if (!empty($word)) {
            // s'il a fait une recherche, je créé une requête SELECT
            // pour trouver les livres que l'utilisateur a recherché
            $books = $bookRepository->findByWordsInResume($word);
        }

        // j'appelle mon fichier twig avec les books trouvés en BDD
        return $this->render('search.html.twig', [
            'books' => $books
        ]);

    }

    /**
     *@Route("/admin/books/insert", name="books_insert")
     */
    public function insertBook(EntityManagerInterface $entityManager)
    {
        // les entités font le lien avec les tables
        // donc pour créer un enregistrement dans ma table book
        // je créé une nouvelle instance de l'entité Book
        $book = new Book();
        // je lui donne les valeurs des colonnes avec les setters
        $book->setTitle("La peau sur les os");
        $book->setGenre("horror");
        $book->setNbPages(400);
        $book->setResume('blablabla');

        // j'utilise l'entityManager pour que Doctrine
        // m'enregistre le livre créé avec la méthode persist()
        // puis je "valide" l'enregistrement avec la méthode flush()
        $entityManager->persist($book);
        $entityManager->flush();

    }
//    je cree ma route avec un chemin admin pour securiser mon url par la suite
// et je rajoute update pour bien preciser le chemin et l'action de methodes qui va etre utiliser sur cette page
    /**
     * @Route("/admin/books/update", name="books_update")
     */
//    je cree ma methodes , avec comme paramtr mon BookRepository qui va me servire a initialiser ma bdd  ,
// et mon entitymanager qui me serre a manipuler mon entiter ( ici des livres),
    public function updateBook(BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
//        j'initialise ma variable $book dans ma $bookRepository , je lui place la methodes 'find' sur mes id
        $book = $bookRepository->find(19);
//je modifie les donnes des livre ici
        $book->setTitle('albertococo');
        $book->setNbPages(800);
//je sais pas pk c'est la ça
        $entityManager->persist($book);
//        je fait un push
        $entityManager->flush();
    }

}

