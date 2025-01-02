<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'username' => 'required|string',
			'email' => 'required|string',
			'nombre' => 'required|string',
			'apellido' => 'required|string',
			'habilitado' => 'required|boolean',
			'bloqueado' => 'required|boolean',
		];
	}

	public function validationData()
    {
		$formData = [];
		foreach ($this->input('form_data') as $input) {
			$formData[$input['name']] = $input['value'];
		}
        return $formData;
    }
}
