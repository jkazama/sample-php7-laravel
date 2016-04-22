<?php
namespace App\Models\Account;

use App\Tests\EntityTestSupport;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoginTest extends EntityTestSupport
{
    protected function initialize()
    {
        Login::register($this->fixtures->loginArray('test'));
    }

    public function testRegister()
    {
        try {
            Login::loadByAccountId('new');
            $this->fail();
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
        Login::register([
            'id' => 'new', 'mail' => 'hoga@example.com', 'plainPassword' => 'password',
        ]);
        $m = Login::loadByAccountId('new');
        $this->assertEquals('new', $m->name);
        $this->assertTrue(password_verify('password', $m->password));
    }

    public function testChangePassword()
    {
        $m = Login::changePassword('test', 'changed');
        $this->assertTrue(password_verify('changed', $m->password));
    }

}
