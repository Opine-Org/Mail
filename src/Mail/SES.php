<?php
/**
 * virtuecenter\mail
 *
 * Copyright (c)2013 Ryan Mahoney, https://github.com/virtuecenter <ryan@virtuecenter.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Mail;
use Aws\Ses\SesClient;

class SES {
	private $config;
	private $client;
    private $message = false;

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