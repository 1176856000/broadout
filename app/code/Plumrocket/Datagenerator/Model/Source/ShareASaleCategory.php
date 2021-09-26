<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Model\Source;

class ShareASaleCategory extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected static $categories;

    /**
     * @var array
     */
    protected static $subCategories;

    /**
     * @return array|null
     */
    public function getAllOptions()
    {
        if ($this->options === null) {
            $subCategories = $this->getSubCategories();
            $categories = $this->getCategories();
            $this->options[] = [
                'label' => __('Inherited From Parent Category'),
                'value' => 0,
            ];
            foreach ($subCategories as $categoryId => $subCategoriesArray) {
                $subCategory = [];
                foreach ($subCategoriesArray as $key => $name) {
                    $subCategory[] = [
                        'label' => __((string)$name),
                        'value' => (int)$key,
                    ];
                }
                $this->options[] = [
                    'label' => __((string)$categories[$categoryId]),
                    'value' => $subCategory,
                ];

            }
        }
        return $this->options;
    }

    /**
     * return list of Categories or Categories Ids
     * @param  boolean $onlyKeys
     * @return array
     */
    public static function getCategories($onlyKeys = false) // phpcs:ignore
    {
        if (!self::$categories) {
            $categorySource = '1: Art / Media / Performance
                2: Auto
                3: Books / Reading
                4: Business / Services
                5: Computer
                6: Electronics
                7: Entertainment
                8: Fashion
                9: Food / Beverage
                10: Gifts / Specialty
                11: Home / Family
                16: Metaphysical
                17: Parts / Equipment Subcategories
                12: Personal Care
                13: Sports / Outdoors
                14: Toys / Games
                15: Travel';

            preg_match_all('/(\d*): (.*)?/', $categorySource, $categoryArray);
            self::$categories = array_combine($categoryArray[1], $categoryArray[2]);
        }

        return $onlyKeys ? array_keys(self::$categories) : self::$categories;
    }

    /**
     * @return array
     */
    protected static function getSubCategories() // phpcs:ignore
    {
        if (!self::$subCategories) {
            $subCategorySource = '1: Art
                2: Photography
                3: Posters / Prints
                4: Music
                5: Music Instruments
                -------------
                6: Accessories
                7: Car Audio
                8: Cleaning / Care
                9: Motorcycles
                10: Misc.
                11: Repair
                12: Parts
                -------------
                13: Art
                14: Careers
                15: Business
                16: Childrens
                17: Computers
                18: Crafts
                19: Education
                20: Engineering
                21: Gifts
                22: Health
                23: History
                24: Fiction
                25: Law
                26: Magazines
                27: Financial
                28: Medical
                29: Office
                30: Real Estate
                31: Misc.
                164: Religious
                173: Science
                -------------
                32: Advertising
                33: Motivational
                34: Coupons / Freebies
                35: Financial
                36: Loans
                37: Office
                38: Careers
                39: Mis.
                179: Education
                -------------
                40: Hardware
                41: Software
                42: Instruction
                43: Handheld / Wireless
                162: Web Hosting
                -------------
                44: Audio
                45: Video
                46: Camera
                47: Wireless
                -------------
                48: Audio
                49: Video
                50: DVD
                51: Laser Disc
                52: Sheet Music
                53: Crafts / Hobbies
                -------------
                54: Boys
                55: Clearance
                56: Vintage
                57: Girls
                58: Mens
                59: Womens
                60: Maternity
                61: Footware
                62: Accessories
                63: Baby / Infant
                64: Jewelry
                65: Lingerie
                66: Plus-Size
                67: Athletic
                161: T-Shirts
                166: Big And Tall
                168: Petite
                169: Unisex
                172: Costumes
                -------------
                68: Baked Goods
                69: Beverages
                70: Chocolate
                71: Cheese / Condiments
                72: Coupons
                73: Diet
                74: Ethnic
                75: Gifts / Gift Baskets
                76: Nuts
                77: Cookies / Desserts
                78: Organic
                163: Tobacco
                176: Gourmet
                177: Meals / Complete Dishes
                180: Appetizers
                181: Soups
                -------------
                79: Anniversary
                80: Birthday
                81: Misc. Holiday
                82: Collectibles
                83: Coupons
                84: Executive Gifts
                85: Flowers
                86: Baskets
                87: Greeting Card
                88: Baby / Infant
                89: Party
                90: Religious
                91: Sympathy
                92: Valentine\'s Day
                93: Wedding
                170: Personalized
                -------------
                94: Bed/Bath
                95: Garden
                96: Cleaning / Care
                97: Furniture
                98: Home Decor
                99: Home Improvement
                100: Kitchen
                101: Pets
                -------------
                160: Metaphysical
                -------------
                167: HVAC (Heating and Air Conditioning)
                171: Medical
                182: Military
                -------------
                102: Cosmetics
                103: Exercise / Wellness
                104: Safety
                183: Medical
                -------------
                105: Accessories
                106: Auto
                107: Outdoors / Camping
                108: Parlor / Backyard Games
                109: Baseball / Softball
                110: Cricket
                111: Billiards
                112: Boating
                113: Body Building / Fitness
                114: Bowling
                115: Boxing
                116: Canoeing
                117: Climbing / Mountaineering
                118: Cycling
                119: Diving
                120: Field Hockey
                121: Skating
                122: Fishing
                123: Football
                124: Frisbee
                125: Golf
                126: Gymnastics
                127: Hockey
                128: Horses
                129: Hunting
                130: In-line Skating
                131: Kayaking
                132: Lacrosse
                133: Martial Arts
                134: Racquetball
                135: Running
                136: Skateboards
                137: Ski/Snowboard
                138: Soccer
                139: Surfing
                140: Tennis
                141: Teamware / Logo
                142: Volleyball
                143: Wrestling
                165: Birding
                174: Prospecting / Treasure Hunting
                175: Swimming
                178: Basketball
                -------------
                144: Action
                145: Animals
                146: Baby / Infant
                147: Board Games
                148: Card / Casino
                149: Electronic
                150: Educational
                151: Magic
                152: Misc.
                153: Musical
                154: Outdoor
                155: Video
                -------------
                156: Coupons
                157: Maps
                158: References / Guides
                159: Vacation Travel';

            $arrayOfSubCategories =  explode('-------------', $subCategorySource);
            $subCatByCatId = array_combine(self::getCategories(true), $arrayOfSubCategories);

            foreach ($subCatByCatId as $categoryId => $subCategoriesString) {
                preg_match_all('/(\d*): (.*)?/', $subCategoriesString, $subCategoryArray);
                self::$subCategories[$categoryId] = array_combine($subCategoryArray[1], $subCategoryArray[2]);
            }
        }

        return self::$subCategories;
    }

    /**
     * @param $categoryId
     * @return int|string
     */
    public static function getCategoryIdBySubcategory($categoryId) // phpcs:ignore
    {
        if (!self::$subCategories) {
            self::getSubCategories();
        }

        foreach (self::$subCategories as $catId => $subCategoryArray) {
            if ($index = array_search($categoryId, array_keys($subCategoryArray)) !== false) {
                return $catId;
            }
        }
        return '0';
    }
}
