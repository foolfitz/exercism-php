<?php

/*
 * By adding type hints and enabling strict type checking, code can become
 * easier to read, self-documenting and reduce the number of potential bugs.
 * By default, type declarations are non-strict, which means they will attempt
 * to change the original type to match the type specified by the
 * type-declaration.
 *
 * In other words, if you pass a string to a function requiring a float,
 * it will attempt to convert the string value to a float.
 *
 * To enable strict mode, a single declare directive must be placed at the top
 * of the file.
 * This means that the strictness of typing is configured on a per-file basis.
 * This directive not only affects the type declarations of parameters, but also
 * a function's return type.
 *
 * For more info review the Concept on strict type checking in the PHP track
 * <link>.
 *
 * To disable strict typing, comment out the directive below.
 */

// This code is referenced from https://exercism.org/tracks/php/exercises/simple-cipher/solutions/ArranCrowley

declare(strict_types=1);

class SimpleCipher
{
    public string $key = '';
    private array $alphabet;
    
    public function __construct(string $key = null)
    {
        if ($key !== null && ! ctype_lower($key))
        {
            throw new InvalidArgumentException();
        }

        $this->alphabet = range('a', 'z');
        $key !== null ? $this->key = $key : array_map(
            fn() => $this->key .= $this->charIndexMapper(random_int(0, 25)),
            range(1, 100)
        );
    }

    public function encode(string $plainText): string
    {
        $encodeOperation = static function ($position, $keyPost){
            $value = $position + $keyPost;
            return $value >= 26 ? $value - 26 : $value;
        };
        return $this->encodeOrDecode($plainText, $encodeOperation);
    }

    public function decode(string $cipherText): string
    {
        $decodeOperation = static function ($position, $keyPost){
            $value = $position - $keyPost;
            return $value < 0 ? $value + 26 : $value;
        };
        return $this->encodeOrDecode($cipherText, $decodeOperation);
    }

    private function charIndexMapper($character)
    {
        if(is_int($character)) {
            return $this->alphabet[$character];
        }

        if(is_string($character) && ctype_alpha($character)){
            return array_keys($this->alphabet, $character)[0];
        }

        throw new InvalidArgumentException("Character must be a valid alphabet letter.");
    }

    private function encodeOrDecode(string $cipherText, callable $operation): string
    {
        $returnString = '';

        foreach(str_split($cipherText) as $keyPo => $character){
            $charPosition = $this->charIndexMapper($character);
            $keyPosition = $this->charIndexMapper($this->key[$keyPo]);
            $newPosition = $operation($charPosition, $keyPosition);
            $returnString .= $this->alphabet[$newPosition];
        }

        return $returnString;
    }
}
