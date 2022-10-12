<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog', name: 'app_blog_')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function blogAction(): Response
    {
        return $this->render('blog/blog.html.twig');
    }

    #[Route('/list/{page}', name: 'list',requirements: ['page' => '\d+'], defaults: ['page' => '1'])]
    public function listAction(int $page,  EntityManagerInterface $em): Response
    {
        /*$A = new Article();
        $A -> setAuthor('ThomasC')
            -> setContent('Ceci est du texet')
            ->setCreateAt(new \DateTime('2022-09-01 12:00:00'))
            -> setNbViews('1')
            -> setPublished(true)
            -> setTitle('Titre 0')
            -> setUpdateAt(new \DateTime('2022-09-01 13:00:00'));

        $em -> persist($A);
        $em->flush();*/

        $Articles = $em->getRepository(Article::class)->findBy(['published' => true], ['createAt' => 'desc']);

        $this->getParameter('nb_article');
        return $this->render('blog/list.html.twig', ['article' => $Articles]);
    }

    #[Route('/article/{id}', name: 'article',requirements: ['id' => '\d+'])]
    public function viewAction(int $id,  EntityManagerInterface $em): Response
    {
        $Articles = $em->getRepository(Article::class)->find($id);
        /*
        $tabId = ['id' => $id, 'contenue' => 'Ceci est un texte assez
        long avec lequel nous allons tester notre
        filtre'];*/

        if (!$Articles->isPublished())
        {
            throw new NotFoundHttpException();
        }

        return $this->render('blog/view.html.twig', ['article' => $Articles]);
    }

    #[Route('/article/add', name: 'add')]
    public function addAction(): Response
    {
        if(true) {
            $this->addFlash('info', "Article ajouter");
            return $this->redirectToRoute('app_blog_list', ['page'=>'1']);
        }
        return $this->render('blog/blog.html.twig');
    }

    #[Route('/article/edit/{id}', name: 'edit',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    public function editAction(int $id): Response
    {
        $this->addFlash('info', "Le message est edit");
        return $this->render('blog/edit.html.twig');
    }

    #[Route('/article/delete/{id}', name: 'delete',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    public function deleteAction(int $id, EntityManagerInterface $em): Response
    {
        $Articles = $em->getRepository(Article::class)->find($id);
        $em->remove($Articles);
        $em->flush();

        $this->addFlash('info', "L'article est supprimer");
        return $this->redirectToRoute('app_blog_list');
    }

    public function leftMenu(): Response
    {
        $lastArticles = [];

        for($i=1 ; $i < 5; $i++) {
            $lastArticles[] = ['article' => $i];
        }
        return $this->render('blog/last_articles.html.twig', ['article' => $lastArticles]);
    }
}
