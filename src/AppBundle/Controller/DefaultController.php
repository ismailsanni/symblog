<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Post;

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
}
