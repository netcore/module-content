<?php

namespace Modules\Content\Widgets\BackendViewHelpers;

class ImageBlock
{
    /**
     * @param $model
     * @param $fieldName
     * @param $language
     * @return array|string
     */
    public function getDataValueForTd($model, $fieldName, $language)
    {
        $dataValue = '';
        if($fieldName != 'image') {
            $dataValue = [];

            $nonJsonFields = ['title', 'subtitle', 'content', 'link'];
            $isNonJsonField = in_array($fieldName, $nonJsonFields);

            if($isNonJsonField) {
                $translation = object_get($model, $fieldName);
            } else {
                $jsonDecoded = (array) $model->json;
                $translation = array_get($jsonDecoded, $fieldName);
            }

            $dataValue[$language->iso_code] = $translation;

            $dataValue = json_encode($dataValue);
        }

        return $dataValue;
    }

    /**
     * @param $model
     * @param $language
     * @param $fieldName
     * @param $fieldType
     * @return mixed
     */
    public function getValueForTd($model, $language, $fieldName, $fieldType)
    {
        $nonJsonFields = ['title', 'subtitle', 'content', 'link'];
        $isNonJsonField = in_array($fieldName, $nonJsonFields);

        if($isNonJsonField) {
            $value = object_get($model, $fieldName);
        } else {
            $jsonDecoded = (array) object_get($model, 'json');
            $value = array_get($jsonDecoded, $fieldName);
        }

        if($fieldType == 'checkbox') {
            $value = $value ? 'Yes' : 'No';
        }

        return $value;
    }
}
