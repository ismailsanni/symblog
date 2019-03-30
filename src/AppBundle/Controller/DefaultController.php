<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
            'posts' => $posts,
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

        $data = $this->get('jms_serializer')->serialize($posts, 'json');
        $response = array(
            'code' => 0,
            'message' => 'success',
            'erros' => null,
            'articles' => json_decode($data),
        );
        $response = new JsonResponse($response);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;

    }
    /**
     * @Route("/post/new/", name="new_post")
     */
    public function newAction(Request $request)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
            'form' => $form->createView(),
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
            'post' => $post,
        ]);
    }

    /**
     * @Route("/post/edit/{id}", name="edit_post")
     */
    public function editAction(Request $request, Post $post)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/post/delete/{id}", name="delete_post")
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Post::class);
        $post = $repo->findOneById($id);

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Post deleted!');
        return $this->redirectToRoute('homepage');
    }

}