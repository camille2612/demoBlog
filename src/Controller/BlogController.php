<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    // Chaque méthode du controller est associé à une route bien spécifique

    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        // dump($repo);

        $articles = $repo->findAll();
        // dump($articles);

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }
    
    /**
     * @route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur le blog Symfony',
            'age' => 26
        ]);
    }

     /**
     * @Route("/blog/new", name="blog_create")
     */
    public function create (Request $request, EntityManagerInterface $manager)
    {
        dump($request);

        if($request->request->count() > 0)
        {
            $article = new Article;
            $article->setTitle($request->request->get('title'))
                    ->setContent($request->request->get('content'))
                    ->setImage($request->request->get('image'))
                    ->setCreatedAt(new \DateTime());

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('blog/create.html.twig');
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article): Response
    {

        return $this ->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }

   
}
