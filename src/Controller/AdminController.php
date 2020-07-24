<?php


namespace App\Controller;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorsType;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/books", name="admin_books")
     */
    //j'utilise la methode repository pour fair un select de mes id sur ma bdd
    public function AdminBooks(BookRepository $bookRepository)
    {
        //        j'utilise la methode utilitaire find pour recuperer une selection rapport a mes id de ma table en bdd
        $books = $bookRepository->findall();
        //        je fait le lien du rendu de mes  methodes (render) sur ma page book
        return $this->render('admin/admin_book.html.twig', [
            //            j'initialise ma variable book
            "books" => $books
        ]);
    }


    /**
     * @Route("/admin/books/delete/{id}" , name="admin_book_delete")
     */
    public function AdminBookDelete(
        BookRepository $bookRepository, EntityManagerInterface  $entityManager, $id)
    {
        $book = $bookRepository -> find($id);
            $entityManager -> remove($book);
            $entityManager -> flush();
            return $this -> redirectToRoute('admin_books');
    }

//    apres sa je vais faire des methodes avec de grosse action , ajouter un livre et ensuite modifier un livre




//            je cree ma route avec un chemin admin pour securiser mon url par la suite
//         et je rajoute update pour bien preciser le chemin et l'action de methodes qui va etre utiliser sur cette page

        /**
         * @Route("/admin/books/update/{id}", name="admin_books_update")
         */
        //    je cree ma methodes , avec comme paramtr mon BookRepository qui va me servire a initialiser ma bdd  ,
        // et mon entitymanager qui me serre a manipuler mon entiter ( ici des livres),

    public function AdminUpdateBook(BookRepository $bookRepository,
                                    Request $request,
                                    EntityManagerInterface $entityManager,
                                    $id

    )
    {
//            j'initialise ma variable $book dans ma $bookRepository , je lui place la methodes 'find' sur mes id
        $book = $bookRepository->find($id);
        $bookForm = $this->createForm(BookType::class, $book);

        $bookForm->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_books');
        }

        return $this->render('admin/admin_book_update.html.twig', [
            'bookForm' => $bookForm->createView()
        ]);
    }
    /**
     * @Route("/admin/books/inserts", name="Admin_insert")
     */
    public function AdminInsert(Request $request, EntityManagerInterface $entityManager)
    {
//        je cree une nouvelle instance de l'entité book
        $book = new Book();
        // je récupère le gabarit de formulaire de
        // l'entité Book, créé avec la commande make:form
        // et je le stocke dans une variable $bookForm
        $bookForm = $this ->createForm(BookType::class,$book);
        // on prend les données de la requête (classe Request)
        //et on les "envoie" à notre formulaire
        $bookForm -> handleRequest($request);
        // si le formulaire a été envoyé et que les données sont valides
        // par rapport à celles attendues alors je persiste le livre
        if ($bookForm->isSubmitted()&&$bookForm->isValid()){
           $bookCoverFile = $bookForm->get('bookCover')->getData();
           if ($bookCoverFile){
                $originalCoverName = pathinfo($bookCoverFile->getClientOriginalname(),PATHINFO_FILENAME);

                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalCoverName);
                $uniqueCoverName = $safeFilename .'-'.uniqid(). '.'.$bookCoverFile->guessExtension();
              try{
                  $bookCoverFile->move(
                      $this->getParameter('book_cover_directory'),
                      $uniqueCoverName
                  );
              } catch (FileException $e){
                  return new Response($e->getMessage());
              }
              $book->setBookCover($uniqueCoverName);
           }
            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Votre livre a été créé !');
            return $this ->redirectToRoute("admin_books");
        }
     return $this->render("admin/admin_book_insert.html.twig",[
     "bookInsert" => $bookForm -> createView()
     ]);
    }
//auteur
    /**
     * @Route("/admin/author", name="admin_author")
     */
    //j'utilise la methode repository pour fair un select de mes id sur ma bdd
    public function AdminAuthor(AuthorRepository $authorRepository)
    {
        //        j'utilise la methode utilitaire find pour recuperer une selection rapport a mes id de ma table en bdd
        $author = $authorRepository->findall();
        //        je fait le lien du rendu de mes  methodes (render) sur ma page book
        return $this->render('admin/admin_author.html.twig', [
            //            j'initialise ma variable book
            "author" => $author
        ]);
    }

    /**
     * @Route("/admin/author/delete/{id}" , name="admin_author_delete")
     */
    public function AdminAuthorDelete(
        AuthorRepository $authorRepository, EntityManagerInterface  $entityManager, $id)
    {
        $author = $authorRepository -> find($id);
        $entityManager -> remove($author);
        $entityManager -> flush();
        return $this -> redirectToRoute('admin_author');
    }

    /**
     * @Route("/admin/author/inserts", name="Admin_insert_author")
     */
    public function AdminInsertAuthor(Request $request,EntityManagerInterface $entityManager)
    {
        $author = new Author();
        $authorForm = $this ->createForm(AuthorsType::class, $author);

        $authorForm -> handleRequest($request);
        if ($authorForm->isSubmitted() && $authorForm->isValid()){
            $entityManager->persist($author);
            $entityManager->flush();
            return $this ->redirectToRoute("admin_author");
        }
        return $this->render("admin/admin_author_insert.html.twig",[
            "AuthorInsert" => $authorForm -> createView()
        ]);
    }
    /**
     * @Route("/admin/author/update/{id}", name="admin_update_author")
     */
    //    je cree ma methodes , avec comme paramtr mon BookRepository qui va me servire a initialiser ma bdd  ,
    // et mon entitymanager qui me serre a manipuler mon entiter ( ici des auteurs),

    public function AdminUpdateAuthor(AuthorRepository $authorRepository,
                                    Request $request,
                                    EntityManagerInterface $entityManager,
                                    $id)
    {
//            j'initialise ma variable $book dans ma $bookRepository , je lui place la methodes 'find' sur mes id
        $author = $authorRepository->find($id);
        $authorForm = $this->createForm(AuthorsType::class, $author);

        $authorForm->handleRequest($request);

        if ($authorForm->isSubmitted() && $authorForm->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('admin_books');
        }

        return $this->render('admin/admin_author_update.html.twig', [
            'AuthorUpdate' => $authorForm->createView()
        ]);
    }



//    /**
//     * @Route("/admin/book/insertwithgenre", name="admin_book_insert_genre")
//     */
//    public function InsertBookWithGenre
//    (GenreRepository $genreRepository, EntityManagerInterface $entityManager )
//    {
//        $genre =$genreRepository->find(2);
//        $book = new Book();
//        $book->setTitle('titre le romano');
//        $book->setnbpages(69);
//        $book->setResume('ehfjkahjkfha')  ;
//        $book->setGenre($genre);
//        $entityManager->persist($book);
//        $entityManager->flush();
//        return new Response('livre enregistrer');
//    }

}
