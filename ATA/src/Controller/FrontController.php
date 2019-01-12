<?php
/**
 * Created by PhpStorm.
 * User: pc 01
 * Date: 07/01/2019
 * Time: 17:38
 */

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Evenement;
use App\Entity\Article;
use App\Entity\Photos;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FrontController extends AbstractController
{

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }

    /**
     *
     * @Route("/contact", name="front_contact")
     */
    public function contact()
    {
        return $this->render('front/contact.html.twig');
    }

    /**
     * @Route("/galerie",name="front_galerie")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function galerie()
    {
        $photos = $this->getDoctrine()
            ->getRepository(Photos::class)
            ->findByDate();
        return $this->render('front/galerie.html.twig',[
            'photos' => $photos
        ]);
    }


    /**
     * @Route("/evenements",name="front_categorie_evenements")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categorieEvenement()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        $evenement = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();

        return $this->render('front/categorie.html.twig', [
            'categories'=> $categories,
            'evenement'=> $evenement
        ]);


    }


    /**
     * Affiche LES articles
     * @Route("/articles",name="front_les_articles" )
     */
    public function lesArticles()
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();


        return $this->render('front/lesArticles.html.twig', [
            'article' => $article
        ]);


    }


    /**
     * Affiche UN article
     * @Route("/{slug<[a-zA-Z0-9\-_\/]+>}_{id<\d+>}.html",
     *     name="front_article")
     *
     * @param Article $article
     * @param $slug
     * @param id
     * @return \Symfony\Component\HttpFoundation\Response
     */
//
    public function article($slug, article $article = null) // par défaut vaut null
    {
//        #####################################
//               REQUETE TEST DE RECUP

//        $article = $this->getDoctrine()
//            ->getRepository(Article::class)
//            ->find(5);
//
//        return $this->render('front/article.html.twig',
//            [    'article' => $article ]);
//        #####################################

        #on s'assure que l'article ne soit pas existant
        if(null === $article){

            return $this->redirectToRoute('index',[],\Symfony\Component\HttpFoundation\Response::HTTP_MOVED_PERMANENTLY);
        }

        #verification du SLUG
        if($article->getSlug() !== $slug){
            return $this->redirectToRoute('front/article.html.twig',[
                'slug'      => $article->getSlug(),
                'id'        => $article->getId()
            ]);

        }

        return $this->render('front/article.html.twig',[
            'article' => $article
        ]);

    }


    /**
     * @Route("/apropos", name="front_apropos")
     *
     */
    public function apropos()
    {
        return $this->render('front/apropos.html.twig');
    }


    /**
     * @Route("{categorie<[a-zA-Z0-9-/]+>}/{slug<[a-zA-Z0-9-/]+>}-{id<\d+>}",
     *     name="front_evenement")
     * @param $categorie
     * @param $slug
     * @param Evenement|null $evenement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function evenement( $categorie, $slug, Evenement $evenement = null)

    {
        #$article = $this->getDoctrine()
        #                               ->getRepository(Article::class)
        #                              ->find($id);

        if (null === $evenement) {
            return $this->redirectToRoute('front_categorie_evenements', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        #Verification du SLUG
        if ($evenement->getSlug() !== $slug || $evenement->getCategories()->getSlug() !== $categorie) {
            return $this->redirectToRoute('front_evenement', [
                'slug' => $evenement->getSlug(),
                'id' => $evenement->getId()
            ]);
        }

        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        # return new Response("<html><body><h1>PAGE ARTICLE : $id</h1></body></html>");
        return $this->render('evenement/evenement.html.twig', [
            'evenement' => $evenement,
            'categories' => $categories
        ]);
    }

    public function sidebar()
    {

        $photos = $this->getDoctrine()
            ->getRepository(Photos::class)
            ->dernieresPhotos();

        #Récuperation du Repository
        $repository = $this->getDoctrine()
            ->getRepository(Evenement::class);

        # Récupérer le 5 derniers articles
        $evenements = $repository->findLatestEvenements();


        #Rendu de la vue
        return $this->render('component/_sidebar.html.twig', [
            'evenements' => $evenements,
            'photos' => $photos
        ]);
    }

}


