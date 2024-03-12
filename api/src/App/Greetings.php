<?php

declare(strict_types=1);


namespace App;

class Greetings
{
    /** @return array<string, string> */
    public function getGreetings(): array
    {
        $greetings =  array("english"=>"hi", "spanish"=>"hola", "french"=>"bonjour");
        return $greetings;
    }

    /** @return array<string,string> */
    public function getGreeting(string $language): array
    {
        $greetings =  array("english"=>"hi", "spanish"=>"hola", "french"=>"bonjour");
        if(array_key_exists($language, $greetings))
        {
            return array($language => $greetings[$language]);
        }
        else
        {
            return array("message" => "no data");
        }
    }
}
