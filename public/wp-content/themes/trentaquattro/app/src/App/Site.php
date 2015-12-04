<?php 

namespace App;

use Symfony\Component\Validator\Validation;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Translation\Translator;
use App\Core\Database;
use App\Core\Request;
use App\Core\ServiceCollection;

use TimberSite;
use TimberMenu;
use PDO;
use Timber;
use Swift_SmtpTransport;
use Swift_Mailer;

use App\Model\Entity\CustomPostType;

class Site extends TimberSite {

	protected $globalContext;
	protected $services;

	public function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_filter('get_twig', [ $this, 'addToTwig' ]) ;
		add_filter( 'timber_context', [ $this, 'addToContext' ] );
		// $this->registerPostTypes();
		parent::__construct();
		// Add Simple configuration
		$this->configureSite();

	}

	protected function registerPostTypes() {
		$menu = new CustomPostType("menu");
		$menu->register_taxonomy(array(
			'taxonomy_name' => 'type',
			'singular' => 'Type',
			'plural' => 'Types',
			'slug' => 'type'
		));
		$menu->menu_icon("dashicons-book-alt");
	}

	/**
	 * Basic configuration
	 */
	protected function configureSite() {
		Timber::$dirname = ['app/views'];
		Timber::$locations = VENDOR_TWIG_BRIDGE_DIR . '/Resources/views/Form';
		Timber::$cache = false;
	}

	/**
	 * Populate the Timber context
	 *
	 * @param $context, add the Timber Context aka Array
	 *
	 * @return Array $context
	 */
	public function addToContext( $context ) {
		$context['menu'] = new TimberMenu();
		$context['site'] = $this;

		/*
		 * Add Services
		 */
		$this->services = new ServiceCollection();
		$this->services->add("request", new Request());
		$this->services->add("mailer", $this->addMailerService());
		$this->services->add("formFactory", $this->addSymfonyFormService());
		$this->services->add("db", $this->addDatabaseService());
		$context["services"] = $this->services;
		return $context;
	}

	/** 
	 * Extend the Twig Environment
	 * @param Twig_Environment $twig
	 *
	 * @return Twig_Environment
	 */
	public function addToTwig($twig){
		// Set up the CSRF Token Manager
		$csrfTokenManager = new CsrfTokenManager();
		/* this is where you can add your own fuctions to twig */
		$formEngine = new TwigRendererEngine(array(DEFAULT_FORM_THEME));
		$formEngine->setEnvironment($twig);
		
		$translator = new Translator('fr');

		$twig->addExtension(new TranslationExtension($translator));
		$twig->addExtension(
		    new FormExtension(new TwigRenderer($formEngine, $csrfTokenManager))
		);
		return $twig;
	}

	/**
	 * Get all configurations from one file in the config folder
	 *
	 * Carefull, no check if the file exists for performances
	 * @return Array
	 */
	private function getConfig($name) {
		return include sprintf(ROOT_DIR . "/app/config/%s.php", $name);
	}

	/**
	 * Configure mailer
	 *
	 * @return Application
	 */
	protected function addMailerService() {

		$mailer = (object)$this->getConfig("mailer");
		$transport = Swift_SmtpTransport::newInstance($mailer->host, $mailer->port);
		if (isset($mailer->user))
			$transport->setUsername($mailer->user);
		if (isset($mailer->password))
			$transport->setPassword($mailer->password);

		return Swift_Mailer::newInstance($transport);
	}

	/** 
	 * Add a better way to custom queries on WordPress
	 *
	 * @return Database
	 */
	protected function addDatabaseService() {
		/* Grab Database Informations */
		$db = (object) [
			'type' => 'mysql',
			'host' => DB_HOST,
			'user' => DB_USER,
			'pass' => DB_PASSWORD,
			'name' => DB_NAME,
		];
		return new Database($db);
	}

	/** 
	 * Add the Symfony Form Component to Wordpress
	 *
	 * @return FormFactoryBuilder
	 */
	public function addSymfonyFormService() {
		// Set up the CSRF Token Manager
		$csrfTokenManager = new CsrfTokenManager();

		// Set up the Validator component
		$validator = Validation::createValidator();

		// Set up the Form component
		$formFactory = Forms::createFormFactoryBuilder()
		    ->addExtension(new CsrfExtension($csrfTokenManager))
		    ->addExtension(new ValidatorExtension($validator))
		    ->getFormFactory();

		return $formFactory;
	}
}