<?php
namespace App\Providers;

use Dingo\Api\Http\Validation\Domain;
use Illuminate\Http\Request;

class MultipleDomains extends Domain
{
    protected $domains;

    public function __construct($domains)
    {
        parent::__construct($domains[0]);
        $this->domains = $domains;
    }

    public function validate(Request $request)
    {
        foreach ($this->domains as $domain) {
            $matched = $request->getHost() === $this->stripPort($this->stripProtocol($domain));

            if ($matched) {
                return true;
            }
        }

        return false;
    }
}