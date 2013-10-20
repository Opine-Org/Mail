<?php
namespace Mail;

class Mail {
	private $transport;

	public function __construct ($transport) {
		$this->transport = $transport;
	}

	public function message ($emailFrom, array $emailTo, array $emailCC, array $emailBCC, $messageSubject, $messageText, $messageHTML='', array $replyToAddresses=[], $returnPath='') {
		$this->transport->message($emailFrom, $emailTo, $emailCC, $emailBCC, $messageSubject, $messageText, $messageHTML, $replyToAddresses, $returnPath);
		return $this;
	}

	public function send () {
		return $this->transport->send(); 
	}
}