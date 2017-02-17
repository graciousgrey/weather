<?php

namespace Weather\Client;

use GuzzleHttp\Client; //durch use kann man unten nur noch Client statt GuzzleHttp\Client schreiben
use TestTools\Fixture\SelfInitializingFixtureTrait;
use Weather\Exception\Exception;
//use oben nur damit man nicht ganzen namespace (Pfad) tippen muss unten
class RestClient
{
    use SelfInitializingFixtureTrait; //use hier heißt, dass ich trait(inheritance ohne polymorphismus) verwende

    private $client; // Encapsulation

    public function __construct(Client $httpClient) //Dependency Injection - Client ist Guzzle kommt von Depency Injection Container
    {
        $this->client = $httpClient; // Variable $this-> client wird auf Client von guzzle gesetzt, durch this Klassenscope, also this-> client meint jetzt immer guzzle Client
        $this->_fixtureInstance = 'self';// per default wird parent class genommen
    }

    protected function getHttpClient() { // gut, wenn Klasenkind auf client zugreifen können muss, weil wenn client geändert wird nur hier
        //äAnderung nötig, und nicht in allen Kindklassen
        return $this->client;
    }

    protected function getRequest(string $url)
    {
        $response = $this->getHttpClient()->request('GET', $url);//request ist guzzle Funktion

        if($response->getStatusCode() >= 300) { //getStatusCode von guzzle
            throw new Exception('Request to ' . $url . ' was not successful: HTTP status code ' . $response->getStatusCode());
        }

        $result = json_decode($response->getBody(), true);//getBody von guzzle

        if($result === false){
            throw new Exception('Request to ' . $url . ' was not successful: Response is not valid JSON.');
        }

        return $result;
    }

    public function request(string $url)
    {
        return $this->callWithFixtures('getRequest', func_get_args());//wird nur als fixture gespeichert und verwendet wenn fixturemodus aktiv,
        // also nur beim testen, sonst Funktion wie oben
        // ändere ich in der testdatei die anfrage sind 2 fixtures gespeichert
    }
}
//durch private client - habe ich die Freiheit die Funktionen zu ändern, z.B. nicht mehr guzzle zu verwenden, weil ja nicht
// guzzle Daten weitergegeben werden sondern nur die Funktionen genutzt werden können. Daten ändern sich oft, aber Methodennamen
// bleiben trotzdem gleich, also müssen auch wenn sich Daten und Funktionsinhalt ändern die Leute die den Code nutzen ihren Code
// nicht umschreiben.