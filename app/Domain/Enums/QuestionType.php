<?php

namespace App\Domain\Enums;

enum QuestionType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case CHECKBOX = 'checkbox';
    case DROPDOWN = 'dropdown';
    case PHOTO = 'photo';

    public function label(): string
    {
        $ar = app()->getLocale() === 'ar';
        return match($this) {
            self::TEXT => $ar ? 'نص' : 'Text Input',
            self::NUMBER => $ar ? 'رقم' : 'Number Input',
            self::CHECKBOX => $ar ? 'مربع اختيار' : 'Checkbox (Yes/No)',
            self::DROPDOWN => $ar ? 'قائمة منسدلة' : 'Dropdown Select',
            self::PHOTO => $ar ? 'صورة' : 'Photo Upload',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TEXT => 'text',
            self::NUMBER => 'number',
            self::CHECKBOX => 'checkbox',
            self::DROPDOWN => 'dropdown',
            self::PHOTO => 'photo',
        };
    }
}