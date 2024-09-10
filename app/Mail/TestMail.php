<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestMail extends Mailable
{
	use Queueable, SerializesModels;

	public $mailData;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($mailData)
	{
		$this->mailData = $mailData;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$email = $this->view('mail.test_mail')
			->subject($this->mailData['title']);

		// Verifica si existen archivos para adjuntar
		if (!empty($this->mailData['files']) && is_array($this->mailData['files'])) {
			foreach ($this->mailData['files'] as $file) {
				// Verifica que el archivo exista antes de adjuntarlo
				if (file_exists($file)) {
					$email->attach($file);
				}
			}
		}

		return $email;
	}
}
