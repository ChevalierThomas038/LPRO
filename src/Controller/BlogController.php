<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
        // Ajout article
        /*$A = new Article();
        $A -> setAuthor('ThomasC')
            -> setContent('Ceci est du texet')
            ->setCreateAt(new \DateTime('2022-02-01 12:00:00'))
            -> setNbViews('1')
            -> setPublished(true)
            -> setTitle('RE Titre 5')
            -> setUpdateAt(new \DateTime('2022-02-01 13:00:00'));

        $em -> persist($A);
        $em->flush();*/

        // Ajout comment
        /*$A = new Comment();
        $A ->setArticle($em->getRepository(Article::class)->find(8))
            ->setTitle('Avis')
            ->setAuthor('Moi')
            ->setCreateAt(new \DateTime('2022-12-12 12:00:00'))
            ->setMessage('Ceci est un message');

        $em -> persist($A);
        $em->flush();*/


        $Articles = $em->getRepository(Article::class)//->findBy(['published' => true], ['createAt' => 'desc'])
            ->pagination($page, $this->getParameter('nb_article'));

        dump($Articles);

        //return $this->render('blog/list.html.twig', ['article' => $Articles]);
        return new Response('<body></body>');

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
    public function addAction(Request $request): Response
    {
        $tire = new Article();
        $form = $this->createForm(ArticleType::class, $tire);
        $form->add('send', SubmitType::class, ['label' => 'Nouveau article']);
        $form->handleRequest($request); // Alimentation du formulaire avec la Request

        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire vient d'être soumis et il est valide => $tire est hydraté avec les données saisies

            // Traitement des données du formulaire...

            return $this->redirectToRoute('app_blog_list');
        }

        // Affichage du formulaire initial (requête GET) OU affichage du formulaire avec erreurs après validation (requête POST)
        return $this->render('blog/forum.html.twig', ['form' => $form->createView()]);

        /*
        if(true) {
            $this->addFlash('info', "Article ajouter");
            return $this->redirectToRoute('app_blog_list', ['page'=>'1']);
        }
        return $this->render('blog/blog.html.twig');*/
    }

    #[Route('/article/edit/{id}', name: 'edit',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    public function editAction(int $id, EntityManagerInterface $em, Request $request, ArticleRepository $article): Response
    {
        $tire = $article->find($id);
        $form = $this->createForm(ArticleType::class, $tire);
        $form->add('send', SubmitType::class, ['label' => 'Valider']);
        $form->handleRequest($request); // Alimentation du formulaire avec la Request

        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire vient d'être soumis et il est valide => $tire est hydraté avec les données saisies

            // Traitement des données du formulaire...

            return $this->redirectToRoute('app_blog_list');
        }

        // Affichage du formulaire initial (requête GET) OU affichage du formulaire avec erreurs après validation (requête POST)
        return $this->render('blog/forum.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/article/delete/{id}', name: 'delete',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    public function deleteAction(int $id, EntityManagerInterface $em): Response
    {
        $Articles = $em->getRepository(Article::class)->find($id);

        if (!$Articles)
        {
            throw new NotFoundHttpException();
        }

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
