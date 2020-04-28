<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Pupil;
use App\Entity\Book;
use App\Entity\Borrow;
use App\Form\Type\BorrowType;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends AbstractController{
    
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Pupil::class);
        $pupils = $repository->findAll();
        return $this->render('front/pupil_selection.html.twig',['pupils'=>$pupils]);
    }
    
    /**
     * @Route("/choisir-livre/{idPupil}", name="select_pupil_borrow")
     */
    public function selectPupilWhoBorrow(int $idPupil)
    {
        $repositoryPupil = $this->getDoctrine()->getRepository(Pupil::class);
        $repositoryBorrow = $this->getDoctrine()->getRepository(Borrow::class);
        $currentBorrow = $repositoryBorrow->findByCurrentBorrow($idPupil);
        $pupil = $repositoryPupil->find($idPupil);
        if ($currentBorrow)
        {
            return $this->redirectToRoute('return_book', ['idPupil' => $idPupil]);
        }
        $repositoryBook = $this->getDoctrine()->getRepository(Book::class);
        $books = $repositoryBook->findAll();
        return $this->render('front/book_selection.html.twig',['pupil'=>$pupil,'books'=>$books]);
    }
    
    /**
     * @Route("/retour-livre/{idPupil}", name="return_book")
     */
    public function returnBook($idPupil,Request $request)
    {
        $repositoryBorrow = $this->getDoctrine()->getRepository(Borrow::class);
        $currentBorrow = $repositoryBorrow->findByCurrentBorrow($idPupil);
        $form = $this->createForm(BorrowType::class, $currentBorrow);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $codeSubmitted = $form->get('code')->getData();
            $book = $currentBorrow->getBook();
            if($codeSubmitted == $book->getCode())
            {
                $currentBorrow->setDateOfReturn(new \DateTime('now'));
                $book->setNbBorrow($book->getNbBorrow()+1);
                $book->setDateLastReturn(new \DateTime('now'));
                $book->setIsBorrowed(false);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($currentBorrow);
                $entityManager->persist($book);
                $entityManager->flush();
                return $this->render('front/book_return_success.html.twig',['pupil'=>$currentBorrow->getPupil(),'book'=>$currentBorrow->getBook()]);
            }
            else
            {
                $this->addFlash('error', 'Code incorrect');
            }
        }
        return $this->render('front/book_return.html.twig',['form'=>$form->createView(),'pupil'=>$currentBorrow->getPupil(),'book'=>$currentBorrow->getBook()]);
    }
    
    /**
     * @Route("/emprunt-valide/{idPupil}-{idBook}", name="select_book_borrow")
     */
    public function summaryBorrow(int $idPupil, int $idBook)
    {
        $repositoryPupil = $this->getDoctrine()->getRepository(Pupil::class);
        $repositoryBook = $this->getDoctrine()->getRepository(Book::class);
        $pupil = $repositoryPupil->find($idPupil);
        $book = $repositoryBook->find($idBook);
        $borrow = new Borrow();
        $borrow->setBook($book);
        $borrow->setPupil($pupil);
        $borrow->setDate(new \DateTime('now'));
        $book->setDateLastBorrow(new \DateTime('now'));
        $book->setIsBorrowed(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($borrow);
        $entityManager->flush();
        return $this->render('front/book_borrow_summary.html.twig',['pupil'=>$pupil,'book'=>$book]);
    }
}
