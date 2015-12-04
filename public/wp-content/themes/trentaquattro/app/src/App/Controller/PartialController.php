<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Core\Controller;
use TimberPost;
use Timber;
use TimberHelper;
use StdClass;

class PartialController extends Controller {

	public function viewSidebarAction() {
		return $this->render([ 'partials/sidebar.twig' ], $this->context );
	}

}
