<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\BookType;
use App\Entity\Book;
use App\Form\Type\PupilType;
Use App\Entity\Pupil;
Use App\Entity\Borrow;
use App\Entity\Image;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BackController extends AbstractController{
    
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->render('back/login.html.twig');
    }
    
    /**
     * @Route("/admin/eleves", name="pupils_list")
     */
    public function pupilsList()
    {
        $repository = $this->getDoctrine()->getRepository(Pupil::class);
        $pupils = $repository->findAll();
        return $this->render('back/pupil/pupils_list.html.twig',['pupils'=>$pupils]);
    }
    
    /**
     * @Route("/admin/livres", name="books_list")
     */
    public function booksList()
    {
        $repository = $this->getDoctrine()->getRepository(Book::class);
        $books = $repository->findAll();
        return $this->render('back/book/books_list.html.twig',['books'=>$books]);
    }
    
    /**
     * @Route("/admin/emprunts", name="borrows_list")
     */
    public function borrowsList()
    {
        $repository = $this->getDoctrine()->getRepository(Borrow::class);
        $borrows = $repository->findAll();
        return $this->render('back/borrow/borrows_list.html.twig',['borrows'=>$borrows]);
    }
    
     /**
     * @Route("/admin/emprunts/non-rendu", name="borrows_not_returned_list")
     */
    public function borrowsNotReturnedList()
    {
        $repository = $this->getDoctrine()->getRepository(Borrow::class);
        $borrows = $repository->findByNotReturn();
        return $this->render('back/borrow/borrows_not_returned_list.html.twig',['borrows'=>$borrows]);
    }
    
    /**
     * @Route("/admin/eleve/{id}", name="pupil_detail", requirements={"id"="\d+"})
     */
    public function pupilDetail(int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Pupil::class);
        $pupil = $repository->find($id);
        return $this->render('back/pupil/pupil_detail.html.twig',['pupil'=>$pupil]);
    }
    
    /**
     * @Route("/admin/livre/{id}", name="book_detail", requirements={"id"="\d+"})
     */
    public function bookDetail(int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Book::class);
        $book = $repository->find($id);
        return $this->render('back/book/book_detail.html.twig',['book'=>$book]);
    }
    
    /**
     * @Route("/admin/emprunt/{id}", name="borrow_detail", requirements={"id"="\d+"})
     */
    public function borrowDetail(int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Borrow::class);
        $borrow = $repository->find($id);
        return $this->render('back/borrow/borrow_detail.html.twig',['borrow'=>$borrow]);
    }
    
    /**
     * @Route("/admin/livre/ajouter", name="book_create")
     */
    public function createBook(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $book = $form->getData();
            $book->setNbBorrow(0);
            $imageUploaded = $form->get('image')->getData();
            if ($imageUploaded) {
                $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = $slugger->slug($originalFilename);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $imageUploaded->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $image = new Image();
                $image->setUrl('/BEB/public/uploads/img/'.$newFilename);
                $image->setAlt($originalFilename);
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $book->setImage($image);
                $book->setIsBorrowed(0);
                $entityManager->persist($image);
                $entityManager->flush();
            }
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('books_list');
        }
        return $this->render('back/book/book_create.html.twig',['form'=>$form->createView()]);
    }   
    
    /**
     * @Route("/admin/eleve/ajouter", name="pupil_create")
     */
    public function createPupil(Request $request)
    {
        $pupil = new Pupil();
        $form = $this->createForm(PupilType::class, $pupil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $pupil = $form->getData();
            $imageUploaded = $form->get('image')->getData();
            if ($imageUploaded) {
                $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = $slugger->slug($originalFilename);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
                try {
                    $imageUploaded->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $image = new Image();
                $image->setUrl('/BEB/public/uploads/img/'.$newFilename);
                $image->setAlt($originalFilename);
                $pupil->setImage($image);
                $entityManager->persist($image);
                $entityManager->flush();
            }
            $entityManager->persist($pupil);
            $entityManager->flush();
            return $this->redirectToRoute('pupils_list');
        }
        return $this->render('back/pupil/pupil_create.html.twig',['form'=>$form->createView()]);
    }  
    
    /**
     * @Route("/admin/eleve/{idPupil}/modifier", name="pupil_edit")
     */
    public function editPupil($idPupil, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Pupil::class);
        $pupil = $repository->find($idPupil);
        $form = $this->createForm(PupilType::class, $pupil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $pupil = $form->getData();
            $imageUploaded = $form->get('image')->getData();
            if ($imageUploaded) {
                $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = $slugger->slug($originalFilename);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
                try {
                    $imageUploaded->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $image = new Image();
                $image->setUrl('/BEB/public/uploads/img/'.$newFilename);
                $image->setAlt($originalFilename);
                $pupil->setImage($image);
                $entityManager->persist($image);
                $entityManager->flush();
            }
            $entityManager->persist($pupil);
            $entityManager->flush();
            return $this->redirectToRoute('pupils_list');
        }
        return $this->render('back/pupil/pupil_edit.html.twig',['form'=>$form->createView(),'pupil'=>$pupil]);
    }
    
    /**
     * @Route("/admin/livre/{idBook}/modifier", name="book_edit")
     */
    public function editBook($idBook, Request $request)
    {
        $repositoryBook = $this->getDoctrine()->getRepository(Book::class);
        $book = $repositoryBook->find($idBook);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $book = $form->getData();
            $imageUploaded = $form->get('image')->getData();
            if ($imageUploaded) {
                $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = $slugger->slug($originalFilename);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
                try {
                    $imageUploaded->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $image = new Image();
                $image->setUrl('/BEB/public/uploads/img/'.$newFilename);
                $image->setAlt($originalFilename);
                $book->setImage($image);
                $entityManager->persist($image);
                $entityManager->flush();
            }
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('books_list');
        }
        return $this->render('back/book/book_edit.html.twig',['form'=>$form->createView(),'book'=>$book]);
    }
    
    /**
     * @Route("/admin/livre/{idBook}/supprimer", name="book_delete")
     */
    public function deleteBook($idBook)
    {
        $repository = $this->getDoctrine()->getRepository(Book::class);
        $book = $repository->find($idBook);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute('books_list');
    }
    
    /**
     * @Route("/admin/eleve/{idPupil}/supprimer", name="pupil_delete")
     */
    public function deletePupil($idPupil)
    {
        $repository = $this->getDoctrine()->getRepository(Pupil::class);
        $pupil = $repository->find($idPupil);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($pupil);
        $entityManager->flush();
        return $this->redirectToRoute('pupils_list');
    }
    
    /**
     * @Route("/admin/livre/{idBook}/historique", name="book_history")
     */
    public function BookHistory($idBook)
    {
        $repositoryBook = $this->getDoctrine()->getRepository(Book::class);
        $book = $repositoryBook->find($idBook);
        $repositoryBorrow = $this->getDoctrine()->getRepository(Borrow::class);
        $borrows = $repositoryBorrow->findByBook($book->getId());
        return $this->render('back/book/book_history.html.twig',['book'=>$book,'borrows'=>$borrows]);
    }
    
    /**
     * @Route("/admin/eleve/{idPupil}/historique", name="pupil_history")
     */
    public function PupilHistory($idPupil)
    {
        $repositoryPupil = $this->getDoctrine()->getRepository(Pupil::class);
        $pupil = $repositoryPupil->find($idPupil);
        $repositoryBorrow = $this->getDoctrine()->getRepository(Borrow::class);
        $borrows = $repositoryBorrow->findByPupil($pupil->getId());
        return $this->render('back/pupil/pupil_history.html.twig',['pupil'=>$pupil,'borrows'=>$borrows]);
    }
    
    /**
     * @Route("/admin/book/search/{field}-{value}", name="book_search")
     */
    public function bookSearch(Request $request, $field, $value)
    {
       
        if($request->request->get('value') !== 'nullValue'){
            $repositoryBook = $this->getDoctrine()->getRepository(Book::class);
            $listBooks = $repositoryBook->findByFieldValue($field, $value);
            $books='';
            foreach ($listBooks as $book)
            {
                $dateLastBorrow = ($book->getDateLastBorrow())? $book->getDateLastBorrow()->format('d-m-Y') : 'Jamais';
                $dateLastReturn = ($book->getDateLastReturn())? $book->getDateLastReturn()->format('d-m-Y') : 'Non';
                $isBorrowed = ($book->getIsBorrowed())? 'Oui' : 'Non';
                $books.= '<tr class="result-book">
                            <td>'.$book->getTitle().'</td>
                            <td>'.$book->getAuthor().'</td>
                            <td>'.$book->getTheme().'</td>
                            <td>'.$book->getCode().'</td>
                            <td>'.$isBorrowed.'</td>
                            <td>'.$dateLastBorrow.'</td>
                            <td>'.$dateLastReturn.'</td>
                            <td><a href="/beb/public/admin/livre/'.$book->getId().'/modifier">Modifier</a>
                        </tr>';

            }
            $response = new Response(json_encode(array(
                'books' => $books
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $response = new Response(json_encode(array(
                'books' => ''
            )));
        return $response;
    }
    
    /**
     * @Route("/admin/pupil/search/{field}-{value}", name="pupil_search")
     */
    public function pupilSearch(Request $request, $field, $value)
    {
        if($request->request->get('value') !== 'nullValue'){
            $repositoryPupil = $this->getDoctrine()->getRepository(Pupil::class);
            $listPupils = $repositoryPupil->findByFieldValue($field, $value);
            $pupils='';
            foreach ($listPupils as $pupil)
            {
                $dateOfBirth = ($pupil->getDateOfBirth())? $pupil->getDateOfBirth()->format('d-m-Y') : 'Non renseignée';
                $pupils.= '<tr class="result-pupil">
                            <td>'.$pupil->getLastName().'</td>
                            <td>'.$pupil->getFirstName().'</td>
                            <td>'.$dateOfBirth.'</td>
                            <td>'.$pupil->getGrade().'</td>
                            <td><a href="/beb/public/admin/eleve/'.$pupil->getId().'/modifier">Modifier</a>
                        </tr>';

            }
            $response = new Response(json_encode(array(
                'pupils' => $pupils
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $response = new Response(json_encode(array(
                'pupils' => ''
            )));
        return $response;
    }
    
    /**
     * @Route("/admin/borrow/search/{field}-{value}", name="borrow_search")
     */
    public function borrowSearch(Request $request, $field, $value)
    {
        if($request->request->get('value') !== 'nullValue'){
            $repositoryBorrow = $this->getDoctrine()->getRepository(Borrow::class);
            $listBorrows = $repositoryBorrow->findByFieldValue($field, $value);
            $borrows='';
            foreach ($listBorrows as $borrow)
            {
                $dateBorrow = ($borrow->getDate())? $borrow->getDate()->format('d-m-Y') : 'Date d\'emprunt non connue';
                $dateOfReturn = ($borrow->getDateOfReturn())? $borrow->getDateOfReturn()->format('d-m-Y') : 'Non rendu';
                $borrows.= '<tr class="result-borrow">
                            <td>'.$borrow->getBook()->getTitle().'</td>
                            <td>'.$borrow->getPupil()->getFirstName().' '.$borrow->getPupil()->getLastName().'</td>
                            <td>'.$dateBorrow.'</td>
                            <td>'.$dateOfReturn.'</td>
                        </tr>';

            }
            $response = new Response(json_encode(array(
                'borrows' => $borrows
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $response = new Response(json_encode(array(
                'borrows' => ''
            )));
        return $response;
    }
}
