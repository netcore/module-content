<?php

namespace Modules\Content\Rules;

use Illuminate\Contracts\Validation\Rule;

class AreWidgetsValid implements Rule
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $widgets = json_decode($value);
        $widgets = (array)array_map(function ($widget) {
            return (array)$widget;
        }, $widgets);

        foreach ($widgets as $widget) {

            $key = array_get($widget, 'widget');
            $config = collect(config('netcore.module-content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');

            if ($backendWorker) {
                $backendWorker = new $backendWorker($config);
                foreach ($backendWorker->getErrors($widget) as $key => $error) {
                    $this->errors[$key] = $error;
                }
            }
        }

        return $this->errors ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errors;
    }
}
