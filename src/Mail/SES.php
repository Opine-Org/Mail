<?php
namespace Mail;
use Aws\Ses\SesClient;

class SES {
	private $config;
	private $client;
	private $message;

	public function __construct ($config) {
		$config = $config->amazonses;
		if ($config === false) {
			throw new \Exception('Missing amazonses configuration.');
		}
		$this->client = SesClient::factory((array)$config);
	}

	public function message ($emailFrom, array $emailTo, array $emailCC, array $emailBCC, $messageSubject, $messageText, $messageHTML='', array $replyToAddresses=[], $returnPath='') {
		$this->message = [];
		$this->message['Source'] = $emailFrom;
		foreach ($emailTo as $to) {
			$this->message['Destination']['ToAddresses'][] = $to;
		}
		foreach ($emailCC as $cc) {
			$this->message['Destination']['CcAddresses'][] = $cc;
		}
		foreach ($emailBCC as $bcc) {
			$this->message['Destination']['BccAddresses'][] = $bcc;
		}
		$this->message['Message']['Subject']['Data'] = $messageSubject;
		$this->message['Message']['Subject']['Charset'] = 'UTF-8';
		$this->message['Message']['Body']['Text']['Data'] = $messageText;
		$this->message['Message']['Body']['Text']['Charset'] = 'UTF-8';
		if (!empty($messageHTML)) {
			$this->message['Message']['Body']['Html']['Data'] = $messageHTML;
			$this->message['Message']['Body']['Html']['Charset'] = 'UTF-8';
		}
		foreach ($replyToAddresses as $replyTo) {
			$this->message['ReplyToAddresses'][] = $replyTo;
		}
		if (!empty($returnPath)) {
			$this->message['ReturnPath'] = $returnPath;
		}
	}

	public function send () {
		if ($this->message === false) {
			throw new \Exception('Unformed message.');
		}
     	$result = $this->client->sendEmail($this->message);
		return $result->get('MessageId');
	}
}