<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Form\FormError;

class ContactForm extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options ) {
		$builder
			->add('lastname', 'text', [
				'constraints' => [
					new Assert\NotBlank(),
					new Assert\Length(array('min' => 2)),
				],
			])
			->add('firstname', 'text', [
				'constraints' => [
					new Assert\NotBlank(),
					new Assert\Length(array('min' => 2)),
				],
			])
			->add('email', 'email', [
				'constraints' => [
					new Assert\NotBlank(),
					new Assert\Email()
				],
			])
			->add('message', 'textarea', [
				'constraints' => [
					new Assert\NotBlank()
				],
			])
		;
	}
	
	public function getDefaultOptions(array $options){
		$options = parent::getDefaultOptions($options);
		return $options;
	}
	
	public function getName() {
		return "contact_form";
	}
}
