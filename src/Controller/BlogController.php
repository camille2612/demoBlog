<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
     * @Route("/blog/{id}/edit", name="blog_edit")
    */


        // Nous avons définit 2 routes différentes, une pour l'insertion et une pour la modification
        // Lorsque l'on envoie la route '/blog/new' dans l'URL, on définit un Article $article NULL, sinon Symfony tente de récuperer un article en BDD et nous avons une erreur 
        // Lorsque l'on envoie la route '/blog/{id}/edit', Symfony selectionne en BDD l'article en fonction de l'id transmit dans l'URL et écrase NULL par l'article recupéré en BDD dans l'objet $article

    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        // On entre dans la condition IF seulement dans le cas de la création d'un nouvel article, c'eest à dire pour la route '/blog/new', $article est NULL, on crée un nouvel objet Article
        // Dans le cas d'une modification, $article n'est pas NULL, il contient l'article selectionné en BDD à modifier, on entre pas dans la condition IF

        if(!$article)
        {
            $article = new Article;
        }
    //     dump($request);

    //     if($request->request->count() > 0)
    //     {
    //         $article = new Article;
    //         $article->setTitle($request->request->get('title'))
    //                 ->setContent($request->request->get('content'))
    //                 ->setImage($request->request->get('image'))
    //                 ->setCreatedAt(new \DateTime());

    //         $manager->persist($article);
    //         $manager->flush();

    //         return $this->redirectToRoute('blog_show', [
    //             'id' => $article->getId()
    //         ]);
    //     }

        // $article = new Article;

        // $article->setTitle("Titre")
        //         ->setContent("Contenu");

        // $form = $this->createFormBuilder($article)
        //              ->add('title')

        //              ->add('content')

        //              ->add('image')

        //              ->getForm();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        dump($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$article->getId())
            {
                $article->setCreatedAt(new \DateTime());
            }
           
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                          'id' => $article->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
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
