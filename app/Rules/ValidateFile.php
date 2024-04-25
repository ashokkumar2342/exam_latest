<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateFile implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($array,$filename,$extension)
    {
        $this->extensionArr = $array;
        $this->filename = $filename;
        $this->extension = $extension;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $request)
    {
        $ext = implode(',',$this->extensionArr);
        if(in_array($this->extension,$this->extensionArr))
        {
            if(substr_count($this->filename, '.') == 1)
            {
                return true;
            }
            else
            {
                $this->message = 'The :attribute must have valid name!';
                return false;
            } 
        }
        else
        {
            $this->message = 'The :attribute must have extension from '.$ext;
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}