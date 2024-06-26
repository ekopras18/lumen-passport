<?php

namespace Ekopras18\LumenPassport\Tests;

use Ekopras18\LumenPassport\LumenPassport;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTest
 * @package Dusterio\LumenPassport\Tests
 */
class IntegrationTest extends TestCase
{

    /**
     * @test
     */
    public function testGlobalTokenTtlCanBesetViaLumenClass() {
        $now = Carbon::now();
        Carbon::setTestNow($now);
        $expiryDate = $now->clone()->addYear();
        LumenPassport::tokensExpireIn($expiryDate);
        $this->assertEquals(Passport::tokensExpireIn(), Carbon::now()->diff($expiryDate));
        $this->assertEquals(LumenPassport::tokensExpireIn(), Carbon::now()->diff($expiryDate));
    }

    /**
     * @test
     */
    public function testClientSpecificTokenTtlCanBeSetViaLumenClass()
    {
        $clientId = 2;
        $now = Carbon::now();
        Carbon::setTestNow($now);
        $clientExpiryDate = $now->clone()->addYears(5);
        $defaultGlobalExpiryDate = $now->clone()->addYears(1);

        LumenPassport::tokensExpireIn($clientExpiryDate, $clientId);
        $this->assertEquals(LumenPassport::tokensExpireIn(null, $clientId), Carbon::now()->diff($clientExpiryDate));

        # global TTL should still default to 1 year
        $this->assertEquals(LumenPassport::tokensExpireIn(), Carbon::now()->diff($defaultGlobalExpiryDate));
        $this->assertEquals(Passport::tokensExpireIn(), Carbon::now()->diff($defaultGlobalExpiryDate));
    }


}