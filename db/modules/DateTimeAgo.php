<?php
class DateTimeAgo {
    private static $intervals = [
        'year'   => 31536000, 
        'month'  => 2592000,  
        'day'    => 86400,    
        'hour'   => 3600,     
        'minute' => 60,       
        'second' => 1
    ];

    private static $declensions = [
        'year'   => ['год', 'года', 'лет'],
        'month'  => ['месяц', 'месяца', 'месяцев'],
        'day'    => ['день', 'дня', 'дней'],
        'hour'   => ['час', 'часа', 'часов'],
        'minute' => ['минута', 'минуты', 'минут'],
        'second' => ['секунда', 'секунды', 'секунд'],
    ];

    private static $cases = [
        'nominative' => 0, // именительный
        'genitive'   => 1, // родительный
        'dative'     => 2, // дательный
        'accusative' => 0, // винительный
        'instrumental' => 1, // творительный
        'prepositional' => 2 // предложный
    ];

    /**
     * Возвращает строку с указанием, сколько времени прошло с указанной даты.
     *
     * @param int $timestamp Временная метка Unix.
     * @return string
     */
    public static function format(int $timestamp): string {
        $currentTime = time();
        $timeDifference = $currentTime - $timestamp;

        if ($timeDifference < 0) {
            return 'В будущем';
        }

        foreach (self::$intervals as $unit => $seconds) {
            $interval = floor($timeDifference / $seconds);

            if ($interval >= 1) {
                $declension = self::getDeclension($interval, self::$declensions[$unit]);
                return "$interval $declension";
            }
        }

        return 'Только что';
    }

    /**
     * Возвращает строку с указанием, сколько времени прошло с указанной даты, с учетом падежа.
     *
     * @param int $timestamp Временная метка Unix.
     * @param string $case Падеж ('nominative', 'genitive', 'dative', 'accusative', 'instrumental', 'prepositional').
     * @return string
     */
    public static function formatWithCase(int $timestamp, string $case = 'nominative'): string {
        $currentTime = time();
        $timeDifference = $currentTime - $timestamp;

        if ($timeDifference < 0) {
            return 'В будущем';
        }

        foreach (self::$intervals as $unit => $seconds) {
            $interval = floor($timeDifference / $seconds);

            if ($interval >= 1) {
                $declension = self::getDeclensionWithCase($interval, self::$declensions[$unit], $case);
                return "$interval $declension";
            }
        }

        return 'Только что';
    }

    /**
     * Возвращает правильное склонение слова в зависимости от числа.
     *
     * @param int $number Число.
     * @param array $declensions Массив склонений (например, ['год', 'года', 'лет']).
     * @return string
     */
    private static function getDeclension(int $number, array $declensions): string {
        $cases = [2, 0, 1, 1, 1, 2];
        return $declensions[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * Возвращает правильное склонение слова в зависимости от числа и падежа.
     *
     * @param int $number Число.
     * @param array $declensions Массив склонений (например, ['год', 'года', 'лет']).
     * @param string $case Падеж ('nominative', 'genitive', 'dative', 'accusative', 'instrumental', 'prepositional').
     * @return string
     */
    private static function getDeclensionWithCase(int $number, array $declensions, string $case): string {
        $caseIndex = self::$cases[$case] ?? 0; // Получаем индекс падежа
        $cases = [2, 0, 1, 1, 1, 2]; // Правила выбора формы слова в зависимости от числа
        $index = ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]; // Определяем индекс формы слова
    
        // Возвращаем форму слова в зависимости от падежа
        return $declensions[$caseIndex];
    }
}