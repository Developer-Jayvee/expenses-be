<?php

namespace App\Enums;

enum BillCategoryEnum: string
{
    case HOUSING = 'housing';
    case FOOD_DINING = 'food_dining';
    case TRANSPORTATION = 'transportation';
    case UTILITIES = 'utilities';
    case SHOPPING = 'shopping';
    case HEALTHCARE = 'healthcare';
    case EDUCATION = 'education';
    case ENTERTAINMENT = 'entertainment';
    case FINANCIAL = 'financial';
    case FAMILY = 'family';
    case TRAVEL = 'travel';
    case PETS = 'pets';
    case GIFTS_DONATIONS = 'gifts_donations';
    case WORK_BUSINESS = 'work_business';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::HOUSING => 'Housing',
            self::FOOD_DINING => 'Food & Dining',
            self::TRANSPORTATION => 'Transportation',
            self::UTILITIES => 'Utilities',
            self::SHOPPING => 'Shopping',
            self::HEALTHCARE => 'Healthcare',
            self::EDUCATION => 'Education',
            self::ENTERTAINMENT => 'Entertainment',
            self::FINANCIAL => 'Financial',
            self::FAMILY => 'Family',
            self::TRAVEL => 'Travel',
            self::PETS => 'Pets',
            self::GIFTS_DONATIONS => 'Gifts & Donations',
            self::WORK_BUSINESS => 'Work & Business',
            self::OTHER => 'Other',
        };
    }
}