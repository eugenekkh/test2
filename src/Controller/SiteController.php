<?php

namespace App\Controller;

use Carbon\Carbon;
use App\Form\FeedbackType;
use App\Model\Feedback;
use Evgeny\Controller;
use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\JsonResponse;


class SiteController extends Controller
{
    public function indexAction()
    {
        $request = $this->getContainer()->get('request');

        $feedback = new Feedback();
        $form = $this->getContainer()->get('form_factory')
            ->create(FeedbackType::class, $feedback);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback->setDate(Carbon::now());

            $file = $feedback->getImage();
            if ($file) {
                $this->resizeUploadedImage($file->getPathname());
            }

            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getContainer()->get('config')['upload_path'],
                    $fileName
                );
                $feedback->setImage($fileName);
            }

            $em = $this->getEntityManager();
            $em->persist($feedback);
            $em->flush();

            return $this->redirectToRoute('/site/index');
        }

        return $this->success('Site/index.html.twig', array(
            'feedbacks' => $this->getFeedbacks(),
            'form' => $form->createView(),

            // для генерации маршрутов сортировки
            'sort' => $request->get('sort'),
            'order' => $request->get('order'),
        ));
    }

    public function previewAction()
    {
        $request = $this->getContainer()->get('request');
        $twig = $this->container->get('twig');

        $feedback = new Feedback();
        $form = $this->getContainer()->get('form_factory')
            ->create(FeedbackType::class, $feedback);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $feedback->setDate(Carbon::now());

            $file = $feedback->getImage();
            if ($file) {
                $this->resizeUploadedImage($file->getPathname());
            }

            $preview = $twig->render('Site/feedback.html.twig', array(
                'feedback' => $feedback,
                'image64' => $file ? $this->getImage64($file) : null,
            ));

            return new JsonResponse(array(
                'preview' => $preview,
                'status' => 200
            ), 200);
        } else {
            $formHtml = $twig->render('Site/feedback_form.html.twig', array(
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('/site/preview')
            ));

            return new JsonResponse(array(
                'form' => $formHtml,
                'status' => 400
            ), 200);
        }
    }

    protected function getImage64($file)
    {
        $content = base64_encode(
            file_get_contents($file->getPathname())
        );
        return sprintf('data:%s;base64,', $file->getMimeType()) . $content;
    }

    protected function resizeUploadedImage($path)
    {
        $imageManager = $this->getContainer()->get('image_manager');
        $img = $imageManager->make($path);
        $img->resize(320, 240);
        $img->save();
    }

    protected function getFeedbacks()
    {
        $request = $this->getContainer()->get('request');

        $sort = 'date';
        $order = 'DESC';
        if ($request->get('sort')) {
            $sort = $request->get('sort');
        }

        if ($request->get('order')) {
            $order = $request->get('order');
        }

        $feedbacks = $this->getEntityManager()->getRepository(Feedback::class)
            ->getPublishedFeedbacks($sort, $order);

        return $feedbacks;
    }
}
