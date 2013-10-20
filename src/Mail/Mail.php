<?php
namespace Mail;

class Mail {
	private $client;
	private $config;
	private $key;
    private $secret;
    private $region;
    private $message = false;
    private $errors;

	public function __construct ($SesClient, $config, $errors) {
		if (empty($config->amazonses)) {
			throw new \Exception('No SES credentials specified.');
		}
		$this->client = $SesClient::factory([
    		'key'    => $config->amazonses['key'],
    		'secret' => $config->amazonses['secret'],
    		'region' => $config->amazonses['region']
		]);
		$this->errors = $errors;
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
		$this->message['Message']['Subject']['Data'] = $subject;
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
		return $this;
	}

	public function send () {
		if ($this->message === false) {
			throw new \Exception('Unformed message.');
		}
		try{
     		$result = $this->client->sendEmail($this->message);
			return $result->get('MessageId');
		} catch (\Exception $e) {
     		$this->error = $e->getMessage();
     		return false;
		} 
	}
}