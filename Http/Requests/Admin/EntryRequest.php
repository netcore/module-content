<?php

namespace Modules\Content\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Content\Rules\AreWidgetsValid;
use Netcore\Translator\Helpers\TransHelper;

class EntryRequest extends FormRequest
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
    public function rules()
    {
        $rules = [
            'widgets' => new AreWidgetsValid
        ];

        $languagesInSystem = TransHelper::getAllLanguages();
        foreach ($languagesInSystem as $language) {
            $rules['translations.' . $language->iso_code . '.title'] = 'required';
            $rules['translations.' . $language->iso_code . '.slug'] = 'required';
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];

        $languagesInSystem = TransHelper::getAllLanguages();
        foreach ($languagesInSystem as $language) {

            $titleMsg = 'Title is required';
            $slugMsg = 'Slug is required';

            if (count($languagesInSystem) > 1) {
                $titleMsg = 'Title (' . $language->iso_code . ') is required';
                $slugMsg = 'Slug (' . $language->iso_code . ') is required';
            }

            $messages['translations.' . $language->iso_code . '.title.required'] = $titleMsg;
            $messages['translations.' . $language->iso_code . '.slug.required'] = $slugMsg;
        }

        return $messages;
    }
}
