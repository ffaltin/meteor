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
		$this->addClientRole();
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

	public function addClientRole() {
		$result = add_role( 'client', __("Client"), [
			"switch_themes" => false,
			"edit_themes" => false,
			"activate_plugins" => false,
			"edit_plugins" => false,
			"edit_users" => false,
			"edit_files" => true,
			"manage_options" => false,
			"moderate_comments" => false,
			"manage_categories" => false,
			"manage_links" => false,
			"upload_files" => true,
			"import" => false,
			"unfiltered_html" => false,
			"edit_posts" => false,
			"edit_others_posts" => false,
			"edit_published_posts" => false,
			"publish_posts" => false,
			"edit_pages" => true,
			"read" => true,
			"level_10" => false,
			"level_9" => false,
			"level_8" => false,
			"level_7" => false,
			"level_6" => false,
			"level_5" => false,
			"level_4" => false,
			"level_3" => false,
			"level_2" => false,
			"level_1" => false,
			"level_0" => false,
			"edit_others_pages" => true,
			"edit_published_pages" => true,
			"publish_pages" => true,
			"delete_pages" => false,
			"delete_others_pages" => false,
			"delete_published_pages" => false,
			"delete_posts" => false,
			"delete_others_posts" => false,
			"delete_published_posts" => false,
			"delete_private_posts" => false,
			"edit_private_posts" => false,
			"read_private_posts" => false,
			"delete_private_pages" => false,
			"edit_private_pages" => false,
			"read_private_pages" => false,
			"delete_users" => false,
			"create_users" => false,
			"unfiltered_upload" => false,
			"edit_dashboard" => true,
			"update_plugins" => false,
			"delete_plugins" => false,
			"install_plugins" => false,
			"update_themes" => false,
			"install_themes" => false,
			"update_core" => false,
			"list_users" => false,
			"remove_users" => false,
			"add_users" => false,
			"promote_users" => false,
			"edit_theme_options" => false,
			"delete_themes" => false,
			"export" => false,
		]);
	}
}