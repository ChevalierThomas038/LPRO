<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\SpamFinder;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/{_locale}/blog', name: 'app_blog_', defaults: ['_locale' => 'fr'])]
class BlogController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function blogAction(): Response
    {
        return $this->render('blog/blog.html.twig');
    }

    #[Route('/category/{id}', name: 'category')]
    public function categoryAction(int $id, EntityManagerInterface $em): Response
    {
        $Articles = $em->getRepository(Article::class)->findOneByCategory($id);
        //->pagination($page, $this->getParameter('nb_article'));

        dump($Articles);

        return $this->render('blog/category.html.twig', ['articles' => $Articles]);
        //return $this->render('blog/list.html.twig');
        //return new Response('<body></body>');
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
        $A ->setArticle($em->getRepository(Article::class)->find(47))
            ->setTitle('Avis sur id 12')
            ->setAuthor('Auteur')
            ->setCreateAt(new \DateTime('2022-12-14 12:00:00'))
            ->setMessage('Ceci est un message');

        $em -> persist($A);
        $em->flush();*/


        $Articles = $em->getRepository(Article::class)//->findBy(['published' => true], ['createAt' => 'desc'])
            ->pagination($page, $this->getParameter('nb_article'));

        //dump($Articles);

        return $this->render('blog/list.html.twig', ['articles' => $Articles]);
        //return new Response('<body></body>');

    }

    #[Route('/article/{id}', name: 'article',requirements: ['id' => '\d+'])]
    public function viewAction(Security $security,int $id,  EntityManagerInterface $em): Response
    {
        $Articles = $em->getRepository(Article::class)->find($id);

        if (!$Articles || !$security->isGranted('view', $Articles)) {
            throw new NotFoundHttpException('Article inconnu');
        }

        $Articles->setNbViews($Articles->getNbViews()+1);
        /*
        $tabId = ['id' => $id, 'contenue' => 'Ceci est un texte assez
        long avec lequel nous allons tester notre
        filtre'];*/

        $em->persist($Articles);
        $em->flush();

        /*if (!$Articles->isPublished())
        {
            throw new NotFoundHttpException();
        }*/



        return $this->render('blog/view.html.twig', ['article' => $Articles]);
    }

    #[Route('/article/add', name: 'add')]
    #[IsGranted('ROLE_ADMIN')]
    public function addAction(Request $request, EntityManagerInterface $em, SpamFinder $spam): Response
    {
        $article = new Article();
        $article->setNbViews(1);
        $article->setCreateAt(new \DateTimeImmutable());
        $article->setUpdateAt(new \DateTimeImmutable());
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('send', SubmitType::class, ['label' => 'Nouveau article']);
        $form->handleRequest($request); // Alimentation du formulaire avec la Request


        if ($form->isSubmitted() && $form->isValid()) {

            if($spam->isSpam($article->getContent())){
                //return $this->render('blog/blog.html.twig');
                return  new Response('<body></body>');
            }
            // Le formulaire vient d'??tre soumis et il est valide => $tire est hydrat?? avec les donn??es saisies

            // Traitement des donn??es du formulaire...

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_blog_list');
        }

        // Affichage du formulaire initial (requ??te GET) OU affichage du formulaire avec erreurs apr??s validation (requ??te POST)
        return $this->render('blog/add.html.twig', ['form' => $form->createView()]);

        /*
        if(true) {
            $this->addFlash('info', "Article ajouter");
            return $this->redirectToRoute('app_blog_list', ['page'=>'1']);
        }
        return $this->render('blog/blog.html.twig');*/
    }

    #[Route('/article/edit/{id}', name: 'edit',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAction(Security $security, int $id, EntityManagerInterface $em, Request $request, ArticleRepository $article, SpamFinder $spam): Response
    {
        //$article_ = $article->find($id);
        $article =$em->getRepository(Article::class)->find($id);
        $form = $this->createForm(ArticleType::class, $article);
        $article->setUpdateAt(new \DateTimeImmutable());
        $form->add('send', SubmitType::class, ['label' => 'Valider']);
        $form->handleRequest($request); // Alimentation du formulaire avec la Request

        if (!$article || !$security->isGranted('edit', $article)) {
            throw new NotFoundHttpException("Vous n'avez pas le droit d'??diter cette article !");
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if($spam->isSpam($article->getContent())){
                //return $this->render('blog/blog.html.twig');
                return  new Response('<body></body>');
            }

            // Le formulaire vient d'??tre soumis et il est valide => $tire est hydrat?? avec les donn??es saisies

            // Traitement des donn??es du formulaire...
            $em->persist($article);
            $em->flush();

            $this->addFlash('info', "L'article est modifier");
            return $this->redirectToRoute('app_blog_list');
        }

        // Affichage du formulaire initial (requ??te GET) OU affichage du formulaire avec erreurs apr??s validation (requ??te POST)
        return $this->render('blog/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/article/delete/{id}', name: 'delete',requirements: ['id' => '\d+'], defaults: ['id' => '1'])]
    #[IsGranted('ROLE_ADMIN')]
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

    public function leftMenu(EntityManagerInterface $em): Response
    {
        $Articles = $em->getRepository(Article::class)//->findBy(['published' => true], ['createAt' => 'desc'])
        ->pagination(1, $this->getParameter('nb_article'));

        $Categories = $em->getRepository(Category::class)->findAll();

        return $this->render('blog/last_articles.html.twig', ['articles' => $Articles , 'categories' => $Categories]);
    }

    #[Route('/change/locale/{lang}/{route}', name: 'change_locale')]
    public function changeLocalLang(string $lang, string $route)
    {
        return $this->redirectToRoute($route, ['_locale'=> $lang]);
    }

    #[Route('/test-slug/{titre}')]
    public function viewSlugAction(EntityManagerInterface $em, string $titre): Response
    {
        $article = $em->getRepository(Article::class)->findOneBy(['title' => $titre]);

        if (!$article)
        {
            throw new NotFoundHttpException("Article non existant");
        }

        $article->setNbViews($article->getNbViews()+1);

        $em->persist($article);
        $em->flush();

        return $this->render('blog/view.html.twig', ['article' => $article]);
    }
}
