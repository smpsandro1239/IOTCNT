<?php

namespace Appttpequests;

use IlluminateoundationttpormRequest;
use Illuminateontractsalidationalidator;
use IlluminatettpxceptionsttpResponseException;

class ESP32DataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Permitir apenas usuários autenticados
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'device_id' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_-]+$/'
            ],
            'temperature' => [
                'required',
                'numeric',
                'min:-50',
                'max:150'
            ],
            'humidity' => [
                'required',
                'numeric',
                'min:0',
                'max:100'
            ],
            'pressure' => [
                'required',
                'numeric',
                'min:800',
                'max:1200'
            ],
            'valve_status' => [
                'required',
                'boolean'
            ],
            'timestamp' => [
                'required',
                'date',
                'before:now',
                'after:2020-01-01'
            ],
            'location' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9,.-]+$/'
            ],
            'battery_level' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'signal_strength' => [
                'nullable',
                'numeric',
                'min:-120',
                'max:0'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'device_id.required' => 'O ID do dispositivo é obrigatório.',
            'device_id.string' => 'O ID do dispositivo deve ser uma string.',
            'device_id.max' => 'O ID do dispositivo não pode ter mais de 255 caracteres.',
            'device_id.regex' => 'O ID do dispositivo contém caracteres inválidos.',
            'temperature.required' => 'A temperatura é obrigatória.',
            'temperature.numeric' => 'A temperatura deve ser um número.',
            'temperature.min' => 'A temperatura não pode ser menor que -50°C.',
            'temperature.max' => 'A temperatura não pode ser maior que 150°C.',
            'humidity.required' => 'A umidade é obrigatória.',
            'humidity.numeric' => 'A umidade deve ser um número.',
            'humidity.min' => 'A umidade não pode ser menor que 0%.',
            'humidity.max' => 'A umidade não pode ser maior que 100%.',
            'pressure.required' => 'A pressão é obrigatória.',
            'pressure.numeric' => 'A pressão deve ser um número.',
            'pressure.min' => 'A pressão não pode ser menor que 800 hPa.',
            'pressure.max' => 'A pressão não pode ser maior que 1200 hPa.',
            'valve_status.required' => 'O status da válvula é obrigatório.',
            'valve_status.boolean' => 'O status da válvula deve ser verdadeiro ou falso.',
            'timestamp.required' => 'O timestamp é obrigatório.',
            'timestamp.date' => 'O timestamp deve ser uma data válida.',
            'timestamp.before' => 'O timestamp não pode ser no futuro.',
            'timestamp.after' => 'O timestamp deve ser após 01/01/2020.',
            'location.string' => 'A localização deve ser uma string.',
            'location.max' => 'A localização não pode ter mais de 255 caracteres.',
            'location.regex' => 'A localização contém caracteres inválidos.',
            'battery_level.numeric' => 'O nível da bateria deve ser um número.',
            'battery_level.min' => 'O nível da bateria não pode ser menor que 0%.',
            'battery_level.max' => 'O nível da bateria não pode ser maior que 100%.',
            'signal_strength.numeric' => 'A força do sinal deve ser um número.',
            'signal_strength.min' => 'A força do sinal não pode ser menor que -120 dBm.',
            'signal_strength.max' => 'A força do sinal não pode ser maior que 0 dBm.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'device_id' => 'ID do Dispositivo',
            'temperature' => 'Temperatura',
            'humidity' => 'Umidade',
            'pressure' => 'Pressão',
            'valve_status' => 'Status da Válvula',
            'timestamp' => 'Timestamp',
            'location' => 'Localização',
            'battery_level' => 'Nível da Bateria',
            'signal_strength' => 'Força do Sinal'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  lluminateontractsalidationalidator  $validator
     * @return void
     *
     * @throws lluminatettpxceptionsttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validação falhou',
            'errors' => $validator->errors(),
            'timestamp' => now()->toISOString()
        ], 422));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Sanitizar dados de entrada
        $this->merge([
            'device_id' => trim($this->input('device_id', '')),
            'location' => trim($this->input('location', ''))
        ]);
    }
}
