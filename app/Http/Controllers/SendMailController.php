<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestMail;
use Mail;

class SendMailController extends Controller
{
	public function sendMailWithAttachment(Request $request)
	{
		// Laravel 8
		// $data["email"] = "test@gmail.com";
		// $data["title"] = "Techsolutionstuff";
		// $data["body"] = "This is test mail with attachment";

		// $files = [
		//     public_path('attachments/test_image.jpeg'),
		//     public_path('attachments/test_pdf.pdf'),
		// ];

		// Mail::send('mail.test_mail', $data, function($message)use($data, $files) {
		//     $message->to($data["email"])
		//             ->subject($data["title"]);

		//     foreach ($files as $file){
		//         $message->attach($file);
		//     }            
		// });
		$mailData = [
			'title' => 'This is Test Mail',
			#'files' => [
			#	public_path('attachments/test_image.jpg'),
			#	public_path('attachments/test_pdf.pdf'),
			#],
		];
		#dd($mailData);
		Mail::to('omarliberatto1967@gmail.com')->send(new TestMail($mailData));

		echo "Mail send successfully 3 !!";
	}



	public function send_email_example()
	{
		$config = array(
			'protocol' => 'tls',
			'smtp_host' => 'mail.alephmanager.com',
			'smtp_port' => 465,
			'smtp_user' => 'info@alephmanager.com',
			'smtp_pass' => 'hustle2006',
			'smtp_timeout' => '4',
			'mailtype'  => 'html',
			'charset'   => 'utf-8',
			'wordwrap' => TRUE
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");

		$this->email->from('info@alephmanager.com', 'Aleph Manager');
		$this->email->to('adrianclaret@gmail.com');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		if ($this->email->send()) {
			//TODO: load view...
			echo "email sent";
		} else {
			$to = $this->input->post('email');
			mail($to, 'test', 'Other sent option failed');
			echo $this->input->post('email');
			show_error($this->email->print_debugger());
		}
	}
}
