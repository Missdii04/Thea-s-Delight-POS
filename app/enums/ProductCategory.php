<?php

namespace App\Enums;

enum ProductCategory: string
{
    // -------------------------
    // Cake Categories
    // -------------------------
    case REFRIGERATED_CAKE = 'Refrigerated Cake';
    case CHOCOLATE_CAKE = 'Chocolate Cake';
    case CARAMEL_CAKE = 'Caramel Cake';
    case FILIPINO_CAKE = 'Filipino Cake';
    case SPECIALTY_CAKE = 'Specialty Cake';
    case FRUIT_VEGETABLE_CAKE = 'Fruit & Vegetable Cake';
    case COFFEE_CAKE = 'Coffee Cake';
    case CHEESE_CAKE = 'Cheese Cake';
    case MILK_CAKE = 'Milk Cake';

    // -------------------------
    // Accessory Categories
    // -------------------------
    case CAKE_TOPPER = 'Cake Topper';
    case CANDLES = 'Candles';
    case GREETING_CARD = 'Greeting Card';

    // ======================================================
    // CATEGORY LIST HELPERS
    // ======================================================

    /** Get all cake categories */
    public static function getCakeCategories(): array
    {
        return [
            self::REFRIGERATED_CAKE->value,
            self::CHOCOLATE_CAKE->value,
            self::CARAMEL_CAKE->value,
            self::FILIPINO_CAKE->value,
            self::SPECIALTY_CAKE->value,
            self::FRUIT_VEGETABLE_CAKE->value,
            self::COFFEE_CAKE->value,
            self::CHEESE_CAKE->value,
            self::MILK_CAKE->value,
        ];
    }

    /** Get all accessory categories */
    public static function getAccessoryCategories(): array
    {
        return [
            self::CAKE_TOPPER_WEDDING->value,
            self::CAKE_TOPPER_VALENTINE->value,
            self::CAKE_TOPPER_CHRISTENING->value,
            self::CAKE_TOPPER_BIRTHDAY->value,
            self::CAKE_TOPPER_CHRISTMAS->value,
            self::CAKE_TOPPER_ANNIVERSARY->value,
            self::CANDLES_REGULAR->value,
            self::CANDLES_NUMBER->value,
            self::CANDLES_SPARKLING->value,
            self::GREETING_CARD->value,
        ];
    }

    /** Get ALL categories (cake + accessory) */
    public static function getAllCategories(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** Useful for dropdowns: ["Refrigerated Cake" => "Refrigerated Cake", ...] */
    public static function getCategoryOptions(): array
    {
        $options = [];
        foreach (self::cases() as $category) {
            $options[$category->value] = $category->value;
        }
        return $options;
    }

    // ======================================================
    // CATEGORY CHECK HELPERS
    // ======================================================

    /** Check if a category belongs to Cake */
    public static function isCakeCategory(string $category): bool
    {
        return in_array($category, self::getCakeCategories());
    }

    /** Check if a category belongs to Accessories */
    public static function isAccessoryCategory(string $category): bool
    {
        return in_array($category, self::getAccessoryCategories());
    }

    // ======================================================
    // ENUM LOOKUP
    // ======================================================

    /** Convert a string value to an enum case (or null if invalid) */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }
}
