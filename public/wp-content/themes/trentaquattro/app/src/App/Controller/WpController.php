<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Core\Controller;
use TimberPost;
use Timber;
use TimberHelper;
use StdClass;

class WpController extends Controller {

	public function viewSingleAction() {
		$data = [];
		$post = Timber::query_post();
		$data['post'] = $post;
		$data['comment_form'] = TimberHelper::get_comment_form();

		if ( post_password_required( $post->ID ) ) {
			return $this->render('single-password.twig', $data);
		} else {
			return $this->render(array( 
				'single-' . $post->ID . '.twig', 
				'single-' . $post->post_type . '.twig', 
				'single.twig' ), 
			$data);
		}

	}

	public function viewAuthorAction() {
		global $wp_query;

		$data = Timber::get_context();
		$data['posts'] = Timber::get_posts();
		if ( isset( $wp_query->query_vars['author'] ) ) {
			$author = new TimberUser( $wp_query->query_vars['author'] );
			$data['author'] = $author;
			$data['title'] = 'Author Archives: ' . $author->name();
		}
		Timber::render( array( 'author.twig', 'archive.twig' ), $data );
	}

	public function viewSearchAction() {
		$templates = array( 'search.twig', 'archive.twig', 'index.twig' );
		$context = Timber::get_context();

		$context['title'] = 'Search results for '. get_search_query();
		$context['posts'] = Timber::get_posts();

		Timber::render( $templates, $context );
	}

	public function viewPageAction() {
		$context = Timber::get_context();
		$post = new TimberPost();
		$context['post'] = $post;

		Timber::render( array( 'page-' . $post->post_name . '.twig', 'pages/page.twig' ), $context );
	}

	public function viewIndexAction() {
		$context = Timber::get_context();
		$context['posts'] = Timber::get_posts();

		$templates = array( 'index.twig' );
		if ( is_home() ) {
			array_unshift( $templates, 'home.twig' );
		}
		Timber::render( $templates, $context );
	}

	public function viewArchiveAction() {
		$templates = array( 'archive.twig', 'index.twig' );

		$data = Timber::get_context();

		$data['title'] = 'Archive';
		if ( is_day() ) {
			$data['title'] = 'Archive: '.get_the_date( 'D M Y' );
		} else if ( is_month() ) {
			$data['title'] = 'Archive: '.get_the_date( 'M Y' );
		} else if ( is_year() ) {
			$data['title'] = 'Archive: '.get_the_date( 'Y' );
		} else if ( is_tag() ) {
			$data['title'] = single_tag_title( '', false );
		} else if ( is_category() ) {
			$data['title'] = single_cat_title( '', false );
			array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
		} else if ( is_post_type_archive() ) {
			$data['title'] = post_type_archive_title( '', false );
			array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
		}

		$data['posts'] = Timber::get_posts();

		Timber::render( $templates, $data );
	}

	public function viewErrorPageAction($code) {
		switch($code):
			case 404:
				$context = Timber::get_context();
				Timber::render( '404.twig', $context );
			break;
		endswitch;
	}

}
