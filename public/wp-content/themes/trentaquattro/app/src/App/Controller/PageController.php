<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Core\Controller;
use TimberPost;
use StdClass;
use TimberImage;

class PageController extends Controller {

	/**
	 * Map to the real page ID
	**/
	const PAGE_WINES = 73;
	const PAGE_ROOM = 103;
	const PAGE_MENU = 112;
	const PAGE_CONTACT = 125;

	public function viewPersonAction() {
		$pi = new TimberPost();
		$data['post'] = $pi;
		$data['wp_title'] = $pi->title();
		$data['post']->links = get_field('links');
		$person = new StdClass();
		$person->picture = get_field("picture");
		$person->links = get_field("links");
		$data["post"]->person = $person;

		$data['post']->image = new TimberImage($pi->image);
		$data['post']->image_mobile = new TimberImage($pi->image_mobile);

		return $this->render(array('page-person-'.$pi->post_name.'.twig', 'pages/page-person.twig'), $data);
	}
	/**
	 * @return Response
	**/
	public function viewOnePageAction() {

		$data["page"] = [
			"wine" => new TimberPost(self::PAGE_WINES),
			"menu" => new TimberPost(self::PAGE_MENU),
			"room" => new TimberPost(self::PAGE_ROOM),
			"contact" => new TimberPost(self::PAGE_CONTACT),
		];

		$pi = new TimberPost();

		$data['post'] = $pi;
		$data['wp_title'] = $pi->title();

		return $this->render([ 'pages/onepage.html.twig' ], $data);
	}


	public function viewContentPageAction($specific = "content") {
		$pi = new TimberPost();
		$data['post'] = $pi;
		$data['wp_title'] = $pi->title();

		return $this->render([ sprintf('pages/%s.html.twig', $specific) ], $data);
	}


	public function viewContactPageAction() {
		$pi = new TimberPost();
		$data['post'] = $pi;
		$data['wp_title'] = $pi->title();

		return $this->render([ sprintf('pages/%s.html.twig', "contact") ], $data);
	}

	/*
	 * Display the Coming Soon Template
	 */
	public function viewComingSoonAction() {

		$db = $this->getDb();
		$request = $this->getRequest();
		$pi = new TimberPost();

		$form = $this->getFormFactory()
			->createBuilder(new \App\Form\ContactForm($db),[])
			->getForm();

		if ($request->isPost()) {
			if($request->has($form->getName())) {
	    		$form->submit($request->get($form->getName()));
	    		if ($form->isValid()) {
	    			$formData = $form->getData();
	    			$request->selfRedirection();
	    		}
			}

			if ($request->has("email")) {
				$db->query("insert into wp_mailinglist (email) values(:email)", ["email" => $request->get("email")]);
				$request->selfRedirection();
			}

		}

		return $this->render("pages/comingsoon.twig", [
			"form" => $form->createView(),
			"people" => $db->query("select * from wp_mailinglist"),
			"wp_title" => $pi->title(),
			"post" => $pi,
		]);
	}

}
