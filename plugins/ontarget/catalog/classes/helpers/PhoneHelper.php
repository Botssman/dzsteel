<?php namespace OnTarget\Catalog\Classes\Helpers;

use October\Rain\Exception\ValidationException;
use October\Rain\Support\Str;

class PhoneHelper
{
    public static function e164(string $phone, $defaultCountryCode = 7): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (starts_with($phone, '8')) {
            $phone = '+' . $defaultCountryCode . mb_substr($phone, 1);
        } else {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    public static function formatForHumans(string $phone, ?string $ext = null): string
    {
        // xxxxxxx1111
        $lastFour = substr($phone, -4);

        // xxxx111xxxx
        $firstTree = substr($phone, -7, 3);

        // x111xxxxxxx
        $operatorCode = substr($phone, -10, 3);

        // 1xxxxxxxxxx (может быть больше одной цифры)
        $countryCode = substr($phone, 0, -10);

        return "+$countryCode ($operatorCode) $firstTree-$lastFour" . ($ext ? " доб. $ext" : '');
    }

    public static function mask(string $phone): string
    {
        $end = substr($phone, -3);

        $start = substr($phone, 0, -8);

        $masked = $start . '*****' . $end;

        return self::formatForHumans($masked);
    }

    public static function clean(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (Str::startsWith($phone, '8')) {
            $phone = '7' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * @param string $phone
     * @param string $validationField
     *
     * @throws \October\Rain\Exception\ValidationException
     */
    public static function validatePhone(string $phone, string $validationField = 'phone'): void
    {
        $validation = validator(
            [$validationField => self::clean($phone)],
            [$validationField => self::getValidationRules()],
            self::getValidationMessages($validationField)
        );

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
    }

    /**
     * @return string[]
     */
    public static function getValidationRules(): array
    {
        return [
            'required',
            'numeric',
            'digits_between:11,14',
        ];
    }

    /**
     * @param string $fieldName
     *
     * @return string[]
     */
    public static function getValidationMessages(string $fieldName = 'phone'): array
    {
        return [
            $fieldName . '.required'       => 'Введите номер телефона',
            $fieldName . '.numeric'        => 'Введите корректный номер телефона',
            $fieldName . '.digits_between' => 'Введите корректный номер телефона',
        ];
    }
}
