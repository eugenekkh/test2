<?php

namespace Evgeny;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Intervention\Image\ImageManager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Validator\Validation;

class Application
{
    protected $config;
    protected $container;
    protected $router;

    public function __construct(array $config)
    {
        $this->container = new Container();

        $this->container['config'] = $config;

        $this->initDoctrine();
        $this->initImageManager();
        $this->initValidator();
        $this->initFormFactory();
        $this->initTranslator();

        $this->container['router'] = function ($c) {
            return new Router($c, $c['config']['router']);
        };

        $this->initTwig();

        $session = new Session();
        $session->start();
        $this->container['session'] = $session;
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Точка входа в это упрощенное приложение
     */
    public function handle(Request $request)
    {
        $this->container['request'] = $request;

        $response = $this->container['router']->getResponse();
        $response->send();
    }

    protected function initDoctrine()
    {
        $this->container['entity_manager'] = function ($c) {
            $entityConfig = Setup::createAnnotationMetadataConfiguration($c['config']['doctrine_entity_files'], true, null, null, false);
            $entityManager = EntityManager::create($c['config']['doctrine'], $entityConfig);

            return $entityManager;
        };
    }

    protected function initImageManager()
    {
        $this->container['image_manager'] = function ($c) {
            return new ImageManager(array('driver' => 'imagick'));
        };
    }

    protected function initFormFactory()
    {
        $this->container['form_factory'] = function ($c) {
            return Forms::createFormFactoryBuilder()
                ->addExtension(new HttpFoundationExtension())
                ->addExtension(new ValidatorExtension($c['validator']))
                ->getFormFactory();
        };
    }

    protected function initTranslator()
    {
        $this->container['translator'] = function ($c) {
            // create the Translator
            $translator = new Translator('ru');
            // somehow load some translations into it
            $translator->addLoader('xlf', new XliffFileLoader());
            /*$translator->addResource(
                'xlf',
                __DIR__.'/path/to/translations/messages.en.xlf',
                'en'
            );*/
            return $translator;
        };
    }

    protected function initTwig()
    {
        $this->container['twig'] = function ($c) {
            $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
            $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

            $loader = new \Twig_Loader_Filesystem(array(
                $c['config']['twig']['templates'],
                $vendorTwigBridgeDir . '/Resources/views/Form',
            ));
            $twig = new \Twig_Environment($loader, array(
                'debug' => true
            ));
            $twig->addExtension(new \Twig_Extension_Debug());

            $formEngine = new TwigRendererEngine(array('form_div_layout.html.twig'));
            $formEngine->setEnvironment($twig);

            // add the FormExtension to Twig
            $twig->addExtension(
                new FormExtension(new TwigRenderer($formEngine))
            );

            // add the TranslationExtension (gives us trans and transChoice filters)
            $twig->addExtension(new TranslationExtension($c['translator']));

            $twig->addExtension(new TwigRouterExtension($c['router']));

            return $twig;
        };
    }

    protected function initValidator()
    {
        // create the validator - details will vary
        $validator = Validation::createValidator();

        $this->container['validator'] = $validator;
    }
}
