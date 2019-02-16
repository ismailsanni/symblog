<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Post::class);
        $posts = $repo->findAll();

        return $this->render('default/index.html.twig', [
            'posts' => $posts
        ]);
    }
    /**
     * @Route("/api/articles", name="show_articles_api")
     */
    public function showArticlesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Post::class);
        $posts = $repo->findAll();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($posts, 'json');
       
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }
     /**
     * @Route("/post/new/", name="new_post")
     */
    public function newAction(Request $request)
    {   
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $post = $form->getData();
            $post->setCreatedAt(new \DateTime("now"));
            $post->setUpdatedAt(new \DateTime("now"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post created!');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/{id}", name="show_post")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Post::class);
        $post = $repo->findOneById($id);

        return $this->render('default/single.html.twig', [
            'post' => $post
        ]);
    }

     /**
     * @Route("/post/edit/{id}", name="edit_post")
     */
    public function editAction(Request $request, Post $post)
    {   
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = new Post();
            $post = $form->getData();
            $post->setUpdatedAt(new \DateTime("now"));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post updated!');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('default/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

   
}
