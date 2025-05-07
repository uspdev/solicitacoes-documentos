<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitacaoDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public const rules = [
        'setor_id' => ['required', 'integer'],
        'tipoarquivo_id' => ['required', 'integer'],
    ];

    public const messages =  [
        'setor_id.required' => 'O setor é obrigatório!',
        'setor_id.numeric' => 'O setor é inválido!',
        'tipoarquivo_id.required' => 'O tipo do documento é obrigatório!',
        'tipoarquivo_id.numeric' => 'O tipo do documento é inválido!',
    ];
}
