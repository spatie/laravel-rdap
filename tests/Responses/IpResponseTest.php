<?php
use Spatie\Rdap\Responses\IpResponse;

beforeEach(function(){
    $ipProperties = $this->getJsonStub("google-ip");
    $this->ipResponse = new IpResponse($ipProperties);
});

it("can get the registration date", function(){
    $registrationDate = $this->ipResponse->registrationDate();

    expect($registrationDate)->toBeDate("1997-09-15 04:00:00");
});

it("can get the expiration date", function(){
    $expirationDate = $this->ipResponse->expirationDate();

    expect($expirationDate)->toBeDate("2028-09-14 04:00:00");
});

it("can get the last changed date", function(){
    $expirationDate = $this->ipResponse->lastChangedDate();

    expect($expirationDate)->toBeDate("2019-09-09 15:39:04");
});

it("can get the last update of the rdap db date", function(){
    $expirationDate = $this->ipResponse->lastUpdateOfRdapDb();

    expect($expirationDate)->toBeDate("2022-05-13 10:21:22");
});

it("can get all properties", function(){
    $allProperties = $this->ipResponse->all();

    expect($allProperties)->toHaveCount(17);
});

it("can get a specific property", function(){
    expect($this->ipResponse->get("objectClassName"))->toBe("ip network");
    expect($this->ipResponse->get("links.0.value"))->toBe("https://rdap.arin.net/registry/ip/216.58.207.206");
});
