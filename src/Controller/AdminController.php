<?php

namespace App\Controller;

use App\Form\FeedbackEditType;
use App\Form\LoginType;
use App\Model\Feedback;
use App\Model\User;
use Evgeny\Controller;
use Symfony\Component\Form\FormError;

class AdminController extends Controller
{
    public function indexAction()
    {
        $session = $this->getContainer()->get('session');

        if (!$session->get('user')) {
            return $this->redirectToRoute('/admin/login');
        }

        $feedbacks = $this->getEntityManager()->getRepository(Feedback::class)
            ->findAll();

        return $this->success('Admin/index.html.twig', array(
            'feedbacks' => $feedbacks,

        ));
    }

    public function editAction()
    {
        $session = $this->getContainer()->get('session');

        if (!$session->get('user')) {
            return $this->redirectToRoute('/admin/login');
        }

        $request = $this->getContainer()->get('request');
        $id = $request->get('id');

        if (!$id) {
            return $this->redirectToRoute('/admin/index');
        }

        $feedback = $this->getEntityManager()->getRepository(Feedback::class)
            ->findOneById($id);

        if (!$feedback) {
            return $this->redirectToRoute('/admin/index');
        }

        $form = $this->getContainer()->get('form_factory')
            ->create(FeedbackEditType::class, $feedback);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEntityManager();

            $em->persist($feedback);
            $em->flush();

            return $this->redirectToRoute('/admin/index');
        }

        return $this->success('Admin/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function loginAction()
    {
        $session = $this->getContainer()->get('session');

        if ($session->get('user')) {
            return $this->redirectToRoute('/admin/index');
        }

        $request = $this->getContainer()->get('request');

        $user = new User();
        $form = $this->getContainer()->get('form_factory')
            ->create(LoginType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actualUser = $this->getEntityManager()->getRepository(User::class)
                ->findOneByName($user->getName());

            if ($actualUser && $actualUser->getPassword() == $user->getPassword()) {
                $session->set('user', $actualUser->getName());

                return $this->redirectToRoute('/admin/index');
            } else {
                $form->get('name')->addError(new FormError('Неправильный логин или пароль'));
            }
        }

        return $this->success('Admin/login.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function logoutAction()
    {
        $session = $this->getContainer()->get('session');

        if ($session->get('user')) {
            $session->set('user', null);
        }

        return $this->redirectToRoute('/admin/login');
    }
}
