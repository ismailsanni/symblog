<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;

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
