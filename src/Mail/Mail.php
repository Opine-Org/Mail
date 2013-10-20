<?php
namespace Mail;

class Mail {
	private $transport;
	private $config;
	private $key;
    private $secret;
    private $region;
    private $message = false;
    private $errors;

	public function __construct ($transport, $config, $errors) {
		$this->transport = $transport;
		$this->errors = $errors;
	}

	public function message ($emailFrom, array $emailTo, array $emailCC, array $emailBCC, $messageSubject, $messageText, $messageHTML='', array $replyToAddresses=[], $returnPath='') {
		$this->transport->message($emailFrom, $emailTo, $emailCC, $emailBCC, $messageSubject, $messageText, $messageHTML, $replyToAddresses, $returnPath);
		return $this;
	}

	public function send () {
		return $this->transport->send(); 
	}
}