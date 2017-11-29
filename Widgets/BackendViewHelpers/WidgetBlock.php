<?php

namespace Modules\Content\Widgets\BackendViewHelpers;

class WidgetBlock
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

            $field = $model->fields->where('key', $fieldName)->first();
            $value = $field ? $field->value : '';

            $dataValue[$language->iso_code] = $value;

            $dataValue = json_encode($dataValue);
        }

        return $dataValue;
    }

    /**
     * @param $model
     * @param $fieldName
     * @param $fieldType
     * @return mixed
     */
    public function getValueForTd($model, $fieldName, $fieldType)
    {
        $field = $model->fields->where('key', $fieldName)->first();
        $value = $field ? $field->value : '';

        if($fieldType == 'checkbox') {
            $value = $value ? 'Yes' : 'No';
        }

        return $value;
    }
}
