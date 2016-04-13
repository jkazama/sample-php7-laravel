<?php
namespace App\Tests;

use App\Context\Actor\ActorSession;
use App\Context\DomainHelper;
use App\Context\Timestamper;
use App\Models\BusinessDayHandler;
use App\Models\DataFixtures;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EntityTestSupport extends TestCase
{
    use WithoutMiddleware;
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected $time;
    protected $businessDay;
    protected $dh;
    protected $fixtures;

    public function setUp()
    {
        parent::setUp();
        $this->time = new Timestamper();
        $this->businessDay = new BusinessDayHandler($this->time);
        $this->dh = new DomainHelper();
        $this->dh->actorSession = ActorSession::mock();
        $this->fixtures = new DataFixtures($this->time);
        $this->initialize();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->terminate();
    }

    protected function initialize()
    {
        //
    }

    protected function terminate()
    {
        //
    }

}
