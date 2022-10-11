<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function listAction(int $page): Response
    {
        return $this->render('blog/list.html.twig');
    }

    #[Route('/article/{id}', name: 'article',requirements: ['id' => '\d+'])]
    public function viewAction(int $id): Response
    {
        return $this->render('blog/view.html.twig');
    }

    #[Route('/article/add', name: 'add')]
    public function addAction(): Response
    {
        if(true) {
            $this->addFlash('info', "Le message flash");
            return $this->redirectToRoute('app_blog_list', ['page'=>'1']);
        }
        return $this->render('blog/blog.html.twig');
    }

    #[Route('/article/edit/{id}', name: 'edit',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    public function editAction(int $id): Response
    {
        $this->addFlash('info', "Le message edit");
        return $this->redirectToRoute('app_blog_article', ['id'=>$id]);
    }

    #[Route('/article/delete/{id}', name: 'delete',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    public function deleteAction(int $id): Response
    {
        $this->addFlash('info', "Le message suppression");
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
