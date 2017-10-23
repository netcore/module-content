<?php

namespace Modules\Content\Widgets\BackendViewHelpers;

class ImageBlock
{
    /**
     * @param $model
     * @param $fieldName
     * @param $languages
     * @return array|string
     */
    public function getDataValueForTd($model, $fieldName, $languages)
    {
        $dataValue = '';
        if($fieldName != 'image') {
            $dataValue = [];
            foreach($languages as $language) {

                $nonJsonFields = ['title', 'subtitle', 'content'];
                $isNonJsonField = in_array($fieldName, $nonJsonFields);

                if($isNonJsonField) {
                    $translation = trans_model($model, $language, $fieldName);
                } else {
                    $jsonString = trans_model($model, $language, 'json');
                    $jsonDecoded = (array) json_decode($jsonString);
                    $translation = array_get($jsonDecoded, $fieldName);
                }

                $dataValue[$language->iso_code] = $translation;
            }
            $dataValue = json_encode($dataValue);
        }

        return $dataValue;
    }

    /**
     * @param $model
     * @param $language
     * @param $fieldName
     * @return mixed
     */
    public function getValueForTd($model, $language, $fieldName)
    {
        $nonJsonFields = ['title', 'subtitle', 'content'];
        $isNonJsonField = in_array($fieldName, $nonJsonFields);

        if($isNonJsonField) {
            $value = trans_model($model, $language, $fieldName);
        } else {
            $jsonString = trans_model($model, $language, 'json');
            $jsonDecoded = (array) json_decode($jsonString);
            $value = array_get($jsonDecoded, $fieldName);
        }

        return $value;
    }
}
