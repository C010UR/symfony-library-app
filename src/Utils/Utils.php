<?php

namespace App\Utils;

class Utils
{
    /**
     * @var string
     */
    final public const ENCODING = 'UTF-8';

    public static function ucwords(string $string): string
    {
        return mb_convert_case($string, MB_CASE_TITLE, self::ENCODING);
    }

    public static function uclower(string $string): string
    {
        return mb_convert_case($string, MB_CASE_LOWER, self::ENCODING);
    }

    public static function getLoremIpsum(int $paragraphs = 5): string
    {
        $lorem =
            'Клиент очень важен, за клиентом последует клиент. Инвестиции в инвестиции в недвижимость.
            А вот члены терапии или шоколад время. Сосиски и салат кладут, а грузовики кланяются.
            Я не думаю, что это большая жизнь для моих детей. Полностью или заранее, он не хочет хорошо
            проводить время. Аккумуляторы в машины не ставить. Дети беременных. Дети живут с болезнями,
            старость и дети, и они страдают от голода и нищеты. Я теперь свободен, я не буду бояться жизни
            с колчаном, меня не пощадят стрелы. Важно позаботиться о самом больном, а за ним будет следить
            адиписцирующий эль';

        return implode("\n\n", array_fill(0, $paragraphs, $lorem));
    }
}
